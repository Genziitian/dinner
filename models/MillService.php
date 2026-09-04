<?php
/**
 * Mill Service Model
 * Atta & Oil Mill Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class MillService {
    public static function allByRestaurant(int $restaurantId, bool $activeOnly = false): array {
        $sql = "SELECT * FROM mill_services WHERE restaurant_id = :restaurant_id";
        if ($activeOnly) {
            $sql .= " AND active = 1";
        }
        $sql .= " ORDER BY id ASC";
        return Database::fetchAll($sql, [':restaurant_id' => $restaurantId]);
    }

    public static function findById(int $id): ?array {
        return Database::fetchOne(
            "SELECT * FROM mill_services WHERE id = :id LIMIT 1",
            [':id' => $id]
        );
    }

    public static function create(array $data): int {
        $now = date('Y-m-d H:i:s');
        $sql = "INSERT INTO mill_services (restaurant_id, name, name_hi, rate_per_kg, active, created_at, updated_at) 
                VALUES (:restaurant_id, :name, :name_hi, :rate_per_kg, :active, :created_at, :updated_at)";
        Database::execute($sql, [
            ':restaurant_id' => (int)$data['restaurant_id'],
            ':name'          => trim($data['name']),
            ':name_hi'       => !empty($data['name_hi']) ? trim($data['name_hi']) : null,
            ':rate_per_kg'   => (float)$data['rate_per_kg'],
            ':active'        => isset($data['active']) ? (int)$data['active'] : 1,
            ':created_at'    => $now,
            ':updated_at'    => $now,
        ]);
        return (int)Database::lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $now = date('Y-m-d H:i:s');
        $sql = "UPDATE mill_services SET 
                name = :name, 
                name_hi = :name_hi, 
                rate_per_kg = :rate_per_kg, 
                active = :active, 
                updated_at = :updated_at 
                WHERE id = :id";
        Database::execute($sql, [
            ':id'          => $id,
            ':name'        => trim($data['name']),
            ':name_hi'     => !empty($data['name_hi']) ? trim($data['name_hi']) : null,
            ':rate_per_kg' => (float)$data['rate_per_kg'],
            ':active'      => isset($data['active']) ? (int)$data['active'] : 1,
            ':updated_at'  => $now,
        ]);
        return true;
    }

    public static function toggle(int $id): bool {
        $service = self::findById($id);
        if (!$service) return false;
        $newActive = $service['active'] ? 0 : 1;
        $now = date('Y-m-d H:i:s');
        Database::execute(
            "UPDATE mill_services SET active = :active, updated_at = :updated_at WHERE id = :id",
            [':active' => $newActive, ':updated_at' => $now, ':id' => $id]
        );
        return true;
    }

    public static function toggleActive(int $id): bool {
        return self::toggle($id);
    }

    /**
     * Seeds default mill services if none exist for this mill
     */
    public static function seedDefaults(int $restaurantId): void {
        $existing = self::allByRestaurant($restaurantId);
        if (!empty($existing)) {
            return;
        }

        $defaults = [
            ['name' => 'Wheat grinding', 'name_hi' => 'गेहूं पिसाई', 'rate_per_kg' => 5.00],
            ['name' => 'Daliya', 'name_hi' => 'दलिया', 'rate_per_kg' => 8.00],
            ['name' => 'Oil extraction', 'name_hi' => 'तेल पिराई', 'rate_per_kg' => 25.00],
            ['name' => 'Rice grinding', 'name_hi' => 'चावल पिसाई', 'rate_per_kg' => 7.00],
            ['name' => 'Other custom services', 'name_hi' => 'अन्य सेवा', 'rate_per_kg' => 10.00],
        ];

        foreach ($defaults as $d) {
            self::create([
                'restaurant_id' => $restaurantId,
                'name'          => $d['name'],
                'name_hi'       => $d['name_hi'],
                'rate_per_kg'   => $d['rate_per_kg'],
                'active'        => 1,
            ]);
        }
    }
}
