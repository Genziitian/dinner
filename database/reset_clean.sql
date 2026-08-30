-- ==========================================================
-- DinePOS - Wipe & Reset All Data Clean (Purge Fake Records)
-- Execute this script in phpMyAdmin to remove all test data.
-- ==========================================================

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `order_items`;
TRUNCATE TABLE `orders`;
TRUNCATE TABLE `daily_order_counters`;
TRUNCATE TABLE `item_variants`;
TRUNCATE TABLE `items`;
TRUNCATE TABLE `audit_logs`;
TRUNCATE TABLE `login_attempts`;
TRUNCATE TABLE `users`;
TRUNCATE TABLE `restaurants`;

SET FOREIGN_KEY_CHECKS = 1;

-- All dummy restaurants, demo users, and test orders are wiped.
-- Now visit your website URL in the browser to set up your real Master Super Admin!
