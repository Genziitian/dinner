<?php
/**
 * Daily Order Counter Model (Atomic Sequence Tracking)
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class DailyCounter {
    /**
     * Atomically get and increment next order number for a restaurant and date.
     * MUST be called inside a Database transaction!
     */
    public static function getNextOrderNumber(int $restaurantId, string $orderDate): int {
        $driver = Database::getDriver();

        if ($driver === 'sqlite') {
            // SQLite UPSERT
            $sql = "INSERT INTO daily_order_counters (restaurant_id, order_date, last_order_number) 
                    VALUES (:restaurant_id, :order_date, 1) 
                    ON CONFLICT(restaurant_id, order_date) 
                    DO UPDATE SET last_order_number = last_order_number + 1";
            Database::execute($sql, [
                ':restaurant_id' => $restaurantId,
                ':order_date' => $orderDate,
            ]);

            // Fetch current updated value
            $val = Database::fetchValue(
                "SELECT last_order_number FROM daily_order_counters WHERE restaurant_id = :restaurant_id AND order_date = :order_date",
                [':restaurant_id' => $restaurantId, ':order_date' => $orderDate]
            );

            return (int)$val;
        } else {
            // MySQL / MariaDB row-level lock or UPSERT with LAST_INSERT_ID
            $upsertSql = "INSERT INTO daily_order_counters (restaurant_id, order_date, last_order_number) 
                          VALUES (:restaurant_id, :order_date, 1) 
                          ON DUPLICATE KEY UPDATE last_order_number = last_order_number + 1";
            Database::execute($upsertSql, [
                ':restaurant_id' => $restaurantId,
                ':order_date' => $orderDate,
            ]);

            // Lock and read guaranteed atomic sequence
            $row = Database::fetchOne(
                "SELECT last_order_number FROM daily_order_counters WHERE restaurant_id = :restaurant_id AND order_date = :order_date FOR UPDATE",
                [':restaurant_id' => $restaurantId, ':order_date' => $orderDate]
            );

            return (int)($row['last_order_number'] ?? 1);
        }
    }

    /**
     * Preview the next order number without incrementing (for UI display only)
     */
    public static function previewNextOrderNumber(int $restaurantId, string $orderDate): int {
        $val = Database::fetchValue(
            "SELECT last_order_number FROM daily_order_counters WHERE restaurant_id = :restaurant_id AND order_date = :order_date",
            [':restaurant_id' => $restaurantId, ':order_date' => $orderDate]
        );
        return $val ? ((int)$val + 1) : 1;
    }
}
