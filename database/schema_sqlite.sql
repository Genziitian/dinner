-- SQLite Database Schema for Restaurant Billing & Order Management System

DROP TABLE IF EXISTS `login_attempts`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `daily_order_counters`;
DROP TABLE IF EXISTS `item_variants`;
DROP TABLE IF EXISTS `items`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `restaurants`;

-- 1. Restaurants Table
CREATE TABLE `restaurants` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `name` TEXT NOT NULL,
    `phone` TEXT NULL,
    `address` TEXT NULL,
    `timezone` TEXT NOT NULL DEFAULT 'Asia/Kolkata',
    `shop_type` TEXT NOT NULL DEFAULT 'restaurant',
    `status` TEXT NOT NULL DEFAULT 'active',
    `created_at` TEXT NOT NULL,
    `updated_at` TEXT NOT NULL
);

-- 2. Users Table
CREATE TABLE `users` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `restaurant_id` INTEGER NULL,
    `username` TEXT NOT NULL UNIQUE,
    `password_hash` TEXT NOT NULL,
    `role` TEXT NOT NULL,
    `status` TEXT NOT NULL DEFAULT 'active',
    `failed_login_attempts` INTEGER NOT NULL DEFAULT 0,
    `locked_until` TEXT NULL,
    `created_at` TEXT NOT NULL,
    `updated_at` TEXT NOT NULL,
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
);
CREATE INDEX `idx_users_restaurant` ON `users` (`restaurant_id`);
CREATE INDEX `idx_users_username` ON `users` (`username`);

-- 3. Items Table
CREATE TABLE `items` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `restaurant_id` INTEGER NOT NULL,
    `name` TEXT NOT NULL,
    `item_type` TEXT NOT NULL DEFAULT 'piece',
    `base_unit` TEXT NOT NULL DEFAULT 'piece',
    `active` INTEGER NOT NULL DEFAULT 1,
    `created_at` TEXT NOT NULL,
    `updated_at` TEXT NOT NULL,
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
);
CREATE INDEX `idx_items_restaurant_active` ON `items` (`restaurant_id`, `active`);

-- 4. Item Variants Table
CREATE TABLE `item_variants` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `item_id` INTEGER NOT NULL,
    `variant_name` TEXT NOT NULL,
    `quantity_value` REAL NOT NULL DEFAULT 1.000,
    `quantity_unit` TEXT NOT NULL DEFAULT 'piece',
    `price` REAL NOT NULL,
    `active` INTEGER NOT NULL DEFAULT 1,
    `created_at` TEXT NOT NULL,
    `updated_at` TEXT NOT NULL,
    FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
);
CREATE INDEX `idx_variants_item_active` ON `item_variants` (`item_id`, `active`);

-- 5. Daily Order Counters (Atomic Sequence Tracking)
CREATE TABLE `daily_order_counters` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `restaurant_id` INTEGER NOT NULL,
    `order_date` TEXT NOT NULL,
    `last_order_number` INTEGER NOT NULL DEFAULT 0,
    UNIQUE (`restaurant_id`, `order_date`),
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
);

-- 6. Orders Table
CREATE TABLE `orders` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `restaurant_id` INTEGER NOT NULL,
    `order_number` INTEGER NOT NULL,
    `order_date` TEXT NOT NULL,
    `order_time` TEXT NOT NULL,
    `customer_name` TEXT NULL,
    `customer_phone` TEXT NULL,
    `subtotal` REAL NOT NULL DEFAULT 0.00,
    `total` REAL NOT NULL DEFAULT 0.00,
    `payment_method` TEXT NOT NULL DEFAULT 'Cash',
    `created_by` INTEGER NOT NULL,
    `status` TEXT NOT NULL DEFAULT 'completed',
    `receipt_token_hash` TEXT NOT NULL UNIQUE,
    `deleted_at` TEXT NULL,
    `deleted_by` INTEGER NULL,
    `created_at` TEXT NOT NULL,
    `updated_at` TEXT NOT NULL,
    UNIQUE (`restaurant_id`, `order_date`, `order_number`),
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
);
CREATE INDEX `idx_orders_date_deleted` ON `orders` (`restaurant_id`, `order_date`, `deleted_at`);
CREATE INDEX `idx_orders_token_hash` ON `orders` (`receipt_token_hash`);

-- 7. Order Items (Historical Snapshot)
CREATE TABLE `order_items` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `order_id` INTEGER NOT NULL,
    `item_id` INTEGER NULL,
    `item_name_snapshot` TEXT NOT NULL,
    `variant_name_snapshot` TEXT NOT NULL,
    `quantity` REAL NOT NULL,
    `unit` TEXT NOT NULL,
    `unit_price` REAL NOT NULL,
    `total_price` REAL NOT NULL,
    `created_at` TEXT NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
);
CREATE INDEX `idx_order_items_order` ON `order_items` (`order_id`);

-- 8. Audit Logs Table
CREATE TABLE `audit_logs` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `restaurant_id` INTEGER NULL,
    `user_id` INTEGER NULL,
    `action` TEXT NOT NULL,
    `entity_type` TEXT NOT NULL,
    `entity_id` INTEGER NULL,
    `metadata` TEXT NULL,
    `ip_address` TEXT NULL,
    `user_agent` TEXT NULL,
    `created_at` TEXT NOT NULL
);
CREATE INDEX `idx_audit_restaurant_created` ON `audit_logs` (`restaurant_id`, `created_at`);
CREATE INDEX `idx_audit_user_created` ON `audit_logs` (`user_id`, `created_at`);

-- 9. Login Attempts
CREATE TABLE `login_attempts` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `ip_address` TEXT NOT NULL,
    `username` TEXT NOT NULL,
    `attempted_at` TEXT NOT NULL
);
CREATE INDEX `idx_login_ip_attempt` ON `login_attempts` (`ip_address`, `attempted_at`);
CREATE INDEX `idx_login_user_attempt` ON `login_attempts` (`username`, `attempted_at`);

-- 10. Mill Services
CREATE TABLE IF NOT EXISTS `mill_services` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `restaurant_id` INTEGER NOT NULL,
    `name` TEXT NOT NULL,
    `name_hi` TEXT NULL,
    `rate_per_kg` REAL NOT NULL DEFAULT 0.00,
    `active` INTEGER NOT NULL DEFAULT 1,
    `created_at` TEXT NOT NULL,
    `updated_at` TEXT NOT NULL,
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS `idx_mill_services_rest` ON `mill_services` (`restaurant_id`, `active`);

-- 11. Mill Customers
CREATE TABLE IF NOT EXISTS `mill_customers` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `restaurant_id` INTEGER NOT NULL,
    `name` TEXT NOT NULL,
    `phone` TEXT NOT NULL,
    `created_at` TEXT NOT NULL,
    `updated_at` TEXT NOT NULL,
    UNIQUE(`restaurant_id`, `phone`),
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS `idx_mill_cust_rest` ON `mill_customers` (`restaurant_id`, `phone`);

-- 12. Mill Orders
CREATE TABLE IF NOT EXISTS `mill_orders` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `restaurant_id` INTEGER NOT NULL,
    `order_number` INTEGER NOT NULL,
    `order_date` TEXT NOT NULL,
    `order_time` TEXT NOT NULL,
    `customer_id` INTEGER NULL,
    `customer_name` TEXT NOT NULL,
    `customer_phone` TEXT NOT NULL,
    `service_id` INTEGER NULL,
    `service_name` TEXT NOT NULL,
    `weight_kg` REAL NOT NULL,
    `rate_per_kg` REAL NOT NULL,
    `total_amount` REAL NOT NULL,
    `payment_status` TEXT NOT NULL DEFAULT 'unpaid',
    `payment_method` TEXT NULL DEFAULT 'Cash',
    `status` TEXT NOT NULL DEFAULT 'received',
    `notes` TEXT NULL,
    `created_at` TEXT NOT NULL,
    `updated_at` TEXT NOT NULL,
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `mill_customers` (`id`) ON DELETE SET NULL,
    FOREIGN KEY (`service_id`) REFERENCES `mill_services` (`id`) ON DELETE SET NULL
);
CREATE INDEX IF NOT EXISTS `idx_mill_orders_rest_date` ON `mill_orders` (`restaurant_id`, `order_date`);
CREATE INDEX IF NOT EXISTS `idx_mill_orders_cust` ON `mill_orders` (`restaurant_id`, `customer_phone`);
CREATE INDEX IF NOT EXISTS `idx_mill_orders_status` ON `mill_orders` (`restaurant_id`, `status`);
CREATE INDEX IF NOT EXISTS `idx_mill_orders_payment` ON `mill_orders` (`restaurant_id`, `payment_status`);
