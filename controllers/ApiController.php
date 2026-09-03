<?php
/**
 * REST API Controller
 * DinePOS - Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/models/Database.php';
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/models/Restaurant.php';
require_once ROOT_PATH . '/models/Item.php';
require_once ROOT_PATH . '/models/ItemVariant.php';
require_once ROOT_PATH . '/models/Order.php';
require_once ROOT_PATH . '/models/OrderItem.php';
require_once ROOT_PATH . '/models/DailyCounter.php';
require_once ROOT_PATH . '/models/RateLimiter.php';
require_once ROOT_PATH . '/models/AuditLog.php';

class ApiController {
    /**
     * Parse Authorization Header or Session to authenticate API user
     */
    private function authenticate(): array {
        startSecureSession();

        // 1. Check Session
        if (auth_check()) {
            $user = current_user();
            if ($user) {
                return $user;
            }
        }

        // 2. Check Bearer Token (Base64-encoded or API Session Header)
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['HTTP_X_API_TOKEN'] ?? '';
        if (str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
            // Decode token format: base64(user_id:username:hash_prefix)
            $decoded = base64_decode($token, true);
            if ($decoded && str_contains($decoded, ':')) {
                $parts = explode(':', $decoded);
                if (count($parts) >= 3) {
                    $userId = (int)$parts[0];
                    $username = $parts[1];
                    $secretHash = $parts[2];
                    $user = User::findById($userId);
                    if ($user && $user['username'] === $username && $user['status'] === 'active') {
                        $secret = (string)env('APP_SECRET', 'dinepos-default-app-secret-change-in-prod');
                        $expected = substr(hash('sha256', $user['password_hash'] . $secret), 0, 16);
                        if (hash_equals($expected, $secretHash)) {
                            return [
                                'id' => (int)$user['id'],
                                'username' => $user['username'],
                                'role' => $user['role'],
                                'restaurant_id' => $user['restaurant_id'] ? (int)$user['restaurant_id'] : null,
                                'restaurant_name' => $user['restaurant_name'] ?? '',
                                'restaurant_timezone' => $user['restaurant_timezone'] ?? 'Asia/Kolkata',
                                'shop_type' => $user['shop_type'] ?? 'restaurant',
                            ];
                        }
                    }
                }
            }
        }

        $this->jsonError('Unauthorized. Please provide a valid authentication token.', 401);
        exit;
    }

    /**
     * Generate Auth Token for User
     */
    private function generateToken(array $user): string {
        $secret = (string)env('APP_SECRET', 'dinepos-default-app-secret-change-in-prod');
        $secretHash = substr(hash('sha256', $user['password_hash'] . $secret), 0, 16);
        return base64_encode($user['id'] . ':' . $user['username'] . ':' . $secretHash);
    }

    private function getJsonInput(): array {
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        return $_POST;
    }

    private function jsonSuccess(array $data = [], string $message = 'Success', int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-API-Token');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    private function jsonError(string $message = 'Error', int $statusCode = 400, array $errors = []): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-API-Token');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * POST /api/v1/auth/login
     */
    public function login(): void {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $input = $this->getJsonInput();
        $username = strtolower(trim((string)($input['username'] ?? '')));
        $password = (string)($input['password'] ?? '');

        // 1. Rate Limiting Check
        if (RateLimiter::isRateLimited($ip, $username)) {
            $minutes = (int)env('LOGIN_LOCKOUT_MINUTES', 15);
            $this->jsonError("Too many failed attempts. Account/IP locked for {$minutes} minutes.", 429);
        }

        if (empty($username) || empty($password)) {
            $this->jsonError('Username and password are required.', 422);
        }

        // 2. Lookup User
        $user = User::findByUsername($username);
        if (!$user) {
            RateLimiter::recordAttempt($ip, $username);
            AuditLog::log(AuditLog::ACTION_FAILED_LOGIN, 'user', null, null, null, ['username' => $username, 'source' => 'api']);
            $this->jsonError('Invalid username or password.', 401);
        }

        // 3. Lockout Check
        if (User::isLocked($user)) {
            $this->jsonError('Account is temporarily locked due to multiple failed login attempts.', 403);
        }

        if ($user['status'] !== 'active') {
            $this->jsonError('Your account has been deactivated.', 403);
        }

        // 4. Check Restaurant Status
        if ($user['role'] !== User::ROLE_SUPERADMIN && (!empty($user['restaurant_status']) && $user['restaurant_status'] !== 'active')) {
            $this->jsonError('Restaurant account is currently inactive.', 403);
        }

        // 5. Verify Password
        if (!password_verify($password, $user['password_hash'])) {
            RateLimiter::recordAttempt($ip, $username);
            User::recordFailedLogin((int)$user['id']);
            AuditLog::log(AuditLog::ACTION_FAILED_LOGIN, 'user', (int)$user['id'], $user['restaurant_id'] ? (int)$user['restaurant_id'] : null, null, ['username' => $username, 'source' => 'api']);
            $remaining = RateLimiter::getRemainingAttempts($ip, $username);
            $this->jsonError("Invalid username or password. {$remaining} attempt(s) remaining.", 401);
        }

        // 6. Success: Clear rate limiter
        RateLimiter::clearAttempts($ip, $username);
        User::resetFailedLogins((int)$user['id']);

        $token = $this->generateToken($user);

        // Fetch Restaurant info if present
        $restaurant = null;
        if (!empty($user['restaurant_id'])) {
            $restaurant = Restaurant::findById((int)$user['restaurant_id']);
        }

        AuditLog::log(AuditLog::ACTION_LOGIN, 'user', (int)$user['id'], $user['restaurant_id'] ? (int)$user['restaurant_id'] : null, (int)$user['id'], ['source' => 'api']);

        $this->jsonSuccess([
            'token' => $token,
            'user' => [
                'id' => (int)$user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'restaurant_id' => $user['restaurant_id'] ? (int)$user['restaurant_id'] : null,
            ],
            'restaurant' => $restaurant ? [
                'id' => (int)$restaurant['id'],
                'name' => $restaurant['name'],
                'phone' => $restaurant['phone'] ?? '',
                'address' => $restaurant['address'] ?? '',
                'timezone' => $restaurant['timezone'] ?? 'Asia/Kolkata',
                'shop_type' => $restaurant['shop_type'] ?? 'restaurant',
            ] : null,
        ], 'Login successful.');
    }

    /**
     * GET /api/v1/auth/me
     */
    public function me(): void {
        $user = $this->authenticate();
        $restaurant = null;
        if (!empty($user['restaurant_id'])) {
            $restaurant = Restaurant::findById((int)$user['restaurant_id']);
        }

        $this->jsonSuccess([
            'user' => $user,
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * GET /api/v1/items
     */
    public function getItems(): void {
        $user = $this->authenticate();
        $restaurantId = (int)($user['restaurant_id'] ?? 0);
        if ($restaurantId <= 0) {
            $this->jsonError('User not associated with a restaurant.', 403);
        }

        $activeOnly = isset($_GET['all']) && $_GET['all'] === '1' ? false : true;
        if ($user['role'] === User::ROLE_CASHIER) {
            $activeOnly = true;
        }

        $items = Item::allByRestaurant($restaurantId, $activeOnly);

        // Also fetch preview next order number
        $restaurant = Restaurant::findById($restaurantId);
        $tz = new DateTimeZone($restaurant['timezone'] ?? 'Asia/Kolkata');
        $todayDate = (new DateTime('now', $tz))->format('Y-m-d');
        $previewOrderNumber = DailyCounter::previewNextOrderNumber($restaurantId, $todayDate);

        $this->jsonSuccess([
            'items' => $items,
            'preview_order_number' => $previewOrderNumber,
            'today_date' => $todayDate,
        ]);
    }

    /**
     * POST /api/v1/orders
     */
    public function createOrder(): void {
        $user = $this->authenticate();
        $restaurantId = (int)($user['restaurant_id'] ?? 0);
        if ($restaurantId <= 0) {
            $this->jsonError('User not associated with a restaurant.', 403);
        }

        $input = $this->getJsonInput();
        $customerName = !empty($input['customer_name']) ? trim((string)$input['customer_name']) : null;
        $customerPhone = !empty($input['customer_phone']) ? trim((string)$input['customer_phone']) : null;
        $paymentMethod = (string)($input['payment_method'] ?? Order::PAYMENT_CASH);
        $items = $input['items'] ?? [];

        if (empty($items) || !is_array($items)) {
            $this->jsonError('Order must contain at least one item.', 422);
        }

        try {
            $result = Order::create(
                $restaurantId,
                $user['id'],
                $customerName,
                $customerPhone,
                $paymentMethod,
                $items
            );

            // Fetch the full created order
            $order = Order::findById((int)$result['order_id'], $restaurantId);

            $this->jsonSuccess([
                'order' => $order,
                'receipt_token' => $result['receipt_token'],
                'public_receipt_url' => url('receipt/' . $result['receipt_token']),
            ], "Order #{$result['order_number']} created successfully.", 201);
        } catch (Throwable $e) {
            $this->jsonError('Failed to create order: ' . $e->getMessage(), 400);
        }
    }

    /**
     * GET /api/v1/orders
     */
    public function getOrders(): void {
        $user = $this->authenticate();

        $restaurantId = (int)($user['restaurant_id'] ?? 0);
        $filter = $_GET['filter'] ?? 'today';
        $search = trim($_GET['search'] ?? '');
        $paymentMethod = $_GET['payment_method'] ?? '';

        $restaurant = Restaurant::findById($restaurantId);
        $tz = new DateTimeZone($restaurant['timezone'] ?? 'Asia/Kolkata');
        $now = new DateTime('now', $tz);

        $filters = [
            'search' => $search,
            'payment_method' => $paymentMethod,
        ];

        switch ($filter) {
            case 'today':
                $filters['date'] = $now->format('Y-m-d');
                break;
            case 'yesterday':
                $filters['date'] = (clone $now)->modify('-1 day')->format('Y-m-d');
                break;
            case '7days':
                $filters['start_date'] = (clone $now)->modify('-6 days')->format('Y-m-d');
                $filters['end_date'] = $now->format('Y-m-d');
                break;
            case 'this_month':
                $filters['start_date'] = $now->format('Y-m-01');
                $filters['end_date'] = $now->format('Y-m-t');
                break;
            case 'last_month':
                $lastMonth = (clone $now)->modify('-1 month');
                $filters['start_date'] = $lastMonth->format('Y-m-01');
                $filters['end_date'] = $lastMonth->format('Y-m-t');
                break;
            case 'custom':
                if (!empty($_GET['start_date'])) $filters['start_date'] = $_GET['start_date'];
                if (!empty($_GET['end_date'])) $filters['end_date'] = $_GET['end_date'];
                break;
        }

        $orders = Order::getOrdersList($restaurantId, $filters, 150);

        $this->jsonSuccess([
            'orders' => $orders,
            'filter' => $filter,
        ]);
    }

    /**
     * GET /api/v1/orders/{id}
     */
    public function getOrder(int $orderId): void {
        $user = $this->authenticate();
        $restaurantId = (int)($user['restaurant_id'] ?? 0);

        $order = Order::findById($orderId, $restaurantId);
        if (!$order) {
            $this->jsonError('Order not found.', 404);
        }

        $this->jsonSuccess(['order' => $order]);
    }

    /**
     * GET /api/v1/cashier/summary
     */
    public function getCashierSummary(): void {
        $user = $this->authenticate();
        $restaurantId = (int)($user['restaurant_id'] ?? 0);
        if ($restaurantId <= 0) {
            $this->jsonError('User not associated with a restaurant.', 403);
        }

        $restaurant = Restaurant::findById($restaurantId);
        $tz = new DateTimeZone($restaurant['timezone'] ?? 'Asia/Kolkata');
        $todayDate = (new DateTime('now', $tz))->format('Y-m-d');

        $stats = Order::getCashierSummary($restaurantId, $todayDate);

        $this->jsonSuccess([
            'stats' => $stats,
            'today_date' => $todayDate,
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * GET /api/v1/manager/dashboard
     */
    public function getManagerDashboard(): void {
        $user = $this->authenticate();
        if ($user['role'] === User::ROLE_CASHIER) {
            $this->jsonError('Unauthorized. Only managers can view the dashboard.', 403);
        }

        $restaurantId = (int)($user['restaurant_id'] ?? 0);
        $restaurant = Restaurant::findById($restaurantId);

        if (($restaurant['shop_type'] ?? '') === 'mill') {
            require_once ROOT_PATH . '/models/MillOrder.php';
            $tz = new DateTimeZone($restaurant['timezone'] ?? 'Asia/Kolkata');
            $today = (new DateTime('now', $tz))->format('Y-m-d');
            $millSummary = MillOrder::getDailySummary($restaurantId, $today);
            $millRecent = MillOrder::allByRestaurant($restaurantId, 'all', null, 10);
            
            $todayStats = [
                'total_sales' => (float)$millSummary['total_amount'],
                'cash_sales' => (float)$millSummary['paid_amount'],
                'online_sales' => 0.0,
                'total_orders' => (int)$millSummary['total_orders'],
                'unpaid_amount' => (float)$millSummary['unpaid_amount'],
                'total_weight_kg' => (float)$millSummary['total_weight_kg'],
                'pending_orders' => (int)$millSummary['pending_orders'],
                'ready_orders' => (int)$millSummary['ready_orders'],
                'delivered_orders' => (int)$millSummary['delivered_orders'],
            ];

            $this->jsonSuccess([
                'stats' => $todayStats,
                'recent_orders' => $millRecent,
                'restaurant' => $restaurant,
            ]);
            return;
        }

        $todayStats = Order::getStats($restaurantId, 'today');
        $recentOrders = Order::getOrdersList($restaurantId, [], 10);

        $this->jsonSuccess([
            'stats' => $todayStats,
            'recent_orders' => $recentOrders,
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * GET /api/v1/manager/reports
     */
    public function getReports(): void {
        $user = $this->authenticate();
        if ($user['role'] === User::ROLE_CASHIER) {
            $this->jsonError('Unauthorized. Only managers can view reports.', 403);
        }

        $restaurantId = (int)($user['restaurant_id'] ?? 0);
        $period = $_GET['period'] ?? 'today';
        $customStart = $_GET['start_date'] ?? null;
        $customEnd = $_GET['end_date'] ?? null;

        $stats = Order::getStats($restaurantId, $period, $customStart, $customEnd);
        $today = Order::getStats($restaurantId, 'today');
        $yesterday = Order::getStats($restaurantId, 'yesterday');
        $thisMonth = Order::getStats($restaurantId, 'this_month');
        $lastMonth = Order::getStats($restaurantId, 'last_month');
        $sixMonths = Order::getStats($restaurantId, '6months');
        $year = Order::getStats($restaurantId, 'year');

        $this->jsonSuccess([
            'current_period' => $period,
            'stats' => $stats,
            'comparison' => [
                'today' => $today,
                'yesterday' => $yesterday,
                'this_month' => $thisMonth,
                'last_month' => $lastMonth,
                'six_months' => $sixMonths,
                'year' => $year,
            ],
        ]);
    }

    /**
     * POST /api/v1/items
     */
    public function createItem(): void {
        $user = $this->authenticate();
        if ($user['role'] === User::ROLE_CASHIER) {
            $this->jsonError('Unauthorized. Only managers can manage items.', 403);
        }

        $restaurantId = (int)($user['restaurant_id'] ?? 0);
        $input = $this->getJsonInput();

        $name = trim((string)($input['name'] ?? ''));
        $itemType = (string)($input['item_type'] ?? Item::TYPE_PIECE);
        $baseUnit = trim((string)($input['base_unit'] ?? 'piece'));
        $active = (int)($input['active'] ?? 1);
        $variants = $input['variants'] ?? [];

        if (empty($name)) {
            $this->jsonError('Item name is required.', 422);
        }

        if (empty($variants) || !is_array($variants)) {
            $this->jsonError('At least one pricing variant is required.', 422);
        }

        try {
            $itemId = Item::create(
                $restaurantId,
                [
                    'name' => $name,
                    'item_type' => $itemType,
                    'base_unit' => $baseUnit,
                    'active' => $active,
                ],
                $variants
            );

            AuditLog::log(AuditLog::ACTION_ITEM_CREATE, 'item', $itemId, $restaurantId, $user['id'], ['name' => $name]);

            $item = Item::findById($itemId, $restaurantId);
            $this->jsonSuccess($item, 'Item created successfully.', 201);
        } catch (Throwable $e) {
            $this->jsonError('Failed to create item: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/items/{id}/toggle
     */
    public function toggleItem(int $itemId): void {
        $user = $this->authenticate();
        if ($user['role'] === User::ROLE_CASHIER) {
            $this->jsonError('Unauthorized.', 403);
        }

        $restaurantId = (int)($user['restaurant_id'] ?? 0);
        $item = Item::findById($itemId, $restaurantId);
        if (!$item) {
            $this->jsonError('Item not found.', 404);
        }

        Item::toggleActive($itemId, $restaurantId);
        $updated = Item::findById($itemId, $restaurantId);

        $this->jsonSuccess($updated, 'Item status updated.');
    }

    /**
     * GET /api/v1/receipt/{token}
     * Public cryptographic receipt lookup (unauthenticated or mobile app)
     */
    public function getReceipt(string $token): void {
        $token = trim($token);
        if (empty($token)) {
            $this->jsonError('Receipt token is required.', 400);
        }

        $order = Order::findByReceiptToken($token);
        $this->jsonSuccess([
            'order' => $order,
            'receipt_token' => $token,
            'public_receipt_url' => url('receipt/' . $token),
        ]);
    }

    /**
     * GET /api/v1/admin/overview
     * Super Admin Multi-Tenant System Overview
     */
    public function getAdminOverview(): void {
        $user = $this->authenticate();
        if ($user['role'] !== User::ROLE_SUPERADMIN) {
            $this->jsonError('Unauthorized. Only Super Admin can access this.', 403);
        }

        $restaurants = Restaurant::all();
        $users = User::all();

        $this->jsonSuccess([
            'restaurants' => $restaurants,
            'total_restaurants' => count($restaurants),
            'total_users' => count($users),
            'users' => $users,
        ]);
    }

    /**
     * POST /api/v1/admin/restaurants
     */
    public function createRestaurant(): void {
        $user = $this->authenticate();
        if ($user['role'] !== User::ROLE_SUPERADMIN) {
            $this->jsonError('Unauthorized.', 403);
        }

        $input = $this->getJsonInput();
        $name = trim((string)($input['name'] ?? ''));
        $phone = trim((string)($input['phone'] ?? ''));
        $address = trim((string)($input['address'] ?? ''));
        $timezone = trim((string)($input['timezone'] ?? 'Asia/Kolkata'));
        $status = (string)($input['status'] ?? 'active');

        if (empty($name)) {
            $this->jsonError('Restaurant name is required.', 422);
        }

        try {
            $id = Restaurant::create([
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'timezone' => $timezone,
                'status' => $status,
            ]);

            AuditLog::log(AuditLog::ACTION_SETTINGS_UPDATE, 'restaurant', $id, $id, $user['id'], ['name' => $name]);
            $created = Restaurant::findById($id);
            $this->jsonSuccess($created, "Restaurant '{$name}' created successfully.", 201);
        } catch (Throwable $e) {
            $this->jsonError('Failed to create restaurant: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/admin/restaurants/{id}
     */
    public function updateRestaurant(int $id): void {
        $user = $this->authenticate();
        if ($user['role'] !== User::ROLE_SUPERADMIN) {
            $this->jsonError('Unauthorized.', 403);
        }

        $restaurant = Restaurant::findById($id);
        if (!$restaurant) {
            $this->jsonError('Restaurant not found.', 404);
        }

        $input = $this->getJsonInput();
        $name = trim((string)($input['name'] ?? $restaurant['name']));
        $phone = trim((string)($input['phone'] ?? $restaurant['phone']));
        $address = trim((string)($input['address'] ?? $restaurant['address']));
        $timezone = trim((string)($input['timezone'] ?? $restaurant['timezone']));
        $status = (string)($input['status'] ?? $restaurant['status']);

        if (empty($name)) {
            $this->jsonError('Restaurant name is required.', 422);
        }

        try {
            Restaurant::update($id, [
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'timezone' => $timezone,
                'status' => $status,
            ]);

            AuditLog::log(AuditLog::ACTION_SETTINGS_UPDATE, 'restaurant', $id, $id, $user['id'], ['name' => $name, 'status' => $status]);
            $updated = Restaurant::findById($id);
            $this->jsonSuccess($updated, "Restaurant updated successfully.");
        } catch (Throwable $e) {
            $this->jsonError('Failed to update restaurant: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/admin/restaurants/{id}/toggle
     */
    public function toggleRestaurant(int $id): void {
        $user = $this->authenticate();
        if ($user['role'] !== User::ROLE_SUPERADMIN) {
            $this->jsonError('Unauthorized.', 403);
        }

        $restaurant = Restaurant::findById($id);
        if (!$restaurant) {
            $this->jsonError('Restaurant not found.', 404);
        }

        Restaurant::toggleStatus($id);
        $updated = Restaurant::findById($id);
        $this->jsonSuccess($updated, "Restaurant status updated.");
    }

    /**
     * POST /api/v1/admin/users
     */
    public function createUser(): void {
        $user = $this->authenticate();
        if ($user['role'] !== User::ROLE_SUPERADMIN) {
            $this->jsonError('Unauthorized.', 403);
        }

        $input = $this->getJsonInput();
        $username = strtolower(trim((string)($input['username'] ?? '')));
        $password = (string)($input['password'] ?? '');
        $role = (string)($input['role'] ?? User::ROLE_CASHIER);
        $restaurantId = !empty($input['restaurant_id']) ? (int)$input['restaurant_id'] : null;
        $status = (string)($input['status'] ?? 'active');

        if ($role !== User::ROLE_SUPERADMIN && empty($restaurantId)) {
            $this->jsonError('Restaurant is required for manager and cashier roles.', 422);
        }

        try {
            $newUserId = User::create([
                'restaurant_id' => $restaurantId,
                'username' => $username,
                'password' => $password,
                'role' => $role,
                'status' => $status,
            ]);

            AuditLog::log(AuditLog::ACTION_USER_CREATE, 'user', $newUserId, $restaurantId, $user['id'], ['username' => $username, 'role' => $role]);
            $created = User::findById($newUserId);
            $this->jsonSuccess($created, "User '{$username}' created successfully.", 201);
        } catch (Throwable $e) {
            $this->jsonError('Failed to create user: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/admin/users/{id}/toggle
     */
    public function toggleUser(int $id): void {
        $user = $this->authenticate();
        if ($user['role'] !== User::ROLE_SUPERADMIN) {
            $this->jsonError('Unauthorized.', 403);
        }

        $targetUser = User::findById($id);
        if (!$targetUser) {
            $this->jsonError('User not found.', 404);
        }

        if ($targetUser['id'] == $user['id']) {
            $this->jsonError('Cannot deactivate your own superadmin account.', 422);
        }

        User::toggleStatus($id);
        $updated = User::findById($id);
        $this->jsonSuccess($updated, "User status updated.");
    }

    /**
     * GET /api/v1/manager/staff
     */
    public function getManagerStaff(): void {
        $user = $this->authenticate();
        if ($user['role'] !== User::ROLE_MANAGER && $user['role'] !== User::ROLE_SUPERADMIN) {
            $this->jsonError('Unauthorized. Manager only.', 403);
        }

        $restaurantId = $user['restaurant_id'];
        if (!$restaurantId) {
            $this->jsonError('Restaurant ID not assigned.', 400);
        }

        $staff = User::allByRestaurant((int)$restaurantId);
        $this->jsonSuccess(['staff' => $staff]);
    }

    /**
     * POST /api/v1/manager/staff
     */
    public function createManagerStaff(): void {
        $user = $this->authenticate();
        if ($user['role'] !== User::ROLE_MANAGER && $user['role'] !== User::ROLE_SUPERADMIN) {
            $this->jsonError('Unauthorized. Manager only.', 403);
        }

        $restaurantId = $user['restaurant_id'];
        if (!$restaurantId) {
            $this->jsonError('Restaurant ID not assigned.', 400);
        }

        $input = $this->getJsonInput();
        $username = strtolower(trim((string)($input['username'] ?? '')));
        $password = (string)($input['password'] ?? '');
        $confirmPassword = (string)($input['confirm_password'] ?? $password);

        if (empty($username) || empty($password)) {
            $this->jsonError('Username and password are required.', 422);
        }

        if ($password !== $confirmPassword) {
            $this->jsonError('Password confirmation does not match.', 422);
        }

        try {
            // Manager can ONLY create Cashier accounts
            $newUserId = User::create([
                'restaurant_id' => (int)$restaurantId,
                'username' => $username,
                'password' => $password,
                'role' => User::ROLE_CASHIER,
                'status' => 'active',
            ]);

            AuditLog::log(AuditLog::ACTION_USER_CREATE, 'user', $newUserId, (int)$restaurantId, $user['id'], [
                'username' => $username,
                'role' => User::ROLE_CASHIER,
                'created_by_manager' => $user['username']
            ]);

            $created = User::findById($newUserId);
            $this->jsonSuccess($created, "Cashier '{$username}' created successfully.", 201);
        } catch (Throwable $e) {
            $this->jsonError('Failed to create staff: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/manager/staff/{id}/toggle
     */
    public function toggleManagerStaff(int $id): void {
        $user = $this->authenticate();
        if ($user['role'] !== User::ROLE_MANAGER && $user['role'] !== User::ROLE_SUPERADMIN) {
            $this->jsonError('Unauthorized. Manager only.', 403);
        }

        $restaurantId = $user['restaurant_id'];
        $targetUser = User::findById($id);

        if (!$targetUser || (int)$targetUser['restaurant_id'] !== (int)$restaurantId) {
            $this->jsonError('Staff user not found in your restaurant.', 404);
        }

        if ($targetUser['role'] !== User::ROLE_CASHIER) {
            $this->jsonError('Managers can only toggle cashier accounts.', 403);
        }

        User::toggleStatus($id);
        $updated = User::findById($id);
        $this->jsonSuccess($updated, "Staff status updated.");
    }

    /**
     * GET /api/v1/manager/export/data
     */
    public function getExportData(): void {
        $user = $this->authenticate();
        if ($user['role'] !== User::ROLE_MANAGER && $user['role'] !== User::ROLE_SUPERADMIN) {
            $this->jsonError('Unauthorized. Manager only.', 403);
        }

        $restaurantId = (int)$user['restaurant_id'];
        $restaurant = Restaurant::findById($restaurantId);
        $tz = new DateTimeZone($restaurant['timezone'] ?? 'Asia/Kolkata');
        $now = new DateTime('now', $tz);

        $exportType = $_GET['type'] ?? 'daily';
        $startDate = $now->format('Y-m-d');
        $endDate = $now->format('Y-m-d');

        if ($exportType === 'daily') {
            $date = $_GET['date'] ?? $now->format('Y-m-d');
            $startDate = $date;
            $endDate = $date;
        } elseif ($exportType === 'monthly') {
            $month = $_GET['month'] ?? $now->format('Y-m');
            $dt = DateTime::createFromFormat('Y-m', $month, $tz) ?: $now;
            $startDate = $dt->format('Y-m-01');
            $endDate = $dt->format('Y-m-t');
        } elseif ($exportType === 'custom') {
            $startDate = $_GET['start_date'] ?? $now->format('Y-m-d');
            $endDate = $_GET['end_date'] ?? $now->format('Y-m-d');
        }

        $stats = Order::getStats($restaurantId, 'custom', $startDate, $endDate);
        $orders = Order::getOrdersForExport($restaurantId, $startDate, $endDate);

        $this->jsonSuccess([
            'restaurant_name' => $restaurant['name'] ?? '',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'stats' => $stats,
            'orders' => $orders,
            'download_url' => url("exports/download?type={$exportType}&start_date={$startDate}&end_date={$endDate}&date={$startDate}&month=" . substr($startDate, 0, 7)),
        ]);
    }

    /**
     * POST /api/v1/admin/users/{id}
     * Super Admin edits any user (Manager or Cashier), changes role, restaurant assignment, or resets password.
     */
    public function updateAdminUser(int $id): void {
        $user = $this->authenticate();
        if ($user['role'] !== User::ROLE_SUPERADMIN) {
            $this->jsonError('Unauthorized. Super Admin only.', 403);
        }

        $targetUser = User::findById($id);
        if (!$targetUser) {
            $this->jsonError('User not found.', 404);
        }

        $input = $this->getJsonInput();
        $username = strtolower(trim((string)($input['username'] ?? $targetUser['username'])));
        $role = (string)($input['role'] ?? $targetUser['role']);
        $restaurantId = !empty($input['restaurant_id']) ? (int)$input['restaurant_id'] : ($targetUser['restaurant_id'] ? (int)$targetUser['restaurant_id'] : null);
        $status = (string)($input['status'] ?? $targetUser['status']);
        $password = (string)($input['password'] ?? '');

        if ($role !== User::ROLE_SUPERADMIN && empty($restaurantId)) {
            $this->jsonError('Restaurant is required for manager and cashier roles.', 422);
        }

        try {
            $updateData = [
                'role' => $role,
                'status' => $status,
            ];
            if (!empty($password)) {
                $updateData['password'] = $password;
            }

            // Update user record
            User::update($id, $updateData);

            // Update username and restaurant_id if changed
            Database::execute(
                "UPDATE users SET username = :username, restaurant_id = :restaurant_id WHERE id = :id",
                [
                    ':username' => $username,
                    ':restaurant_id' => $restaurantId,
                    ':id' => $id,
                ]
            );

            AuditLog::log(AuditLog::ACTION_USER_UPDATE, 'user', $id, $restaurantId, $user['id'], [
                'username' => $username,
                'role' => $role,
                'status' => $status,
                'updated_by' => 'superadmin'
            ]);

            $updated = User::findById($id);
            $this->jsonSuccess($updated, "User updated successfully.");
        } catch (Throwable $e) {
            $this->jsonError('Failed to update user: ' . $e->getMessage(), 400);
        }
    }

    /**
     * POST /api/v1/manager/staff/{id}
     * Manager updates Cashier staff (change username or reset cashier password with double confirmation).
     */
    public function updateManagerStaff(int $id): void {
        $user = $this->authenticate();
        if ($user['role'] !== User::ROLE_MANAGER && $user['role'] !== User::ROLE_SUPERADMIN) {
            $this->jsonError('Unauthorized. Manager only.', 403);
        }

        $restaurantId = (int)$user['restaurant_id'];
        $targetUser = User::findById($id);

        if (!$targetUser || (int)$targetUser['restaurant_id'] !== $restaurantId) {
            $this->jsonError('Staff user not found in your restaurant.', 404);
        }

        if ($targetUser['role'] !== User::ROLE_CASHIER) {
            $this->jsonError('Managers can only modify Cashier accounts.', 403);
        }

        $input = $this->getJsonInput();
        $username = strtolower(trim((string)($input['username'] ?? $targetUser['username'])));
        $password = (string)($input['password'] ?? '');
        $confirmPassword = (string)($input['confirm_password'] ?? '');
        $status = (string)($input['status'] ?? $targetUser['status']);

        if (!empty($password)) {
            if ($password !== $confirmPassword) {
                $this->jsonError('Password confirmation does not match.', 422);
            }
            if (strlen($password) < 8) {
                $this->jsonError('Password must be at least 8 characters.', 422);
            }
        }

        try {
            $updateData = ['status' => $status];
            if (!empty($password)) {
                $updateData['password'] = $password;
            }
            User::update($id, $updateData);

            Database::execute(
                "UPDATE users SET username = :username WHERE id = :id",
                [':username' => $username, ':id' => $id]
            );

            AuditLog::log(AuditLog::ACTION_USER_UPDATE, 'user', $id, $restaurantId, $user['id'], [
                'username' => $username,
                'status' => $status,
                'password_reset' => !empty($password),
                'updated_by_manager' => $user['username']
            ]);

            $updated = User::findById($id);
            $this->jsonSuccess($updated, "Cashier staff updated successfully.");
        } catch (Throwable $e) {
            $this->jsonError('Failed to update cashier: ' . $e->getMessage(), 400);
        }
    }

    // ==========================================
    // MILL API ENDPOINTS
    // ==========================================

    /**
     * GET /api/v1/mill/services
     */
    public function getMillServices(): void {
        $user = $this->authenticate();
        $restaurantId = (int)($user['restaurant_id'] ?? 0);
        require_once ROOT_PATH . '/models/MillService.php';
        $services = MillService::allByRestaurant($restaurantId, false);
        $this->jsonSuccess($services);
    }

    /**
     * POST /api/v1/mill/services
     */
    public function saveMillService(): void {
        $user = $this->authenticate();
        $restaurantId = (int)($user['restaurant_id'] ?? 0);
        require_once ROOT_PATH . '/models/MillService.php';
        $input = $this->getJsonInput();

        $name = trim((string)($input['name'] ?? ''));
        $nameHi = trim((string)($input['name_hi'] ?? ''));
        $rate = (float)($input['rate_per_kg'] ?? 0);
        $active = isset($input['active']) ? (int)$input['active'] : 1;
        $id = isset($input['id']) ? (int)$input['id'] : 0;

        if (empty($name)) {
            $this->jsonError('Service name is required.', 422);
        }
        if ($rate <= 0) {
            $this->jsonError('Rate per KG must be greater than zero.', 422);
        }

        if ($id > 0) {
            MillService::update($id, [
                'name' => $name,
                'name_hi' => $nameHi,
                'rate_per_kg' => $rate,
                'active' => $active,
            ]);
            $service = MillService::findById($id);
            $this->jsonSuccess($service, 'Service updated successfully.');
        } else {
            $newId = MillService::create([
                'restaurant_id' => $restaurantId,
                'name' => $name,
                'name_hi' => $nameHi,
                'rate_per_kg' => $rate,
                'active' => $active,
            ]);
            $service = MillService::findById($newId);
            $this->jsonSuccess($service, 'Service created successfully.');
        }
    }

    /**
     * POST /api/v1/mill/services/{id}/toggle
     */
    public function toggleMillService(int $id): void {
        $user = $this->authenticate();
        require_once ROOT_PATH . '/models/MillService.php';
        $service = MillService::findById($id);
        if (!$service) {
            $this->jsonError('Service not found.', 404);
        }
        MillService::toggleActive($id);
        $updated = MillService::findById($id);
        $this->jsonSuccess($updated, 'Service status updated.');
    }

    /**
     * GET /api/v1/mill/orders
     */
    public function getMillOrders(): void {
        $user = $this->authenticate();
        $restaurantId = (int)($user['restaurant_id'] ?? 0);
        require_once ROOT_PATH . '/models/MillOrder.php';

        $status = $_GET['status'] ?? 'all';
        $search = $_GET['search'] ?? null;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

        $orders = MillOrder::allByRestaurant($restaurantId, $status, $search, $limit, $offset);
        $this->jsonSuccess($orders);
    }

    /**
     * POST /api/v1/mill/orders
     */
    public function createMillOrder(): void {
        $user = $this->authenticate();
        $restaurantId = (int)($user['restaurant_id'] ?? 0);
        require_once ROOT_PATH . '/models/MillOrder.php';
        $input = $this->getJsonInput();

        $customerName = trim((string)($input['customer_name'] ?? ''));
        $customerPhone = trim((string)($input['customer_phone'] ?? ''));
        $serviceId = isset($input['service_id']) ? (int)$input['service_id'] : null;
        $serviceName = trim((string)($input['service_name'] ?? 'Grinding'));
        $weightKg = (float)($input['weight_kg'] ?? 0);
        $ratePerKg = (float)($input['rate_per_kg'] ?? 0);
        $paymentStatus = (string)($input['payment_status'] ?? 'unpaid');
        $paymentMethod = (string)($input['payment_method'] ?? 'cash');
        $notes = trim((string)($input['notes'] ?? ''));

        if (empty($customerName)) {
            $this->jsonError('Customer name is required.', 422);
        }
        if (empty($customerPhone)) {
            $this->jsonError('Customer phone is required.', 422);
        }
        if ($weightKg <= 0) {
            $this->jsonError('Weight in KG must be greater than zero.', 422);
        }
        if ($ratePerKg <= 0) {
            $this->jsonError('Rate per KG must be greater than zero.', 422);
        }

        $orderId = MillOrder::create([
            'restaurant_id' => $restaurantId,
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'service_id' => $serviceId,
            'service_name' => $serviceName,
            'weight_kg' => $weightKg,
            'rate_per_kg' => $ratePerKg,
            'payment_status' => $paymentStatus,
            'payment_method' => $paymentMethod,
            'notes' => $notes,
        ]);

        $order = MillOrder::findById($orderId);
        $this->jsonSuccess($order, 'Order created successfully.');
    }

    /**
     * POST /api/v1/mill/orders/{id}/status
     */
    public function updateMillOrderStatus(int $id): void {
        $user = $this->authenticate();
        require_once ROOT_PATH . '/models/MillOrder.php';
        $order = MillOrder::findById($id);
        if (!$order) {
            $this->jsonError('Order not found.', 404);
        }

        $input = $this->getJsonInput();
        if (isset($input['status'])) {
            MillOrder::updateStatus($id, (string)$input['status']);
        }
        if (isset($input['payment_status'])) {
            $method = (string)($input['payment_method'] ?? 'cash');
            MillOrder::updatePaymentStatus($id, (string)$input['payment_status'], $method);
        }

        $updated = MillOrder::findById($id);
        $this->jsonSuccess($updated, 'Order updated successfully.');
    }

    /**
     * GET /api/v1/mill/customers
     */
    public function getMillCustomers(): void {
        $user = $this->authenticate();
        $restaurantId = (int)($user['restaurant_id'] ?? 0);
        require_once ROOT_PATH . '/models/MillCustomer.php';
        $search = (string)($_GET['search'] ?? '');
        $customers = MillCustomer::allByRestaurant($restaurantId, $search);
        $this->jsonSuccess($customers);
    }

    /**
     * GET /api/v1/mill/customers/search
     */
    public function searchMillCustomers(): void {
        $user = $this->authenticate();
        $restaurantId = (int)($user['restaurant_id'] ?? 0);
        require_once ROOT_PATH . '/models/MillCustomer.php';
        $query = (string)($_GET['q'] ?? '');
        $results = MillCustomer::search($restaurantId, $query);
        $this->jsonSuccess($results);
    }
}
