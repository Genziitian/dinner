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
                        $expected = substr(hash('sha256', $user['password_hash'] . env('APP_SECRET')), 0, 16);
                        if (hash_equals($expected, $secretHash)) {
                            return [
                                'id' => (int)$user['id'],
                                'username' => $user['username'],
                                'role' => $user['role'],
                                'restaurant_id' => $user['restaurant_id'] ? (int)$user['restaurant_id'] : null,
                                'restaurant_name' => $user['restaurant_name'] ?? '',
                                'restaurant_timezone' => $user['restaurant_timezone'] ?? 'Asia/Kolkata',
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
        $secretHash = substr(hash('sha256', $user['password_hash'] . env('APP_SECRET')), 0, 16);
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
        if ($user['role'] === User::ROLE_CASHIER) {
            $this->jsonError('Unauthorized. Only managers can browse order history.', 403);
        }

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
            $this->jsonSuccess(['item' => $item], 'Item created successfully.', 201);
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

        $this->jsonSuccess(['item' => $updated], 'Item status updated.');
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
        if (!$order) {
            $this->jsonError('Receipt not found or invalid token.', 404);
        }

        $this->jsonSuccess([
            'order' => $order,
            'receipt_token' => $token,
            'public_receipt_url' => url('receipt/' . $token),
        ]);
    }
}
