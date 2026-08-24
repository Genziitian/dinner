<?php
/**
 * Database Migration & Seeder CLI Script
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/models/Database.php';

echo "====================================================\n";
echo " Restaurant Billing System - Database Migration\n";
echo "====================================================\n";

try {
    $driver = Database::getDriver();
    echo "Using database driver: [{$driver}]\n";

    $db = Database::getConnection();

    $schemaFile = $driver === 'sqlite' 
        ? ROOT_PATH . '/database/schema_sqlite.sql' 
        : ROOT_PATH . '/database/schema.sql';

    if (!file_exists($schemaFile)) {
        throw new RuntimeException("Schema file not found: {$schemaFile}");
    }

    echo "Running schema: " . basename($schemaFile) . "...\n";
    $schemaSql = file_get_contents($schemaFile);

    if ($driver === 'sqlite') {
        $db->exec($schemaSql);
    } else {
        // Execute multi-query MySQL statements
        $db->exec($schemaSql);
    }

    echo "✓ Tables created successfully.\n";

    echo "Seeding initial demo data...\n";

    // 1. Demo Restaurant
    $now = date('Y-m-d H:i:s');
    $db->prepare("INSERT INTO restaurants (id, name, phone, address, timezone, status, created_at, updated_at) 
                  VALUES (1, 'The Royal Fork', '+91 98765 43210', '102 Flavor Street, Connaught Place, New Delhi', 'Asia/Kolkata', 'active', :now1, :now2)")
       ->execute([':now1' => $now, ':now2' => $now]);

    // 2. Users (password123)
    $passwordHash = password_hash('password123', PASSWORD_DEFAULT);

    $userStmt = $db->prepare("INSERT INTO users (id, restaurant_id, username, password_hash, role, status, failed_login_attempts, locked_until, created_at, updated_at) 
                              VALUES (:id, :restaurant_id, :username, :password_hash, :role, 'active', 0, NULL, :now1, :now2)");

    // Super Admin
    $userStmt->execute([
        ':id' => 1,
        ':restaurant_id' => null,
        ':username' => 'superadmin',
        ':password_hash' => $passwordHash,
        ':role' => 'superadmin',
        ':now1' => $now,
        ':now2' => $now,
    ]);

    // Manager
    $userStmt->execute([
        ':id' => 2,
        ':restaurant_id' => 1,
        ':username' => 'manager01',
        ':password_hash' => $passwordHash,
        ':role' => 'manager',
        ':now1' => $now,
        ':now2' => $now,
    ]);

    // Cashier
    $userStmt->execute([
        ':id' => 3,
        ':restaurant_id' => 1,
        ':username' => 'cashier01',
        ':password_hash' => $passwordHash,
        ':role' => 'cashier',
        ':now1' => $now,
        ':now2' => $now,
    ]);

    // 3. Demo Items & Variants
    $itemStmt = $db->prepare("INSERT INTO items (id, restaurant_id, name, item_type, base_unit, active, created_at, updated_at) 
                              VALUES (:id, 1, :name, :item_type, :base_unit, 1, :now1, :now2)");

    $variantStmt = $db->prepare("INSERT INTO item_variants (id, item_id, variant_name, quantity_value, quantity_unit, price, active, created_at, updated_at) 
                                 VALUES (:id, :item_id, :variant_name, :quantity_value, :quantity_unit, :price, 1, :now1, :now2)");

    // Item 1: Chicken Curry (Portion)
    $itemStmt->execute([':id' => 1, ':name' => 'Chicken Curry', ':item_type' => 'portion', ':base_unit' => 'portion', ':now1' => $now, ':now2' => $now]);
    $variantStmt->execute([':id' => 1, ':item_id' => 1, ':variant_name' => 'Full', ':quantity_value' => 1.0, ':quantity_unit' => 'portion', ':price' => 120.00, ':now1' => $now, ':now2' => $now]);
    $variantStmt->execute([':id' => 2, ':item_id' => 1, ':variant_name' => 'Half', ':quantity_value' => 0.5, ':quantity_unit' => 'portion', ':price' => 60.00, ':now1' => $now, ':now2' => $now]);
    $variantStmt->execute([':id' => 3, ':item_id' => 1, ':variant_name' => 'Quarter', ':quantity_value' => 0.25, ':quantity_unit' => 'portion', ':price' => 30.00, ':now1' => $now, ':now2' => $now]);

    // Item 2: Boiled Egg (Per Piece)
    $itemStmt->execute([':id' => 2, ':name' => 'Boiled Egg', ':item_type' => 'piece', ':base_unit' => 'piece', ':now1' => $now, ':now2' => $now]);
    $variantStmt->execute([':id' => 4, ':item_id' => 2, ':variant_name' => 'Standard', ':quantity_value' => 1.0, ':quantity_unit' => 'piece', ':price' => 30.00, ':now1' => $now, ':now2' => $now]);

    // Item 3: Fresh Egg (Per Piece)
    $itemStmt->execute([':id' => 3, ':name' => 'Egg', ':item_type' => 'piece', ':base_unit' => 'piece', ':now1' => $now, ':now2' => $now]);
    $variantStmt->execute([':id' => 5, ':item_id' => 3, ':variant_name' => 'Standard', ':quantity_value' => 1.0, ':quantity_unit' => 'piece', ':price' => 20.00, ':now1' => $now, ':now2' => $now]);

    // Item 4: Basmati Rice (Weight: kg)
    $itemStmt->execute([':id' => 4, ':name' => 'Rice', ':item_type' => 'weight', ':base_unit' => 'kg', ':now1' => $now, ':now2' => $now]);
    $variantStmt->execute([':id' => 6, ':item_id' => 4, ':variant_name' => '1 kg', ':quantity_value' => 1.0, ':quantity_unit' => 'kg', ':price' => 120.00, ':now1' => $now, ':now2' => $now]);

    // Item 5: Cold Drink (Volume: Liter)
    $itemStmt->execute([':id' => 5, ':name' => 'Cold Drink', ':item_type' => 'weight', ':base_unit' => 'l', ':now1' => $now, ':now2' => $now]);
    $variantStmt->execute([':id' => 7, ':item_id' => 5, ':variant_name' => '1 Liter', ':quantity_value' => 1.0, ':quantity_unit' => 'l', ':price' => 100.00, ':now1' => $now, ':now2' => $now]);

    echo "✓ Seed data inserted successfully.\n\n";
    echo "Demo Credentials:\n";
    echo "----------------------------------------------------\n";
    echo "Super Admin: username: superadmin | password: password123\n";
    echo "Manager:     username: manager01  | password: password123 (Restaurant: The Royal Fork)\n";
    echo "Cashier:     username: cashier01  | password: password123 (Restaurant: The Royal Fork)\n";
    echo "----------------------------------------------------\n";
    echo "Migration Completed Successfully!\n";

} catch (Exception $e) {
    echo "Error during migration: " . $e->getMessage() . "\n";
    exit(1);
}
