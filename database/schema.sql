-- MySQL / MariaDB Database Schema for Restaurant Billing & Order Management System

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `login_attempts`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `daily_order_counters`;
DROP TABLE IF EXISTS `item_variants`;
DROP TABLE IF EXISTS `items`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `restaurants`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Restaurants Table
CREATE TABLE `restaurants` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) NULL,
    `address` TEXT NULL,
    `timezone` VARCHAR(64) NOT NULL DEFAULT 'Asia/Kolkata',
    `shop_type` VARCHAR(32) NOT NULL DEFAULT 'restaurant',
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Users Table
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` INT NULL,
    `username` VARCHAR(10) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('superadmin', 'manager', 'cashier') NOT NULL,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `failed_login_attempts` INT NOT NULL DEFAULT 0,
    `locked_until` DATETIME NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    INDEX `idx_users_restaurant` (`restaurant_id`),
    INDEX `idx_users_username` (`username`),
    CONSTRAINT `fk_users_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Items Table
CREATE TABLE `items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` INT NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `item_type` ENUM('piece', 'weight', 'portion') NOT NULL DEFAULT 'piece',
    `base_unit` VARCHAR(20) NOT NULL DEFAULT 'piece',
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    INDEX `idx_items_restaurant_active` (`restaurant_id`, `active`),
    CONSTRAINT `fk_items_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Item Variants Table
CREATE TABLE `item_variants` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `item_id` INT NOT NULL,
    `variant_name` VARCHAR(100) NOT NULL,
    `quantity_value` DECIMAL(10,3) NOT NULL DEFAULT 1.000,
    `quantity_unit` VARCHAR(20) NOT NULL DEFAULT 'piece',
    `price` DECIMAL(10,2) NOT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    INDEX `idx_variants_item_active` (`item_id`, `active`),
    CONSTRAINT `fk_variants_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Daily Order Counters (Atomic Sequence Tracking with Lock)
CREATE TABLE `daily_order_counters` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` INT NOT NULL,
    `order_date` DATE NOT NULL,
    `last_order_number` INT NOT NULL DEFAULT 0,
    UNIQUE KEY `uniq_rest_date` (`restaurant_id`, `order_date`),
    CONSTRAINT `fk_counters_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Orders Table
CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` INT NOT NULL,
    `order_number` INT NOT NULL,
    `order_date` DATE NOT NULL,
    `order_time` TIME NOT NULL,
    `customer_name` VARCHAR(100) NULL,
    `customer_phone` VARCHAR(20) NULL,
    `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `payment_method` ENUM('Cash', 'Online', 'QR / UPI') NOT NULL DEFAULT 'Cash',
    `created_by` INT NOT NULL,
    `status` ENUM('completed', 'cancelled') NOT NULL DEFAULT 'completed',
    `receipt_token_hash` VARCHAR(64) NOT NULL UNIQUE,
    `deleted_at` DATETIME NULL,
    `deleted_by` INT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    UNIQUE KEY `uniq_rest_order_daily` (`restaurant_id`, `order_date`, `order_number`),
    INDEX `idx_orders_date_deleted` (`restaurant_id`, `order_date`, `deleted_at`),
    INDEX `idx_orders_token_hash` (`receipt_token_hash`),
    CONSTRAINT `fk_orders_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_orders_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Order Items (Historical Snapshot)
CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `item_id` INT NULL,
    `item_name_snapshot` VARCHAR(150) NOT NULL,
    `variant_name_snapshot` VARCHAR(100) NOT NULL,
    `quantity` DECIMAL(10,3) NOT NULL,
    `unit` VARCHAR(20) NOT NULL,
    `unit_price` DECIMAL(10,2) NOT NULL,
    `total_price` DECIMAL(10,2) NOT NULL,
    `created_at` DATETIME NOT NULL,
    INDEX `idx_order_items_order` (`order_id`),
    CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Audit Logs Table
CREATE TABLE `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` INT NULL,
    `user_id` INT NULL,
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` INT NULL,
    `metadata` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL,
    INDEX `idx_audit_restaurant_created` (`restaurant_id`, `created_at`),
    INDEX `idx_audit_user_created` (`user_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Login Rate Limiting & Failed Attempt Tracker
CREATE TABLE `login_attempts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ip_address` VARCHAR(45) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `attempted_at` DATETIME NOT NULL,
    INDEX `idx_login_ip_attempt` (`ip_address`, `attempted_at`),
    INDEX `idx_login_user_attempt` (`username`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Mill Services
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

-- 11. Mill Customers
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

-- 12. Mill Orders
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
