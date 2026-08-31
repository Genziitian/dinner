<?php
/**
 * DinePOS Comprehensive Automated Test Suite
 * Validates Security, Billing, Daily Order Sequences, Tokens, Roles, Multi-Tenancy & Exports
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
require_once ROOT_PATH . '/models/RateLimiter.php';
require_once ROOT_PATH . '/models/AuditLog.php';

class TestRunner {
    private int $passed = 0;
    private int $failed = 0;

    public function assert(bool $condition, string $testName, string $failureMsg = ''): void {
        if ($condition) {
            $this->passed++;
            echo "  [PASS] {$testName}\n";
        } else {
            $this->failed++;
            echo "  [FAIL] {$testName} - {$failureMsg}\n";
        }
    }

    public function run(): void {
        echo "========================================================\n";
        echo " DinePOS Comprehensive Automated Verification Test Suite\n";
        echo "========================================================\n\n";

        $this->setupFixtures();

        $this->testDatabaseAndSeeds();
        $this->testAuthenticationAndValidation();
        $this->testBruteForceProtection();
        $this->testDailyOrderNumberGeneration();
        $this->testServerSidePricingSecurity();
        $this->testCryptographicReceiptTokenSecurity();
        $this->testMultiTenantIsolation();
        $this->testManagerOrderEditingAndSoftDelete();
        $this->testAuditLogging();
        $this->testReportingAndCsvExport();
        $this->testLegalAndPolicyPages();

        echo "\n========================================================\n";
        echo " Test Results: Passed: {$this->passed}, Failed: {$this->failed}\n";
        echo "========================================================\n";

        if ($this->failed > 0) {
            exit(1);
        }
    }

    private function setupFixtures(): void {
        $db = Database::getConnection();
        $now = date('Y-m-d H:i:s');
        $hash = password_hash('password123', PASSWORD_DEFAULT);

        // Clean tables
        $db->exec("DELETE FROM order_items; DELETE FROM orders; DELETE FROM daily_order_counters; DELETE FROM item_variants; DELETE FROM items; DELETE FROM users; DELETE FROM restaurants;");

        // Fixtures
        $db->prepare("INSERT INTO restaurants (id, name, phone, address, timezone, status, created_at, updated_at) VALUES (1, 'The Royal Fork', '+91 98765 43210', '102 Flavor Street', 'Asia/Kolkata', 'active', :now1, :now2)")->execute([':now1' => $now, ':now2' => $now]);
        $db->prepare("INSERT INTO users (id, restaurant_id, username, password_hash, role, status, failed_login_attempts, locked_until, created_at, updated_at) VALUES (1, NULL, 'superadmin', :hash, 'superadmin', 'active', 0, NULL, :now1, :now2)")->execute([':hash' => $hash, ':now1' => $now, ':now2' => $now]);
        $db->prepare("INSERT INTO users (id, restaurant_id, username, password_hash, role, status, failed_login_attempts, locked_until, created_at, updated_at) VALUES (2, 1, 'manager01', :hash, 'manager', 'active', 0, NULL, :now1, :now2)")->execute([':hash' => $hash, ':now1' => $now, ':now2' => $now]);
        $db->prepare("INSERT INTO users (id, restaurant_id, username, password_hash, role, status, failed_login_attempts, locked_until, created_at, updated_at) VALUES (3, 1, 'cashier01', :hash, 'cashier', 'active', 0, NULL, :now1, :now2)")->execute([':hash' => $hash, ':now1' => $now, ':now2' => $now]);

        // Items
        $db->prepare("INSERT INTO items (id, restaurant_id, name, item_type, base_unit, active, created_at, updated_at) VALUES (1, 1, 'Chicken Curry', 'portion', 'portion', 1, :now1, :now2)")->execute([':now1' => $now, ':now2' => $now]);
        $db->prepare("INSERT INTO item_variants (id, item_id, variant_name, quantity_value, quantity_unit, price, active, created_at, updated_at) VALUES (1, 1, 'Full', 1.0, 'portion', 120.00, 1, :now1, :now2), (2, 1, 'Half', 0.5, 'portion', 60.00, 1, :now1, :now2), (3, 1, 'Quarter', 0.25, 'portion', 30.00, 1, :now1, :now2)")->execute([':now1' => $now, ':now2' => $now]);

        $db->prepare("INSERT INTO items (id, restaurant_id, name, item_type, base_unit, active, created_at, updated_at) VALUES (2, 1, 'Boiled Egg', 'piece', 'piece', 1, :now1, :now2)")->execute([':now1' => $now, ':now2' => $now]);
        $db->prepare("INSERT INTO item_variants (id, item_id, variant_name, quantity_value, quantity_unit, price, active, created_at, updated_at) VALUES (4, 2, 'Standard', 1.0, 'piece', 30.00, 1, :now1, :now2)")->execute([':now1' => $now, ':now2' => $now]);

        $db->prepare("INSERT INTO items (id, restaurant_id, name, item_type, base_unit, active, created_at, updated_at) VALUES (3, 1, 'Egg', 'piece', 'piece', 1, :now1, :now2)")->execute([':now1' => $now, ':now2' => $now]);
        $db->prepare("INSERT INTO item_variants (id, item_id, variant_name, quantity_value, quantity_unit, price, active, created_at, updated_at) VALUES (5, 3, 'Standard', 1.0, 'piece', 20.00, 1, :now1, :now2)")->execute([':now1' => $now, ':now2' => $now]);

        $db->prepare("INSERT INTO items (id, restaurant_id, name, item_type, base_unit, active, created_at, updated_at) VALUES (4, 1, 'Rice', 'weight', 'kg', 1, :now1, :now2)")->execute([':now1' => $now, ':now2' => $now]);
        $db->prepare("INSERT INTO item_variants (id, item_id, variant_name, quantity_value, quantity_unit, price, active, created_at, updated_at) VALUES (6, 4, '1 kg', 1.0, 'kg', 120.00, 1, :now1, :now2)")->execute([':now1' => $now, ':now2' => $now]);

        $db->prepare("INSERT INTO items (id, restaurant_id, name, item_type, base_unit, active, created_at, updated_at) VALUES (5, 1, 'Cold Drink', 'weight', 'l', 1, :now1, :now2)")->execute([':now1' => $now, ':now2' => $now]);
        $db->prepare("INSERT INTO item_variants (id, item_id, variant_name, quantity_value, quantity_unit, price, active, created_at, updated_at) VALUES (7, 5, '1 Liter', 1.0, 'l', 100.00, 1, :now1, :now2)")->execute([':now1' => $now, ':now2' => $now]);
    }

    private function testDatabaseAndSeeds(): void {
        echo "1. Testing Database & Test Fixtures Integrity...\n";

        $restaurant = Restaurant::findById(1);
        $this->assert($restaurant !== null && $restaurant['name'] === 'The Royal Fork', "Test restaurant loaded");

        $superadmin = User::findByUsername('superadmin');
        $this->assert($superadmin !== null && $superadmin['role'] === 'superadmin', "Super Admin fixture loaded");

        $manager = User::findByUsername('manager01');
        $this->assert($manager !== null && $manager['role'] === 'manager' && (int)$manager['restaurant_id'] === 1, "Manager fixture loaded with Restaurant 1");

        $cashier = User::findByUsername('cashier01');
        $this->assert($cashier !== null && $cashier['role'] === 'cashier' && (int)$cashier['restaurant_id'] === 1, "Cashier fixture loaded with Restaurant 1");

        $items = Item::allByRestaurant(1);
        $this->assert(count($items) >= 5, "Sample items loaded (count >= 5)");
    }

    private function testAuthenticationAndValidation(): void {
        echo "\n2. Testing Authentication & Input Validation Rules...\n";

        $user = User::findByUsername('cashier01');
        $this->assert(password_verify('password123', $user['password_hash']), "Valid password verified with password_verify");
        $this->assert(!password_verify('wrongpassword', $user['password_hash']), "Invalid password rejected");

        // Username formatting rules
        $this->assert(User::validateUsername('cashier01'), "Valid username 'cashier01' accepted");
        $this->assert(User::validateUsername('mgr_1'), "Valid username 'mgr_1' accepted");
        $this->assert(!User::validateUsername('toolongusername123'), "Over-10-char username rejected");
        $this->assert(!User::validateUsername('bad username'), "Username with spaces rejected");
        $this->assert(!User::validateUsername('us'), "Short username (< 3 chars) rejected");
        $this->assert(!User::validateUsername('user!@#'), "Username with special chars rejected");

        // Password length rules
        $this->assert(User::validatePassword('password123'), "Valid 11-char password accepted");
        $this->assert(!User::validatePassword('short'), "Password < 8 chars rejected");
        $this->assert(!User::validatePassword(str_repeat('a', 75)), "Password > 72 chars rejected");
    }

    private function testBruteForceProtection(): void {
        echo "\n3. Testing Brute-Force Rate Limiting (5 Attempts -> 15 min lock)...\n";

        $testIp = '192.168.1.100';
        $testUser = 'test_brute';

        RateLimiter::clearAttempts($testIp, $testUser);
        $this->assert(!RateLimiter::isRateLimited($testIp, $testUser), "Initial state is not rate limited");

        for ($i = 1; $i <= 4; $i++) {
            RateLimiter::recordAttempt($testIp, $testUser);
        }
        $this->assert(!RateLimiter::isRateLimited($testIp, $testUser), "4 attempts is not yet rate limited");

        RateLimiter::recordAttempt($testIp, $testUser);
        $this->assert(RateLimiter::isRateLimited($testIp, $testUser), "5th attempt triggers rate limit lock");

        RateLimiter::clearAttempts($testIp, $testUser);
        $this->assert(!RateLimiter::isRateLimited($testIp, $testUser), "Clear attempts unlocks user/IP");
    }

    private function testDailyOrderNumberGeneration(): void {
        echo "\n4. Testing Atomic Daily Order Numbering & Resetting...\n";

        // Create a test restaurant
        $testRestId = Restaurant::create([
            'name' => 'Counter Test Rest',
            'timezone' => 'Asia/Kolkata',
            'status' => 'active'
        ]);

        $date1 = '2026-08-24';
        $date2 = '2026-08-25';

        // Day 1
        $n1 = DailyCounter::getNextOrderNumber($testRestId, $date1);
        $n2 = DailyCounter::getNextOrderNumber($testRestId, $date1);
        $n3 = DailyCounter::getNextOrderNumber($testRestId, $date1);

        $this->assert($n1 === 1, "Day 1: Order #1 generated");
        $this->assert($n2 === 2, "Day 1: Order #2 generated");
        $this->assert($n3 === 3, "Day 1: Order #3 generated");

        // Day 2 (Reset to 1)
        $nDay2_1 = DailyCounter::getNextOrderNumber($testRestId, $date2);
        $nDay2_2 = DailyCounter::getNextOrderNumber($testRestId, $date2);

        $this->assert($nDay2_1 === 1, "Day 2: Order number resets to 1");
        $this->assert($nDay2_2 === 2, "Day 2: Order #2 generated on next day");
    }

    private function testServerSidePricingSecurity(): void {
        echo "\n5. Testing Server-Side Authoritative Pricing & Zero-Trust Client Calculation...\n";

        // Chicken Curry: Full = ₹120, Half = ₹60, Quarter = ₹30
        // Boiled Egg: ₹30
        // Rice: ₹120/kg
        $orderPayload = [
            [
                'item_id' => 1, // Chicken Curry
                'variant_id' => 2, // Half (₹60)
                'quantity' => 2, // 2 x 60 = 120
                'tampered_price' => 1.00 // Malicious client price (must be ignored)
            ],
            [
                'item_id' => 2, // Boiled Egg (₹30)
                'variant_id' => 4,
                'quantity' => 2, // 2 x 30 = 60
            ],
            [
                'item_id' => 4, // Rice (₹120/kg)
                'variant_id' => 6,
                'quantity' => 0.5, // 0.5 x 120 = 60
            ]
        ];

        // Expected Total: 120 + 60 + 60 = 240.00
        $order = Order::create(
            1,
            3, // cashier01
            'Raj',
            '9876543210',
            Order::PAYMENT_CASH,
            $orderPayload
        );

        $this->assert($order['total'] == 240.00, "Server calculated correct total (₹240.00) despite client tampering", "Got: {$order['total']}");
        $this->assert(!empty($order['receipt_token']), "Generated cryptographic receipt token");

        // Verify Order Items Snapshots
        $savedOrder = Order::findById((int)$order['order_id'], 1);
        $this->assert($savedOrder !== null, "Saved order retrieved from DB");
        $this->assert(count($savedOrder['items']) === 3, "All 3 order items snapshotted");

        $curryItem = $savedOrder['items'][0];
        $this->assert($curryItem['item_name_snapshot'] === 'Chicken Curry', "Item name snapshotted");
        $this->assert($curryItem['variant_name_snapshot'] === 'Half', "Variant name snapshotted");
        $this->assert((float)$curryItem['unit_price'] === 60.00, "Unit price snapshotted (₹60)");
        $this->assert((float)$curryItem['total_price'] === 120.00, "Item total price calculated (₹120)");
    }

    private function testCryptographicReceiptTokenSecurity(): void {
        echo "\n6. Testing Public QR Receipt Cryptographic Token Security...\n";

        // Create an order
        $order = Order::create(1, 3, 'TokenTest', null, Order::PAYMENT_ONLINE, [
            ['item_id' => 3, 'variant_id' => 5, 'quantity' => 1] // Egg ₹20
        ]);

        $rawToken = $order['receipt_token'];

        // Lookup with exact raw token
        $found = Order::findByReceiptToken($rawToken);
        $this->assert($found !== null && (int)$found['id'] === (int)$order['order_id'], "Lookup by exact cryptographic token succeeds");

        // Attempting to lookup by altered token must fail (404 / null)
        $tamperedToken = substr($rawToken, 0, -2) . 'ab';
        $tamperedFound = Order::findByReceiptToken($tamperedToken);
        $this->assert($tamperedFound === null, "Lookup with altered token hash returns null (Anti-tamper protection)");

        // Numeric ID cannot be used as receipt token
        $idLookup = Order::findByReceiptToken((string)$order['order_id']);
        $this->assert($idLookup === null, "Passing internal numeric ID as token fails");
    }

    private function testMultiTenantIsolation(): void {
        echo "\n7. Testing Multi-Tenant Data Isolation...\n";

        // Create second restaurant
        $r2Id = Restaurant::create([
            'name' => 'Restaurant B',
            'phone' => '1111111111',
            'timezone' => 'Asia/Kolkata',
            'status' => 'active'
        ]);

        // Create item for Restaurant B
        $r2ItemId = Item::create($r2Id, [
            'name' => 'Special Biryani',
            'item_type' => 'piece',
            'base_unit' => 'piece',
            'active' => 1
        ], [
            ['variant_name' => 'Standard', 'price' => 250.00, 'quantity_value' => 1.0, 'quantity_unit' => 'piece', 'active' => 1]
        ]);

        // Verify Restaurant 1 cannot see Restaurant 2 items
        $r1Items = Item::allByRestaurant(1);
        $r1HasBiryani = false;
        foreach ($r1Items as $it) {
            if ($it['name'] === 'Special Biryani') $r1HasBiryani = true;
        }
        $this->assert(!$r1HasBiryani, "Restaurant 1 cannot view Restaurant 2 items");

        // Verify Restaurant 1 cannot fetch Restaurant 2 item by ID
        $leakItem = Item::findById($r2ItemId, 1);
        $this->assert($leakItem === null, "Restaurant 1 scoped query for Restaurant 2 item returns null (IDOR protection)");
    }

    private function testManagerOrderEditingAndSoftDelete(): void {
        echo "\n8. Testing Manager Order Editing, Total Recalculation & Soft Deletion...\n";

        // Create an order
        $order = Order::create(1, 3, 'EditTest', '1234567890', Order::PAYMENT_CASH, [
            ['item_id' => 3, 'variant_id' => 5, 'quantity' => 1] // Egg: 1 x 20 = 20
        ]);

        $orderId = (int)$order['order_id'];

        // Manager edits order: changes to 3 Boiled Eggs (3 x 30 = 90) and payment to Online
        $updated = Order::updateOrder(
            $orderId,
            1, // Restaurant 1
            2, // manager01
            'EditTest Updated',
            '9999999999',
            Order::PAYMENT_ONLINE,
            [
                ['item_id' => 2, 'variant_id' => 4, 'quantity' => 3]
            ]
        );

        $this->assert($updated === true, "Manager updated order successfully");

        $editedOrder = Order::findById($orderId, 1);
        $this->assert((float)$editedOrder['total'] === 90.00, "Server recalculated new total to ₹90.00");
        $this->assert($editedOrder['payment_method'] === 'Online / UPI', "Payment method updated to Online / UPI");
        $this->assert($editedOrder['customer_name'] === 'EditTest Updated', "Customer details updated");

        // Manager Soft Deletion
        $deleted = Order::softDelete($orderId, 1, 2);
        $this->assert($deleted === true, "Manager soft-deleted order");

        // Active orders query should exclude soft deleted order
        $activeOrder = Order::findById($orderId, 1, false);
        $this->assert($activeOrder === null, "Soft-deleted order no longer returned in active queries");

        // Customer token lookup on deleted order should return null (404)
        $tokenLookup = Order::findByReceiptToken($order['receipt_token']);
        $this->assert($tokenLookup === null, "Soft-deleted order receipt is inaccessible to public tokens");
    }

    private function testAuditLogging(): void {
        echo "\n9. Testing System Audit Logging...\n";

        $logs = AuditLog::getByRestaurant(1, 10);
        $this->assert(!empty($logs), "Audit log records exist for Restaurant 1");

        $actions = array_column($logs, 'action');
        $this->assert(in_array(AuditLog::ACTION_ORDER_CREATE, $actions, true), "ORDER_CREATE action logged in audit trail");
        $this->assert(in_array(AuditLog::ACTION_ORDER_DELETE, $actions, true), "ORDER_DELETE action logged in audit trail");
    }

    private function testReportingAndCsvExport(): void {
        echo "\n10. Testing Reporting Aggregations & Export Queries...\n";

        $todayStats = Order::getStats(1, 'today');
        $this->assert(isset($todayStats['total_sales']), "Aggregated today's total sales computed");
        $this->assert(isset($todayStats['cash_sales']), "Aggregated cash sales computed");
        $this->assert(isset($todayStats['online_sales']), "Aggregated online / UPI sales computed");

        $exportRows = Order::getOrdersForExport(1, '2026-08-01', '2026-08-31');
        $this->assert(is_array($exportRows), "Export data query succeeded");
    }

    private function testLegalAndPolicyPages(): void {
        echo "\n11. Testing Privacy Policy & Terms and Conditions Pages...\n";

        // Web files check
        $privacyPath = ROOT_PATH . '/views/legal/privacy.php';
        $termsPath = ROOT_PATH . '/views/legal/terms.php';
        $loginPath = ROOT_PATH . '/views/auth/login.php';

        $this->assert(file_exists($privacyPath), "Privacy Policy view file exists");
        $this->assert(file_exists($termsPath), "Terms & Conditions view file exists");

        $privacyContent = file_get_contents($privacyPath);
        $this->assert(
            str_contains($privacyContent, 'Our restaurant management and billing software gives restaurant owners complete control over their daily operations'),
            "Privacy Policy contains specified restaurant management description"
        );
        $this->assert(
            str_contains($privacyContent, 'role-based access for Owners, Managers, and Cashiers'),
            "Privacy Policy contains role-based access information"
        );

        $termsContent = file_get_contents($termsPath);
        $this->assert(
            stripos($termsContent, 'WE are a platform only') !== false && stripos($termsContent, 'responsible for your data') !== false,
            "Terms & Conditions contains platform data liability disclaimer"
        );

        $loginContent = file_get_contents($loginPath);
        $this->assert(
            str_contains($loginContent, 'By continuing, you agree to our') &&
            str_contains($loginContent, 'privacy-policy') &&
            str_contains($loginContent, 'terms-and-conditions') &&
            str_contains($loginContent, 'target="_blank"'),
            "Login page has agreement text with links opening in new tab"
        );

        // Android files check
        $androidLegal = ROOT_PATH . '/android/app/src/main/java/com/dinepos/app/presentation/legal/LegalScreens.kt';
        $androidNav = ROOT_PATH . '/android/app/src/main/java/com/dinepos/app/presentation/navigation/NavRoutes.kt';
        $androidLogin = ROOT_PATH . '/android/app/src/main/java/com/dinepos/app/presentation/auth/LoginScreen.kt';

        $this->assert(file_exists($androidLegal), "Android LegalScreens.kt exists");
        $androidLegalContent = file_get_contents($androidLegal);
        $this->assert(
            str_contains($androidLegalContent, "Our restaurant management and billing software gives restaurant owners complete control"),
            "Android PrivacyPolicyScreen contains overview text"
        );
        $this->assert(
            stripos($androidLegalContent, "WE are a platform only") !== false && stripos($androidLegalContent, "responsible for your data") !== false,
            "Android TermsAndConditionsScreen contains platform disclaimer"
        );

        $androidLoginContent = file_get_contents($androidLogin);
        $this->assert(
            str_contains($androidLoginContent, "By continuing you agree to") &&
            str_contains($androidLoginContent, "Privacy Policy") &&
            str_contains($androidLoginContent, "Terms and Conditions"),
            "Android LoginScreen contains clickable legal agreement"
        );
    }
}

// Run test suite
$runner = new TestRunner();
$runner->run();
