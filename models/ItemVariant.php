<?php
/**
 * Item Variant Model
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class ItemVariant {
    public static function findById(int $id): ?array {
        return Database::fetchOne(
            "SELECT * FROM item_variants WHERE id = :id LIMIT 1",
            [':id' => $id]
        );
    }

    public static function getByItemId(int $itemId, bool $activeOnly = false): array {
        $sql = "SELECT * FROM item_variants WHERE item_id = :item_id";
        if ($activeOnly) {
            $sql .= " AND active = 1";
        }
        $sql .= " ORDER BY price ASC, id ASC";
        return Database::fetchAll($sql, [':item_id' => $itemId]);
    }

    public static function create(int $itemId, array $data): int {
        $now = date('Y-m-d H:i:s');
        $sql = "INSERT INTO item_variants (item_id, variant_name, quantity_value, quantity_unit, price, active, created_at, updated_at) 
                VALUES (:item_id, :variant_name, :quantity_value, :quantity_unit, :price, :active, :created_at, :updated_at)";
        Database::execute($sql, [
            ':item_id' => $itemId,
            ':variant_name' => trim($data['variant_name'] ?? 'Standard'),
            ':quantity_value' => (float)($data['quantity_value'] ?? 1.0),
            ':quantity_unit' => trim($data['quantity_unit'] ?? 'piece'),
            ':price' => (float)$data['price'],
            ':active' => isset($data['active']) ? (int)$data['active'] : 1,
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);
        return (int)Database::lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $now = date('Y-m-d H:i:s');
        $sql = "UPDATE item_variants SET 
                variant_name = :variant_name, 
                quantity_value = :quantity_value, 
                quantity_unit = :quantity_unit, 
                price = :price, 
                active = :active, 
                updated_at = :updated_at 
                WHERE id = :id";
        Database::execute($sql, [
            ':id' => $id,
            ':variant_name' => trim($data['variant_name'] ?? 'Standard'),
            ':quantity_value' => (float)($data['quantity_value'] ?? 1.0),
            ':quantity_unit' => trim($data['quantity_unit'] ?? 'piece'),
            ':price' => (float)$data['price'],
            ':active' => isset($data['active']) ? (int)$data['active'] : 1,
            ':updated_at' => $now,
        ]);
        return true;
    }

    public static function deleteByItemId(int $itemId): void {
        Database::execute("DELETE FROM item_variants WHERE item_id = :item_id", [':item_id' => $itemId]);
    }
}
