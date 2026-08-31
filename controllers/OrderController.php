<?php
/**
 * Order Controller
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once ROOT_PATH . '/models/Order.php';
require_once ROOT_PATH . '/models/Item.php';
require_once ROOT_PATH . '/models/Restaurant.php';

class OrderController extends BaseController {
    /**
     * Store new order (Cashier / Manager) via AJAX or POST
     */
    public function store(): void {
        $user = $this->requireRole([User::ROLE_CASHIER, User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $this->validateCsrf();
        $restaurantId = $this->requireRestaurantId();

        // Get payload (supports JSON or Form POST)
        $input = [];
        $rawJson = file_get_contents('php://input');
        if (!empty($rawJson)) {
            $decoded = json_decode($rawJson, true);
            if (is_array($decoded)) {
                $input = $decoded;
            }
        }
        if (empty($input)) {
            $input = $_POST;
        }

        $customerName = !empty($input['customer_name']) ? trim((string)$input['customer_name']) : null;
        $customerPhone = !empty($input['customer_phone']) ? trim((string)$input['customer_phone']) : null;
        $paymentMethod = (string)($input['payment_method'] ?? Order::PAYMENT_CASH);
        $items = $input['items'] ?? [];

        if (empty($items) || !is_array($items)) {
            $this->json(['success' => false, 'message' => 'Cart is empty. Please add items before saving.'], 422);
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

            $this->json([
                'success' => true,
                'message' => "Order #{$result['order_number']} saved successfully.",
                'order_id' => $result['order_id'],
                'order_number' => $result['order_number'],
                'total' => $result['total'],
                'receipt_token' => $result['receipt_token'],
                'redirect_url' => url('receipt/view?id=' . $result['order_id'] . '&token=' . $result['receipt_token']),
            ]);
        } catch (Throwable $e) {
            error_log('Order creation error: ' . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to save order: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Internal receipt view for restaurant staff (Cashier, Manager, Admin)
     */
    public function view(): void {
        $user = $this->requireRole([User::ROLE_CASHIER, User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $orderId = (int)($_GET['id'] ?? 0);
        $orderNumber = (int)($_GET['order_number'] ?? 0);
        $token = trim((string)($_GET['token'] ?? ''));
        $search = trim((string)($_GET['search'] ?? $_GET['q'] ?? ''));

        if (!empty($search)) {
            // Strip out #, Order, url path if scanned
            if (preg_match('#receipt/([a-zA-Z0-9_\-]+)#', $search, $m)) {
                $token = $m[1];
            } elseif (strlen($search) > 20 && preg_match('/^[a-zA-Z0-9_\-]+$/', $search)) {
                $token = $search;
            } else {
                $cleanNum = (int)preg_replace('/[^0-9]/', '', $search);
                if ($cleanNum > 0) {
                    $orderNumber = $cleanNum;
                }
            }
        }

        $order = null;
        if (!empty($token)) {
            $order = Order::findByReceiptToken($token);
        } elseif ($orderId > 0) {
            $order = Order::findById($orderId, $restaurantId);
        } elseif ($orderNumber > 0) {
            $order = Order::findByOrderNumber($orderNumber, $restaurantId);
        }

        if (!$order || (int)$order['restaurant_id'] !== $restaurantId) {
            $fallbackUrl = $user['role'] === User::ROLE_CASHIER ? '/cashier/order' : '/manager/dashboard';
            $this->redirect($fallbackUrl, 'danger', 'Receipt or Order not found.');
            return;
        }

        // Public URL token
        $rawToken = !empty($token) ? $token : null;
        $publicUrl = $rawToken ? url_absolute('receipt/' . $rawToken) : null;

        $this->render('receipt/view', [
            'title' => "Receipt #{$order['order_number']} | DinePOS",
            'order' => $order,
            'publicUrl' => $publicUrl,
        ]);
    }

    /**
     * Manager Edit Order Screen (Manager / Superadmin only)
     */
    public function edit(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $orderId = (int)($_GET['id'] ?? 0);
        $order = Order::findById($orderId, $restaurantId);

        if (!$order) {
            $this->redirect('/manager/orders', 'danger', 'Order not found or inaccessible.');
        }

        $items = Item::allByRestaurant($restaurantId, false);

        $this->render('manager/order_edit', [
            'title' => "Edit Order #{$order['order_number']} | DinePOS",
            'order' => $order,
            'items' => $items,
        ]);
    }

    /**
     * Manager Update Order Submission (Manager / Superadmin only)
     */
    public function update(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $this->validateCsrf();
        $restaurantId = $this->requireRestaurantId();

        $input = [];
        $rawJson = file_get_contents('php://input');
        if (!empty($rawJson)) {
            $decoded = json_decode($rawJson, true);
            if (is_array($decoded)) $input = $decoded;
        }
        if (empty($input)) $input = $_POST;

        $orderId = (int)($input['order_id'] ?? 0);
        $customerName = !empty($input['customer_name']) ? trim((string)$input['customer_name']) : null;
        $customerPhone = !empty($input['customer_phone']) ? trim((string)$input['customer_phone']) : null;
        $paymentMethod = (string)($input['payment_method'] ?? Order::PAYMENT_CASH);
        $items = $input['items'] ?? [];

        try {
            Order::updateOrder(
                $orderId,
                $restaurantId,
                $user['id'],
                $customerName,
                $customerPhone,
                $paymentMethod,
                $items
            );

            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => 'Order updated successfully.']);
            }

            $this->redirect("/orders/view?id={$orderId}", 'success', 'Order updated successfully.');
        } catch (Throwable $e) {
            error_log('Order update error: ' . $e->getMessage());
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            $this->redirect("/manager/orders/edit?id={$orderId}", 'danger', 'Error updating order: ' . $e->getMessage());
        }
    }

    /**
     * Manager Soft Delete Order (Manager / Superadmin only)
     */
    public function delete(): void {
        $user = $this->requireRole([User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $this->validateCsrf();
        $restaurantId = $this->requireRestaurantId();

        $orderId = (int)($_POST['order_id'] ?? 0);
        if ($orderId <= 0) {
            $this->redirect('/manager/orders', 'danger', 'Invalid order ID.');
        }

        $success = Order::softDelete($orderId, $restaurantId, $user['id']);

        if ($this->isAjax()) {
            $this->json([
                'success' => $success,
                'message' => $success ? 'Order deleted successfully.' : 'Could not delete order.',
            ]);
        }

        $this->redirect('/manager/orders', $success ? 'success' : 'danger', $success ? 'Order deleted successfully.' : 'Order not found.');
    }
}
