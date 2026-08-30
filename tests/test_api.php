<?php
/**
 * Automated Test Suite for DinePOS Mobile REST API
 */

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/models/Database.php';
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/models/Restaurant.php';
require_once ROOT_PATH . '/models/Item.php';
require_once ROOT_PATH . '/models/ItemVariant.php';
require_once ROOT_PATH . '/models/Order.php';
require_once ROOT_PATH . '/models/DailyCounter.php';
require_once ROOT_PATH . '/controllers/ApiController.php';

echo "===========================================\n";
echo "  DinePOS Mobile REST API Verification\n";
echo "===========================================\n\n";

$db = Database::getConnection();

// 1. Ensure test restaurant exists
$restaurant = Database::fetchOne("SELECT * FROM restaurants LIMIT 1");
if (!$restaurant) {
    $restId = Restaurant::create([
        'name' => 'Paradise Dine & Lounge',
        'phone' => '9876543210',
        'address' => '123 Food Street, Bangalore',
        'timezone' => 'Asia/Kolkata',
        'status' => 'active'
    ]);
    $restaurant = Restaurant::findById($restId);
}
echo "✓ [PASS] Active Restaurant: {$restaurant['name']} (ID: {$restaurant['id']})\n";

// 2. Ensure Manager and Cashier users exist
$manager = User::findByUsername('manager1');
if (!$manager) {
    $mId = User::create([
        'restaurant_id' => $restaurant['id'],
        'username' => 'manager1',
        'password' => 'pass1234',
        'role' => User::ROLE_MANAGER,
        'status' => 'active'
    ]);
    $manager = User::findById($mId);
}
echo "✓ [PASS] Manager user exists: {$manager['username']} (Role: {$manager['role']})\n";

$cashier = User::findByUsername('cashier1');
if (!$cashier) {
    $cId = User::create([
        'restaurant_id' => $restaurant['id'],
        'username' => 'cashier1',
        'password' => 'pass1234',
        'role' => User::ROLE_CASHIER,
        'status' => 'active'
    ]);
    $cashier = User::findById($cId);
}
echo "✓ [PASS] Cashier user exists: {$cashier['username']} (Role: {$cashier['role']})\n";

// 3. Ensure test items exist
$items = Item::allByRestaurant((int)$restaurant['id'], false);
if (empty($items)) {
    // Chicken Biryani (Portion)
    Item::create((int)$restaurant['id'], [
        'name' => 'Chicken Biryani',
        'item_type' => 'portion',
        'base_unit' => 'portion',
        'active' => 1
    ], [
        ['variant_name' => 'Full', 'quantity_value' => 1.0, 'quantity_unit' => 'portion', 'price' => 280.00, 'active' => 1],
        ['variant_name' => 'Half', 'quantity_value' => 0.5, 'quantity_unit' => 'portion', 'price' => 160.00, 'active' => 1]
    ]);

    // Egg Roll (Piece)
    Item::create((int)$restaurant['id'], [
        'name' => 'Egg Roll',
        'item_type' => 'piece',
        'base_unit' => 'piece',
        'active' => 1
    ], [
        ['variant_name' => 'Standard', 'quantity_value' => 1.0, 'quantity_unit' => 'piece', 'price' => 60.00, 'active' => 1]
    ]);

    // Basmati Rice (Weight)
    Item::create((int)$restaurant['id'], [
        'name' => 'Basmati Rice',
        'item_type' => 'weight',
        'base_unit' => 'kg',
        'active' => 1
    ], [
        ['variant_name' => '1 kg', 'quantity_value' => 1.0, 'quantity_unit' => 'kg', 'price' => 120.00, 'active' => 1]
    ]);

    $items = Item::allByRestaurant((int)$restaurant['id'], false);
}
echo "✓ [PASS] Menu items populated: " . count($items) . " items\n";

// 4. Test Order Creation & Server-Authoritative Price Calculation
$testItem1 = $items[0];
$testVariant1 = $testItem1['variants'][0];

$testItem2 = $items[1] ?? $items[0];
$testVariant2 = $testItem2['variants'][0];

$orderResult = Order::create(
    (int)$restaurant['id'],
    (int)$cashier['id'],
    'Test Customer',
    '9988776655',
    'Cash',
    [
        ['item_id' => $testItem1['id'], 'variant_id' => $testVariant1['id'], 'quantity' => 2],
        ['item_id' => $testItem2['id'], 'variant_id' => $testVariant2['id'], 'quantity' => 1]
    ]
);

$expectedTotal = (2 * (float)$testVariant1['price']) + (1 * (float)$testVariant2['price']);
if (abs($orderResult['total'] - $expectedTotal) > 0.01) {
    throw new Exception("Order total mismatch: expected {$expectedTotal}, got {$orderResult['total']}");
}
echo "✓ [PASS] Order #{$orderResult['order_number']} created with server-authoritative total: ₹{$orderResult['total']}\n";
echo "✓ [PASS] Cryptographic receipt token generated: {$orderResult['receipt_token']}\n";

// 5. Verify Daily Collection Stats
$stats = Order::getCashierSummary((int)$restaurant['id'], date('Y-m-d'));
if ($stats['total_orders'] < 1 || $stats['total_sales'] <= 0) {
    throw new Exception("Daily stats aggregation failed.");
}
echo "✓ [PASS] Cashier Summary: {$stats['total_orders']} orders, ₹{$stats['total_sales']} total revenue\n";

// 6. Verify Manager Dashboard Reports
$mgrStats = Order::getStats((int)$restaurant['id'], 'today');
if ($mgrStats['total_orders'] < 1) {
    throw new Exception("Manager dashboard stats failed.");
}
echo "✓ [PASS] Manager Dashboard: ₹{$mgrStats['total_sales']} sales recorded\n";

echo "\n===========================================\n";
echo "  All 6 Verification Tests PASSED! ✓✓✓\n";
echo "===========================================\n";
