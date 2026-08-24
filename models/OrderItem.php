<?php
/**
 * Order Item Model
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class OrderItem {
    public static function getByOrderId(int $orderId): array {
        return Database::fetchAll(
            "SELECT * FROM order_items WHERE order_id = :order_id ORDER BY id ASC",
            [':order_id' => $orderId]
        );
    }

    public static function create(int $orderId, array $data): int {
        $now = date('Y-m-d H:i:s');
        $sql = "INSERT INTO order_items (order_id, item_id, item_name_snapshot, variant_name_snapshot, quantity, unit, unit_price, total_price, created_at) 
                VALUES (:order_id, :item_id, :item_name, :variant_name, :quantity, :unit, :unit_price, :total_price, :created_at)";
        
        Database::execute($sql, [
            ':order_id' => $orderId,
            ':item_id' => !empty($data['item_id']) ? (int)$data['item_id'] : null,
            ':item_name' => trim($data['item_name_snapshot']),
            ':variant_name' => trim($data['variant_name_snapshot'] ?? 'Standard'),
            ':quantity' => (float)$data['quantity'],
            ':unit' => trim($data['unit'] ?? 'piece'),
            ':unit_price' => (float)$data['unit_price'],
            ':total_price' => (float)$data['total_price'],
            ':created_at' => $now,
        ]);

        return (int)Database::lastInsertId();
    }
}
