<?php
declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/models/Restaurant.php';
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/models/MillService.php';
require_once ROOT_PATH . '/models/MillCustomer.php';
require_once ROOT_PATH . '/models/MillOrder.php';

echo "========================================\n";
echo "Testing Atta Mill Module Features\n";
echo "========================================\n";

// 1. Create a Mill Outlet
$millName = 'Shree Ram Atta Mill Test';
$millId = Restaurant::create([
    'name'      => $millName,
    'phone'     => '9876543210',
    'address'   => 'Main Bazaar, Mandi Road',
    'timezone'  => 'Asia/Kolkata',
    'shop_type' => 'mill',
    'status'    => 'active',
]);
echo "[PASS] 1. Mill shop created with ID: {$millId}\n";

$shop = Restaurant::findById($millId);
assert($shop['shop_type'] === 'mill', "Shop type must be 'mill'");
echo "[PASS] 2. Verified shop_type === 'mill'\n";

// 2. Seed Default Services
MillService::seedDefaults($millId);
$services = MillService::allByRestaurant($millId, true);
assert(count($services) >= 4, "Must have at least 4 default services");
echo "[PASS] 3. Seeded " . count($services) . " default mill services\n";

$wheatService = null;
foreach ($services as $s) {
    if (str_contains(strtolower($s['name']), 'wheat')) {
        $wheatService = $s;
        break;
    }
}
assert($wheatService !== null, "Wheat grinding service must exist");
echo "[PASS] 4. Found Wheat grinding service @ Rs. {$wheatService['rate_per_kg']}/KG\n";

// 3. Create Manager User (3-10 chars username)
$username = 'mgr_' . rand(100, 999);
$userId = User::create([
    'restaurant_id' => $millId,
    'username'      => $username,
    'password'      => 'secret123',
    'role'          => User::ROLE_MANAGER,
    'status'        => 'active',
]);
$userObj = User::findById($userId);
assert($userObj['shop_type'] === 'mill', "User's shop_type must be 'mill'");
echo "[PASS] 5. Created manager user '{$username}' with shop_type 'mill'\n";

// 4. Create New Order (Feature 1, 2, 5)
$orderId = MillOrder::create([
    'restaurant_id'   => $millId,
    'customer_name'   => 'Ramesh Kumar',
    'customer_phone'  => '9876543210',
    'service_id'      => $wheatService['id'],
    'service_name'    => $wheatService['name'],
    'weight_kg'       => 20.0,
    'rate_per_kg'     => (float)$wheatService['rate_per_kg'],
    'payment_status'  => 'unpaid',
    'payment_method'  => 'Cash',
    'status'          => 'received',
    'notes'           => 'Pisaai moti rakhna',
]);
$order = MillOrder::findById($orderId);
assert($order !== null, "Order must be created");
assert((float)$order['total_amount'] === 100.0, "Total must be 20 * 5 = 100.00");
echo "[PASS] 6. Created order #{$order['order_number']} for 20 KG @ Rs.{$order['rate_per_kg']}/kg = Rs.{$order['total_amount']}\n";

// 5. Test Rate Snapshotting (Feature 3)
// Change wheat service rate to Rs. 8/KG
MillService::update((int)$wheatService['id'], [
    'name'        => $wheatService['name'],
    'name_hi'     => $wheatService['name_hi'],
    'rate_per_kg' => 8.00,
    'active'      => 1,
]);
$orderAfterRateChange = MillOrder::findById($orderId);
assert((float)$orderAfterRateChange['rate_per_kg'] === 5.0, "Old order must retain rate 5.00");
assert((float)$orderAfterRateChange['total_amount'] === 100.0, "Old order total must remain 100.00");
echo "[PASS] 7. Rate snapshot verified: Service changed to Rs.8, order preserved at Rs.{$orderAfterRateChange['rate_per_kg']}\n";

// 6. Test Status Flow (Feature 4)
MillOrder::updateStatus($orderId, 'processing');
$o = MillOrder::findById($orderId);
assert($o['status'] === 'processing', "Status should be processing");

MillOrder::updateStatus($orderId, 'ready');
$o = MillOrder::findById($orderId);
assert($o['status'] === 'ready', "Status should be ready");

MillOrder::updateStatus($orderId, 'delivered');
$o = MillOrder::findById($orderId);
assert($o['status'] === 'delivered', "Status should be delivered");
echo "[PASS] 8. Status pipeline verified (received -> processing -> ready -> delivered)\n";

// 7. Test Payment Tracking (Feature 7)
assert($o['payment_status'] === 'unpaid', "Order should be unpaid initially");
MillOrder::updatePayment($orderId, 'paid', 'UPI');
$o = MillOrder::findById($orderId);
assert($o['payment_status'] === 'paid', "Payment should be paid");
assert($o['payment_method'] === 'UPI', "Payment method should be UPI");
echo "[PASS] 9. Payment tracking verified: marked as Paid via UPI\n";

// 8. Test Customer Records & Auto-Remember (Feature 1 & 8)
$customer = MillCustomer::findByPhone($millId, '9876543210');
assert($customer !== null, "Customer must be auto-remembered");
assert($customer['name'] === 'Ramesh Kumar', "Customer name must match");

$stats = MillCustomer::getCustomerStats($millId, '9876543210');
assert((int)$stats['total_orders'] === 1, "Customer should have 1 order");
assert((float)$stats['lifetime_spent'] === 100.0, "Lifetime spent should be 100.00");
echo "[PASS] 10. Customer auto-remember & history stats verified\n";

// 9. Test Daily Summary (Feature 9)
$summary = MillOrder::getDailySummary($millId, date('Y-m-d'));
assert((int)$summary['total_orders'] >= 1, "Today orders >= 1");
assert((float)$summary['paid_amount'] >= 100.0, "Paid amount >= 100.00");
echo "[PASS] 11. Daily summary metrics verified: orders: {$summary['total_orders']}, paid: {$summary['paid_amount']}\n";

// 10. Test Backup Export & Restore (Feature 10)
$backup = MillOrder::exportAll($millId);
assert(!empty($backup['orders']), "Backup must have orders");
assert(!empty($backup['customers']), "Backup must have customers");
assert(!empty($backup['services']), "Backup must have services");
echo "[PASS] 12. Backup exported successfully: " . count($backup['orders']) . " orders, " . count($backup['customers']) . " customers\n";

// 11. Test Translations & Language Toggle (English & Hindi)
set_lang('en');
assert(__t('nav_new_order') === 'New Order', "English translation must be 'New Order'");
set_lang('hi');
assert(__t('nav_new_order') === 'नया ऑर्डर', "Hindi translation must be 'नया ऑर्डर'");
set_lang('en'); // Reset to English
echo "[PASS] 13. Bilingual translations verified (English & Hindi)\n";

// Clean up test data
Database::execute("DELETE FROM restaurants WHERE id = :id", [':id' => $millId]);
echo "[PASS] 14. Test cleanup completed.\n";

echo "========================================\n";
echo "All 14 tests PASSED successfully!\n";
echo "========================================\n";
