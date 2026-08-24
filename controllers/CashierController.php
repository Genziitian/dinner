<?php
/**
 * Cashier Controller
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once ROOT_PATH . '/models/Item.php';
require_once ROOT_PATH . '/models/Order.php';
require_once ROOT_PATH . '/models/DailyCounter.php';

class CashierController extends BaseController {
    /**
     * Cashier Billing Screen
     */
    public function order(): void {
        $user = $this->requireRole([User::ROLE_CASHIER, User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $restaurant = Restaurant::findById($restaurantId);
        $timezone = $restaurant['timezone'] ?? 'Asia/Kolkata';
        $tz = new DateTimeZone($timezone);
        $todayDate = (new DateTime('now', $tz))->format('Y-m-d');

        // Fetch active items with active variants
        $items = Item::allByRestaurant($restaurantId, true);
        $previewOrderNumber = DailyCounter::previewNextOrderNumber($restaurantId, $todayDate);

        $this->render('cashier/billing', [
            'title' => 'New Order | DinePOS',
            'restaurant' => $restaurant,
            'items' => $items,
            'previewOrderNumber' => $previewOrderNumber,
            'todayDate' => $todayDate,
        ]);
    }

    /**
     * Cashier Today's Collection Summary
     */
    public function summary(): void {
        $user = $this->requireRole([User::ROLE_CASHIER, User::ROLE_MANAGER, User::ROLE_SUPERADMIN]);
        $restaurantId = $this->requireRestaurantId();

        $restaurant = Restaurant::findById($restaurantId);
        $tz = new DateTimeZone($restaurant['timezone'] ?? 'Asia/Kolkata');
        $todayDate = (new DateTime('now', $tz))->format('Y-m-d');

        $stats = Order::getCashierSummary($restaurantId, $todayDate);

        $this->render('cashier/summary', [
            'title' => "Today's Summary | DinePOS",
            'restaurant' => $restaurant,
            'stats' => $stats,
            'todayDate' => $todayDate,
        ]);
    }
}
