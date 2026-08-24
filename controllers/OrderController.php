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
     * Internal receipt view for restaurant staff
     */
    public function view(): void {
        $user = $this->requireRole([User::ROLE_CASHIER, User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $orderId = (int)($_GET['id'] ?? 0);
        $token = (string)($_GET['token'] ?? '');

        if ($orderId <= 0 && empty($token)) {
            $this->redirect('/cashier/order', 'danger', 'Invalid receipt request.');
        }

        $order = null;
        if (!empty($token)) {
            // Find by token if provided
            $order = Order::findByReceiptToken($token);
        } elseif ($orderId > 0) {
            // Cashiers cannot freely browse historical order IDs unless manager
            if ($user['role'] === User::ROLE_CASHIER) {
                $this->redirect('/cashier/order', 'danger', 'Cashiers can only view receipts immediately upon saving.');
            }
            $order = Order::findById($orderId, $restaurantId);
        }

        if (!$order || (int)$order['restaurant_id'] !== $restaurantId) {
            http_response_code(404);
            $this->renderStandalone('errors/404', ['title' => 'Receipt Not Found']);
            return;
        }

        // Generate public secure URL for QR code
        // Note: The raw token is present if passed in URL or reconstructed if order was looked up
        $rawToken = !empty($token) ? $token : null;
        $publicUrl = $rawToken ? url('receipt/' . $rawToken) : null;

        $this->render('receipt/view', [
            'title' => "Receipt #{$order['order_number']} | DinePOS",
            'order' => $order,
            'publicUrl' => $publicUrl,
            'rawToken' => $rawToken,
        ]);
    }

    /**
     * Manager Edit Order Screen
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
     * Manager Update Order Submission
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

            $this->redirect("/manager/orders/view?id={$orderId}", 'success', 'Order updated successfully.');
        } catch (Throwable $e) {
            error_log('Order update error: ' . $e->getMessage());
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            $this->redirect("/manager/orders/edit?id={$orderId}", 'danger', 'Error updating order: ' . $e->getMessage());
        }
    }

    /**
     * Manager Soft Delete Order
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
