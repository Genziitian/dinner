<?php
/**
 * Mill Customer Model
 * Atta & Oil Mill Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class MillCustomer {
    public static function findById(int $id): ?array {
        return Database::fetchOne(
            "SELECT * FROM mill_customers WHERE id = :id LIMIT 1",
            [':id' => $id]
        );
    }

    public static function findByPhone(int $restaurantId, string $phone): ?array {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        return Database::fetchOne(
            "SELECT * FROM mill_customers WHERE restaurant_id = :restaurant_id AND phone = :phone LIMIT 1",
            [':restaurant_id' => $restaurantId, ':phone' => $cleanPhone]
        );
    }

    public static function findOrCreate(int $restaurantId, string $name, string $phone): array {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $cleanName = trim($name);

        $existing = self::findByPhone($restaurantId, $cleanPhone);
        if ($existing) {
            // Update name if changed
            if ($existing['name'] !== $cleanName && !empty($cleanName)) {
                $now = date('Y-m-d H:i:s');
                Database::execute(
                    "UPDATE mill_customers SET name = :name, updated_at = :updated_at WHERE id = :id",
                    [':name' => $cleanName, ':updated_at' => $now, ':id' => $existing['id']]
                );
                $existing['name'] = $cleanName;
            }
            return $existing;
        }

        $now = date('Y-m-d H:i:s');
        Database::execute(
            "INSERT INTO mill_customers (restaurant_id, name, phone, created_at, updated_at) 
             VALUES (:restaurant_id, :name, :phone, :created_at, :updated_at)",
            [
                ':restaurant_id' => $restaurantId,
                ':name'          => $cleanName,
                ':phone'         => $cleanPhone,
                ':created_at'    => $now,
                ':updated_at'    => $now,
            ]
        );
        $id = (int)Database::lastInsertId();
        return [
            'id'            => $id,
            'restaurant_id' => $restaurantId,
            'name'          => $cleanName,
            'phone'         => $cleanPhone,
            'created_at'    => $now,
            'updated_at'    => $now,
        ];
    }

    public static function search(int $restaurantId, string $query): array {
        $trimmed = trim($query);
        if (empty($trimmed)) {
            return [];
        }
        $sql = "SELECT id, name, phone FROM mill_customers 
                WHERE restaurant_id = :restaurant_id 
                  AND (name LIKE :q_name OR phone LIKE :q_phone) 
                ORDER BY name ASC LIMIT 10";
        return Database::fetchAll($sql, [
            ':restaurant_id' => $restaurantId,
            ':q_name'        => '%' . $trimmed . '%',
            ':q_phone'       => '%' . $trimmed . '%',
        ]);
    }

    public static function allByRestaurant(int $restaurantId, string $search = ''): array {
        $params = [':restaurant_id' => $restaurantId];
        $sql = "SELECT c.*, 
                       COUNT(o.id) AS total_orders,
                       COALESCE(SUM(o.total_amount), 0) AS lifetime_amount,
                       COALESCE(SUM(CASE WHEN o.payment_status = 'unpaid' THEN o.total_amount ELSE 0 END), 0) AS unpaid_amount
                FROM mill_customers c
                LEFT JOIN mill_orders o ON c.phone = o.customer_phone AND c.restaurant_id = o.restaurant_id
                WHERE c.restaurant_id = :restaurant_id";

        if (!empty($search)) {
            $sql .= " AND (c.name LIKE :search OR c.phone LIKE :search)";
            $params[':search'] = '%' . trim($search) . '%';
        }

        $sql .= " GROUP BY c.id ORDER BY c.updated_at DESC";
        return Database::fetchAll($sql, $params);
    }

    public static function getCustomerStats(int $restaurantId, string $phone): array {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $sql = "SELECT 
                    COUNT(id) AS total_orders,
                    COALESCE(SUM(total_amount), 0) AS lifetime_spent,
                    COALESCE(SUM(CASE WHEN payment_status = 'unpaid' THEN total_amount ELSE 0 END), 0) AS pending_due,
                    COALESCE(SUM(weight_kg), 0) AS total_weight_kg
                FROM mill_orders 
                WHERE restaurant_id = :restaurant_id AND customer_phone = :phone";
        return Database::fetchOne($sql, [':restaurant_id' => $restaurantId, ':phone' => $cleanPhone]) ?? [];
    }
}
