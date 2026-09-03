<?php
/**
 * Atta Mill Module Database Migration
 */

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/models/Database.php';

echo "Running Mill Module Migration...\n";

try {
    $driver = Database::getDriver();
    $db = Database::getConnection();

    // 1. Add shop_type to restaurants if not exists
    if ($driver === 'sqlite') {
        $cols = $db->query("PRAGMA table_info(restaurants)")->fetchAll(PDO::FETCH_ASSOC);
        $colNames = array_column($cols, 'name');
        if (!in_array('shop_type', $colNames, true)) {
            $db->exec("ALTER TABLE restaurants ADD COLUMN shop_type TEXT NOT NULL DEFAULT 'restaurant'");
            echo "Added shop_type column to restaurants (sqlite).\n";
        } else {
            echo "shop_type column already exists in restaurants.\n";
        }
    } else {
        $cols = $db->query("SHOW COLUMNS FROM restaurants LIKE 'shop_type'")->fetchAll(PDO::FETCH_ASSOC);
        if (empty($cols)) {
            $db->exec("ALTER TABLE `restaurants` ADD COLUMN `shop_type` VARCHAR(32) NOT NULL DEFAULT 'restaurant' AFTER `timezone`");
            echo "Added shop_type column to restaurants (mysql).\n";
        } else {
            echo "shop_type column already exists in restaurants.\n";
        }
    }

    // 2. Create mill_services table
    if ($driver === 'sqlite') {
        $db->exec("
            CREATE TABLE IF NOT EXISTS mill_services (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                restaurant_id INTEGER NOT NULL,
                name TEXT NOT NULL,
                name_hi TEXT NULL,
                rate_per_kg REAL NOT NULL DEFAULT 0.00,
                active INTEGER NOT NULL DEFAULT 1,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL,
                FOREIGN KEY (restaurant_id) REFERENCES restaurants (id) ON DELETE CASCADE
            );
            CREATE INDEX IF NOT EXISTS idx_mill_services_rest ON mill_services (restaurant_id, active);
        ");
    } else {
        $db->exec("
            CREATE TABLE IF NOT EXISTS `mill_services` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `restaurant_id` INT NOT NULL,
                `name` VARCHAR(150) NOT NULL,
                `name_hi` VARCHAR(150) NULL,
                `rate_per_kg` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `active` TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                INDEX `idx_mill_services_rest` (`restaurant_id`, `active`),
                CONSTRAINT `fk_mill_services_rest` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }
    echo "mill_services table verified.\n";

    // 3. Create mill_customers table
    if ($driver === 'sqlite') {
        $db->exec("
            CREATE TABLE IF NOT EXISTS mill_customers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                restaurant_id INTEGER NOT NULL,
                name TEXT NOT NULL,
                phone TEXT NOT NULL,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL,
                UNIQUE(restaurant_id, phone),
                FOREIGN KEY (restaurant_id) REFERENCES restaurants (id) ON DELETE CASCADE
            );
            CREATE INDEX IF NOT EXISTS idx_mill_cust_rest ON mill_customers (restaurant_id, phone);
        ");
    } else {
        $db->exec("
            CREATE TABLE IF NOT EXISTS `mill_customers` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `restaurant_id` INT NOT NULL,
                `name` VARCHAR(150) NOT NULL,
                `phone` VARCHAR(20) NOT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                UNIQUE KEY `uniq_mill_cust` (`restaurant_id`, `phone`),
                INDEX `idx_mill_cust_phone` (`restaurant_id`, `phone`),
                CONSTRAINT `fk_mill_cust_rest` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }
    echo "mill_customers table verified.\n";

    // 4. Create mill_orders table
    if ($driver === 'sqlite') {
        $db->exec("
            CREATE TABLE IF NOT EXISTS mill_orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                restaurant_id INTEGER NOT NULL,
                order_number INTEGER NOT NULL,
                order_date TEXT NOT NULL,
                order_time TEXT NOT NULL,
                customer_id INTEGER NULL,
                customer_name TEXT NOT NULL,
                customer_phone TEXT NOT NULL,
                service_id INTEGER NULL,
                service_name TEXT NOT NULL,
                weight_kg REAL NOT NULL,
                rate_per_kg REAL NOT NULL,
                total_amount REAL NOT NULL,
                payment_status TEXT NOT NULL DEFAULT 'unpaid',
                payment_method TEXT NULL DEFAULT 'Cash',
                status TEXT NOT NULL DEFAULT 'received',
                notes TEXT NULL,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL,
                FOREIGN KEY (restaurant_id) REFERENCES restaurants (id) ON DELETE CASCADE,
                FOREIGN KEY (customer_id) REFERENCES mill_customers (id) ON DELETE SET NULL,
                FOREIGN KEY (service_id) REFERENCES mill_services (id) ON DELETE SET NULL
            );
            CREATE INDEX IF NOT EXISTS idx_mill_orders_rest_date ON mill_orders (restaurant_id, order_date);
            CREATE INDEX IF NOT EXISTS idx_mill_orders_cust ON mill_orders (restaurant_id, customer_phone);
            CREATE INDEX IF NOT EXISTS idx_mill_orders_status ON mill_orders (restaurant_id, status);
            CREATE INDEX IF NOT EXISTS idx_mill_orders_payment ON mill_orders (restaurant_id, payment_status);
        ");
    } else {
        $db->exec("
            CREATE TABLE IF NOT EXISTS `mill_orders` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `restaurant_id` INT NOT NULL,
                `order_number` INT NOT NULL,
                `order_date` DATE NOT NULL,
                `order_time` TIME NOT NULL,
                `customer_id` INT NULL,
                `customer_name` VARCHAR(150) NOT NULL,
                `customer_phone` VARCHAR(20) NOT NULL,
                `service_id` INT NULL,
                `service_name` VARCHAR(150) NOT NULL,
                `weight_kg` DECIMAL(10,3) NOT NULL,
                `rate_per_kg` DECIMAL(10,2) NOT NULL,
                `total_amount` DECIMAL(10,2) NOT NULL,
                `payment_status` ENUM('paid', 'unpaid') NOT NULL DEFAULT 'unpaid',
                `payment_method` VARCHAR(20) NULL DEFAULT 'Cash',
                `status` ENUM('received', 'processing', 'ready', 'delivered', 'cancelled') NOT NULL DEFAULT 'received',
                `notes` TEXT NULL,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                INDEX `idx_mill_orders_rest_date` (`restaurant_id`, `order_date`),
                INDEX `idx_mill_orders_cust` (`restaurant_id`, `customer_phone`),
                INDEX `idx_mill_orders_status` (`restaurant_id`, `status`),
                INDEX `idx_mill_orders_payment` (`restaurant_id`, `payment_status`),
                CONSTRAINT `fk_mill_orders_rest` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }
    echo "mill_orders table verified.\n";

    echo "Mill migration completed successfully.\n";
} catch (Throwable $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
