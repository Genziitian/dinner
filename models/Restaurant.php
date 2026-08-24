<?php
/**
 * Restaurant Model
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class Restaurant {
    public static function findById(int $id): ?array {
        return Database::fetchOne(
            "SELECT * FROM restaurants WHERE id = :id LIMIT 1",
            [':id' => $id]
        );
    }

    public static function all(bool $activeOnly = false): array {
        $sql = "SELECT * FROM restaurants";
        if ($activeOnly) {
            $sql .= " WHERE status = 'active'";
        }
        $sql .= " ORDER BY id ASC";
        return Database::fetchAll($sql);
    }

    public static function create(array $data): int {
        $now = date('Y-m-d H:i:s');
        $sql = "INSERT INTO restaurants (name, phone, address, timezone, status, created_at, updated_at) 
                VALUES (:name, :phone, :address, :timezone, :status, :created_at, :updated_at)";
        Database::execute($sql, [
            ':name' => trim($data['name']),
            ':phone' => !empty($data['phone']) ? trim($data['phone']) : null,
            ':address' => !empty($data['address']) ? trim($data['address']) : null,
            ':timezone' => !empty($data['timezone']) ? trim($data['timezone']) : 'Asia/Kolkata',
            ':status' => $data['status'] ?? 'active',
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);
        return (int)Database::lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $now = date('Y-m-d H:i:s');
        $sql = "UPDATE restaurants SET 
                name = :name, 
                phone = :phone, 
                address = :address, 
                timezone = :timezone, 
                status = :status, 
                updated_at = :updated_at 
                WHERE id = :id";
        Database::execute($sql, [
            ':id' => $id,
            ':name' => trim($data['name']),
            ':phone' => !empty($data['phone']) ? trim($data['phone']) : null,
            ':address' => !empty($data['address']) ? trim($data['address']) : null,
            ':timezone' => !empty($data['timezone']) ? trim($data['timezone']) : 'Asia/Kolkata',
            ':status' => $data['status'] ?? 'active',
            ':updated_at' => $now,
        ]);
        return true;
    }

    public static function toggleStatus(int $id): bool {
        $rest = self::findById($id);
        if (!$rest) return false;
        $newStatus = $rest['status'] === 'active' ? 'inactive' : 'active';
        $now = date('Y-m-d H:i:s');
        Database::execute(
            "UPDATE restaurants SET status = :status, updated_at = :updated_at WHERE id = :id",
            [':status' => $newStatus, ':updated_at' => $now, ':id' => $id]
        );
        return true;
    }
}
