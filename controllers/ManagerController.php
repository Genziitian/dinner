<?php
/**
 * Manager Controller
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once ROOT_PATH . '/models/Order.php';
require_once ROOT_PATH . '/models/Item.php';
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/models/Restaurant.php';
require_once ROOT_PATH . '/models/AuditLog.php';

class ManagerController extends BaseController {
    /**
     * Manager Dashboard
     */
    public function dashboard(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $restaurant = Restaurant::findById($restaurantId);
        $todayStats = Order::getStats($restaurantId, 'today');
        $recentOrders = Order::getOrdersList($restaurantId, [], 10);

        $this->render('manager/dashboard', [
            'title' => 'Manager Dashboard | DinePOS',
            'restaurant' => $restaurant,
            'stats' => $todayStats,
            'recentOrders' => $recentOrders,
        ]);
    }

    /**
     * Manager Order History with Filters
     */
    public function orders(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $filter = $_GET['filter'] ?? 'today';
        $search = trim($_GET['search'] ?? '');
        $paymentMethod = $_GET['payment_method'] ?? '';
        $customStart = $_GET['start_date'] ?? '';
        $customEnd = $_GET['end_date'] ?? '';

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
                if (!empty($customStart)) $filters['start_date'] = $customStart;
                if (!empty($customEnd)) $filters['end_date'] = $customEnd;
                break;
            case 'all':
            default:
                break;
        }

        $orders = Order::getOrdersList($restaurantId, $filters, 100);

        $this->render('manager/orders', [
            'title' => 'Order History | DinePOS',
            'orders' => $orders,
            'currentFilter' => $filter,
            'search' => $search,
            'paymentMethod' => $paymentMethod,
            'customStart' => $customStart,
            'customEnd' => $customEnd,
        ]);
    }

    /**
     * Manager View Single Order Details
     */
    public function orderView(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $orderId = (int)($_GET['id'] ?? 0);
        $order = Order::findById($orderId, $restaurantId);

        if (!$order) {
            $this->redirect('/manager/orders', 'danger', 'Order not found or inaccessible.');
        }

        $this->render('manager/order_view', [
            'title' => "Order #{$order['order_number']} Details | DinePOS",
            'order' => $order,
        ]);
    }

    /**
     * Aggregated Financial & Sales Reports
     */
    public function reports(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $period = $_GET['period'] ?? 'today';
        $customStart = $_GET['start_date'] ?? null;
        $customEnd = $_GET['end_date'] ?? null;

        $stats = Order::getStats($restaurantId, $period, $customStart, $customEnd);

        // Also fetch comparison stats for quick insights
        $today = Order::getStats($restaurantId, 'today');
        $yesterday = Order::getStats($restaurantId, 'yesterday');
        $thisMonth = Order::getStats($restaurantId, 'this_month');
        $lastMonth = Order::getStats($restaurantId, 'last_month');
        $sixMonths = Order::getStats($restaurantId, '6months');
        $year = Order::getStats($restaurantId, 'year');

        $this->render('manager/reports', [
            'title' => 'Financial Reports | DinePOS',
            'currentPeriod' => $period,
            'stats' => $stats,
            'summary' => [
                'today' => $today,
                'yesterday' => $yesterday,
                'this_month' => $thisMonth,
                'last_month' => $lastMonth,
                'six_months' => $sixMonths,
                'year' => $year,
            ],
            'customStart' => $customStart,
            'customEnd' => $customEnd,
        ]);
    }

    /**
     * Restaurant Staff / User Management
     */
    public function users(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $users = User::allByRestaurant($restaurantId);

        $this->render('manager/users', [
            'title' => 'Staff Management | DinePOS',
            'users' => $users,
        ]);
    }

    /**
     * Create user form
     */
    public function userCreate(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $this->render('manager/user_form', [
            'title' => 'Add Staff Member | DinePOS',
            'userObj' => null,
            'isEdit' => false,
        ]);
    }

    /**
     * Store new user
     */
    public function userStore(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $this->validateCsrf();
        $restaurantId = $this->requireRestaurantId();

        $username = strtolower(trim((string)($_POST['username'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        $role = (string)($_POST['role'] ?? User::ROLE_CASHIER);
        $status = (string)($_POST['status'] ?? 'active');

        // Managers cannot create superadmins
        if ($user['role'] !== User::ROLE_SUPERADMIN && $role === User::ROLE_SUPERADMIN) {
            $role = User::ROLE_CASHIER;
        }

        try {
            $newUserId = User::create([
                'restaurant_id' => $restaurantId,
                'username' => $username,
                'password' => $password,
                'role' => $role,
                'status' => $status,
            ]);

            AuditLog::log(
                AuditLog::ACTION_USER_CREATE,
                'user',
                $newUserId,
                $restaurantId,
                $user['id'],
                ['username' => $username, 'role' => $role]
            );

            $this->redirect('/manager/users', 'success', "User '{$username}' created successfully.");
        } catch (Throwable $e) {
            $this->redirect('/manager/users/create', 'danger', $e->getMessage());
        }
    }

    /**
     * Edit staff user form
     */
    public function userEdit(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $editId = (int)($_GET['id'] ?? 0);
        $userObj = User::findById($editId);

        if (!$userObj || (int)$userObj['restaurant_id'] !== $restaurantId) {
            $this->redirect('/manager/users', 'danger', 'User not found or inaccessible.');
        }

        $this->render('manager/user_form', [
            'title' => "Edit Staff - {$userObj['username']} | DinePOS",
            'userObj' => $userObj,
            'isEdit' => true,
        ]);
    }

    /**
     * Update staff user
     */
    public function userUpdate(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $this->validateCsrf();
        $restaurantId = $this->requireRestaurantId();

        $editId = (int)($_POST['id'] ?? 0);
        $userObj = User::findById($editId);

        if (!$userObj || (int)$userObj['restaurant_id'] !== $restaurantId) {
            $this->redirect('/manager/users', 'danger', 'User not found or inaccessible.');
        }

        $role = (string)($_POST['role'] ?? $userObj['role']);
        $status = (string)($_POST['status'] ?? $userObj['status']);
        $password = (string)($_POST['password'] ?? '');

        try {
            User::update($editId, [
                'role' => $role,
                'status' => $status,
                'password' => $password !== '' ? $password : null,
            ]);

            AuditLog::log(
                AuditLog::ACTION_USER_EDIT,
                'user',
                $editId,
                $restaurantId,
                $user['id'],
                ['username' => $userObj['username'], 'status' => $status]
            );

            $this->redirect('/manager/users', 'success', "User '{$userObj['username']}' updated successfully.");
        } catch (Throwable $e) {
            $this->redirect("/manager/users/edit?id={$editId}", 'danger', $e->getMessage());
        }
    }

    /**
     * Audit Trail View
     */
    public function audit(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $logs = AuditLog::getByRestaurant($restaurantId, 150);

        $this->render('manager/audit', [
            'title' => 'Audit Trail | DinePOS',
            'logs' => $logs,
        ]);
    }

    /**
     * Restaurant Settings
     */
    public function settings(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $restaurant = Restaurant::findById($restaurantId);

        $this->render('manager/settings', [
            'title' => 'Restaurant Settings | DinePOS',
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * Update Restaurant Settings (Super Admin Only)
     */
    public function updateSettings(): void {
        $user = $this->requireRole(User::ROLE_SUPERADMIN);
        $this->validateCsrf();
        $restaurantId = $this->requireRestaurantId();

        $name = trim((string)($_POST['name'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));
        $address = trim((string)($_POST['address'] ?? ''));
        $timezone = trim((string)($_POST['timezone'] ?? 'Asia/Kolkata'));

        if (empty($name)) {
            $this->redirect('/manager/settings', 'danger', 'Restaurant name is required.');
        }

        try {
            Restaurant::update($restaurantId, [
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'timezone' => $timezone,
                'status' => 'active',
            ]);

            // Update session if needed
            $_SESSION['restaurant_name'] = $name;
            $_SESSION['restaurant_timezone'] = $timezone;

            AuditLog::log(
                AuditLog::ACTION_SETTINGS_UPDATE,
                'restaurant',
                $restaurantId,
                $restaurantId,
                $user['id'],
                ['name' => $name, 'timezone' => $timezone]
            );

            $this->redirect('/manager/settings', 'success', 'Restaurant settings saved successfully.');
        } catch (Throwable $e) {
            $this->redirect('/manager/settings', 'danger', 'Error updating settings: ' . $e->getMessage());
        }
    }
}
