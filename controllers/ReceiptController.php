<?php
/**
 * Public Receipt Controller
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once ROOT_PATH . '/models/Order.php';

class ReceiptController extends BaseController {
    /**
     * Publicly view receipt via cryptographically random token
     */
    public function show(string $token): void {
        $token = trim($token);
        
        // Ensure token has sufficient length/entropy (at least 32 characters hex/base64)
        if (strlen($token) < 32 || !preg_match('/^[a-zA-Z0-9_\-]+$/', $token)) {
            http_response_code(404);
            $this->renderStandalone('errors/404', ['title' => 'Invalid Receipt Token']);
            return;
        }

        $order = Order::findByReceiptToken($token);

        if (!$order) {
            http_response_code(404);
            $this->renderStandalone('errors/404', ['title' => 'Receipt Not Found']);
            return;
        }

        $this->renderStandalone('receipt/public_view', [
            'title' => "Receipt #{$order['order_number']} - {$order['restaurant_name']}",
            'order' => $order,
            'token' => $token,
        ]);
    }
}
