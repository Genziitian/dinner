<?php
/**
 * Atta Mill Controller
 * Handles dedicated Mill dashboard, orders, services, rates, customers, WhatsApp, and backups.
 * Strictly NO emojis used
 */

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once ROOT_PATH . '/models/Restaurant.php';
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/models/MillService.php';
require_once ROOT_PATH . '/models/MillCustomer.php';
require_once ROOT_PATH . '/models/MillOrder.php';

class MillController extends BaseController {
    /**
     * Ensure authenticated user belongs to a Mill outlet
     */
    private function requireMillUser(): array {
        $user = $this->requireAuth();

        if ($user['role'] === User::ROLE_SUPERADMIN) {
            // If Super Admin, use query parameter or first mill outlet
            $restaurantId = !empty($_GET['mill_id']) ? (int)$_GET['mill_id'] : ($user['restaurant_id'] ?? null);
            if (!$restaurantId) {
                $firstMill = Database::fetchOne("SELECT id, name FROM restaurants WHERE shop_type = 'mill' LIMIT 1");
                $restaurantId = $firstMill ? (int)$firstMill['id'] : 1;
            }
            $user['restaurant_id'] = $restaurantId;
            return $user;
        }

        if (($user['shop_type'] ?? '') !== 'mill') {
            $this->redirect('/login', 'danger', 'Access denied. Account is not assigned to a Mill outlet.');
        }

        return $user;
    }

    /**
     * Mill Dashboard / Today's Summary
     */
    public function dashboard(): void {
        $user = $this->requireMillUser();
        $restaurantId = (int)$user['restaurant_id'];
        $today = date('Y-m-d');

        $summary = MillOrder::getDailySummary($restaurantId, $today);
        $recentOrders = MillOrder::allByRestaurant($restaurantId, ['limit' => 8, 'exclude_cancelled' => true]);
        $services = MillService::allByRestaurant($restaurantId, true);

        $this->render('mill/dashboard', [
            'title'        => __t('dashboard_title'),
            'user'         => $user,
            'summary'      => $summary,
            'recentOrders' => $recentOrders,
            'services'     => $services,
            'today'        => $today,
        ]);
    }

    /**
     * Create New Order Screen
     */
    public function newOrder(): void {
        $user = $this->requireMillUser();
        $restaurantId = (int)$user['restaurant_id'];

        $services = MillService::allByRestaurant($restaurantId, true);
        if (empty($services)) {
            MillService::seedDefaults($restaurantId);
            $services = MillService::allByRestaurant($restaurantId, true);
        }

        $prefillPhone = trim((string)($_GET['phone'] ?? ''));
        $prefillCustomer = null;
        if (!empty($prefillPhone)) {
            $prefillCustomer = MillCustomer::findByPhone($restaurantId, $prefillPhone);
        }

        $this->render('mill/new_order', [
            'title'           => __t('create_order_title'),
            'user'            => $user,
            'services'        => $services,
            'prefillCustomer' => $prefillCustomer,
        ]);
    }

    /**
     * Store New Order
     */
    public function storeOrder(): void {
        $user = $this->requireMillUser();
        $this->validateCsrf();
        $restaurantId = (int)$user['restaurant_id'];

        $name = trim((string)($_POST['customer_name'] ?? ''));
        $phone = preg_replace('/[^0-9]/', '', (string)($_POST['customer_phone'] ?? ''));
        $serviceId = (int)($_POST['service_id'] ?? 0);
        $weight = (float)($_POST['weight_kg'] ?? 0);
        $paymentStatus = (string)($_POST['payment_status'] ?? 'unpaid');
        $paymentMethod = (string)($_POST['payment_method'] ?? 'Cash');
        $notes = trim((string)($_POST['notes'] ?? ''));

        if (empty($name) || empty($phone)) {
            $this->redirect('/mill/orders/new', 'danger', 'Customer name and valid mobile number are required.');
        }

        if ($weight <= 0) {
            $this->redirect('/mill/orders/new', 'danger', 'Please enter a valid weight greater than 0 KG.');
        }

        $service = MillService::findById($serviceId);
        if (!$service || (int)$service['restaurant_id'] !== $restaurantId) {
            $this->redirect('/mill/orders/new', 'danger', 'Please select a valid service.');
        }

        $serviceName = current_lang() === 'hi' && !empty($service['name_hi']) ? $service['name_hi'] : $service['name'];

        try {
            $orderId = MillOrder::create([
                'restaurant_id'   => $restaurantId,
                'customer_name'   => $name,
                'customer_phone'  => $phone,
                'service_id'      => $service['id'],
                'service_name'    => $serviceName,
                'weight_kg'       => $weight,
                'rate_per_kg'     => (float)$service['rate_per_kg'],
                'payment_status'  => $paymentStatus === 'paid' ? 'paid' : 'unpaid',
                'payment_method'  => $paymentMethod,
                'status'          => MillOrder::STATUS_RECEIVED,
                'notes'           => $notes,
            ]);

            $this->redirect('/mill/orders', 'success', 'Order created successfully.');
        } catch (Throwable $e) {
            $this->redirect('/mill/orders/new', 'danger', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Orders List & Filters
     */
    public function orders(): void {
        $user = $this->requireMillUser();
        $restaurantId = (int)$user['restaurant_id'];

        $status = (string)($_GET['status'] ?? 'all');
        $search = trim((string)($_GET['search'] ?? ''));

        $filters = ['search' => $search];
        if ($status === 'unpaid') {
            $filters['payment_status'] = 'unpaid';
        } elseif ($status !== 'all' && in_array($status, ['received', 'processing', 'ready', 'delivered', 'cancelled'], true)) {
            $filters['status'] = $status;
        }

        $orders = MillOrder::allByRestaurant($restaurantId, $filters);

        $this->render('mill/orders', [
            'title'   => __t('order_list_title'),
            'user'    => $user,
            'orders'  => $orders,
            'status'  => $status,
            'search'  => $search,
        ]);
    }

    /**
     * Update Order Status (AJAX / POST)
     */
    public function updateStatus(): void {
        $user = $this->requireMillUser();
        $this->validateCsrf();
        $restaurantId = (int)$user['restaurant_id'];

        $id = (int)($_POST['id'] ?? 0);
        $status = (string)($_POST['status'] ?? '');

        $allowed = ['received', 'processing', 'ready', 'delivered', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            $this->redirect('/mill/orders', 'danger', 'Invalid status.');
        }

        $order = MillOrder::findById($id);
        if (!$order || (int)$order['restaurant_id'] !== $restaurantId) {
            $this->redirect('/mill/orders', 'danger', 'Order not found.');
        }

        MillOrder::updateStatus($id, $status);

        if ($this->isAjax()) {
            $this->json(['success' => true, 'status' => $status]);
            return;
        }

        $this->redirect('/mill/orders', 'success', 'Status updated.');
    }

    /**
     * Update Payment Status (Paid / Unpaid)
     */
    public function updatePayment(): void {
        $user = $this->requireMillUser();
        $this->validateCsrf();
        $restaurantId = (int)$user['restaurant_id'];

        $id = (int)($_POST['id'] ?? 0);
        $paymentStatus = (string)($_POST['payment_status'] ?? 'paid');
        $paymentMethod = !empty($_POST['payment_method']) ? (string)$_POST['payment_method'] : 'Cash';

        $order = MillOrder::findById($id);
        if (!$order || (int)$order['restaurant_id'] !== $restaurantId) {
            $this->redirect('/mill/orders', 'danger', 'Order not found.');
        }

        MillOrder::updatePayment($id, $paymentStatus, $paymentMethod);

        if ($this->isAjax()) {
            $this->json(['success' => true, 'payment_status' => $paymentStatus]);
            return;
        }

        $this->redirect('/mill/orders', 'success', 'Payment status updated.');
    }

    /**
     * Customers Directory
     */
    public function customers(): void {
        $user = $this->requireMillUser();
        $restaurantId = (int)$user['restaurant_id'];
        $search = trim((string)($_GET['search'] ?? ''));

        $customers = MillCustomer::allByRestaurant($restaurantId, $search);

        $this->render('mill/customers', [
            'title'     => __t('customer_title'),
            'user'      => $user,
            'customers' => $customers,
            'search'    => $search,
        ]);
    }

    /**
     * Customer Profile & Past Grinding History
     */
    public function customerView(): void {
        $user = $this->requireMillUser();
        $restaurantId = (int)$user['restaurant_id'];

        $phone = preg_replace('/[^0-9]/', '', (string)($_GET['phone'] ?? ''));
        if (empty($phone)) {
            $this->redirect('/mill/customers', 'danger', 'Customer phone required.');
        }

        $customer = MillCustomer::findByPhone($restaurantId, $phone);
        if (!$customer) {
            $this->redirect('/mill/customers', 'danger', 'Customer not found.');
        }

        $stats = MillCustomer::getCustomerStats($restaurantId, $phone);
        $orders = MillOrder::getCustomerOrders($restaurantId, $phone);

        $this->render('mill/customer_view', [
            'title'    => $customer['name'] . ' - ' . __t('customer_history'),
            'user'     => $user,
            'customer' => $customer,
            'stats'    => $stats,
            'orders'   => $orders,
        ]);
    }

    /**
     * Customer Search Autocomplete Endpoint (JSON)
     */
    public function customerSearchAjax(): void {
        $user = $this->requireMillUser();
        $restaurantId = (int)$user['restaurant_id'];
        $q = trim((string)($_GET['q'] ?? ''));

        $results = MillCustomer::search($restaurantId, $q);
        $this->json(['success' => true, 'customers' => $results]);
    }

    /**
     * Services & Rates Management
     */
    public function services(): void {
        $user = $this->requireMillUser();
        $restaurantId = (int)$user['restaurant_id'];

        $services = MillService::allByRestaurant($restaurantId);
        if (empty($services)) {
            MillService::seedDefaults($restaurantId);
            $services = MillService::allByRestaurant($restaurantId);
        }

        $this->render('mill/services', [
            'title'    => __t('services_title'),
            'user'     => $user,
            'services' => $services,
        ]);
    }

    /**
     * Save Service (Create or Update)
     */
    public function serviceSave(): void {
        $user = $this->requireMillUser();
        $this->validateCsrf();
        $restaurantId = (int)$user['restaurant_id'];

        $id = (int)($_POST['id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $nameHi = trim((string)($_POST['name_hi'] ?? ''));
        $rate = (float)($_POST['rate_per_kg'] ?? 0);
        $active = isset($_POST['active']) ? 1 : 0;

        if (empty($name)) {
            $this->redirect('/mill/services', 'danger', 'Service name is required.');
        }

        if ($rate < 0) {
            $this->redirect('/mill/services', 'danger', 'Rate must be 0 or positive.');
        }

        if ($id > 0) {
            $existing = MillService::findById($id);
            if (!$existing || (int)$existing['restaurant_id'] !== $restaurantId) {
                $this->redirect('/mill/services', 'danger', 'Service not found.');
            }
            MillService::update($id, [
                'name'        => $name,
                'name_hi'     => $nameHi,
                'rate_per_kg' => $rate,
                'active'      => $active,
            ]);
            $this->redirect('/mill/services', 'success', 'Service updated.');
        } else {
            MillService::create([
                'restaurant_id' => $restaurantId,
                'name'          => $name,
                'name_hi'       => $nameHi,
                'rate_per_kg'   => $rate,
                'active'        => 1,
            ]);
            $this->redirect('/mill/services', 'success', 'New service added.');
        }
    }

    /**
     * Toggle Service Active/Inactive
     */
    public function serviceToggle(): void {
        $user = $this->requireMillUser();
        $this->validateCsrf();
        $restaurantId = (int)$user['restaurant_id'];

        $id = (int)($_POST['id'] ?? 0);
        $existing = MillService::findById($id);
        if (!$existing || (int)$existing['restaurant_id'] !== $restaurantId) {
            $this->redirect('/mill/services', 'danger', 'Service not found.');
        }

        MillService::toggle($id);
        $this->redirect('/mill/services', 'success', 'Service status updated.');
    }

    /**
     * Backup View
     */
    public function backup(): void {
        $user = $this->requireMillUser();

        $this->render('mill/backup', [
            'title' => __t('backup_title'),
            'user'  => $user,
        ]);
    }

    /**
     * Export Backup (JSON download)
     */
    public function exportBackup(): void {
        $user = $this->requireMillUser();
        $restaurantId = (int)$user['restaurant_id'];

        $data = MillOrder::exportAll($restaurantId);
        $shopName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $user['restaurant_name'] ?: 'mill');
        $filename = "backup_{$shopName}_" . date('Ymd_His') . ".json";

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Restore Backup (Upload JSON)
     */
    public function restoreBackup(): void {
        $user = $this->requireMillUser();
        $this->validateCsrf();
        $restaurantId = (int)$user['restaurant_id'];

        if (empty($_FILES['backup_file']['tmp_name'])) {
            $this->redirect('/mill/backup', 'danger', 'Please choose a valid JSON backup file.');
        }

        $raw = file_get_contents($_FILES['backup_file']['tmp_name']);
        $payload = json_decode($raw, true);

        if (!is_array($payload)) {
            $this->redirect('/mill/backup', 'danger', 'Invalid backup file format.');
        }

        $result = MillOrder::restoreBackup($restaurantId, $payload);

        $msg = "Backup restored: {$result['customers']} customer(s), {$result['services']} service(s), {$result['orders']} order(s).";
        $this->redirect('/mill/backup', 'success', $msg);
    }

    /**
     * Language Switcher
     */
    public function setLanguage(): void {
        $lang = (string)($_GET['lang'] ?? 'en');
        set_lang($lang);

        $referer = $_SERVER['HTTP_REFERER'] ?? url('mill/dashboard');
        header('Location: ' . $referer);
        exit;
    }
}
