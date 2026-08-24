<?php
/**
 * Item Model
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/ItemVariant.php';

class Item {
    public const TYPE_PIECE = 'piece';
    public const TYPE_WEIGHT = 'weight';
    public const TYPE_PORTION = 'portion';

    public static function findById(int $id, int $restaurantId): ?array {
        $item = Database::fetchOne(
            "SELECT * FROM items WHERE id = :id AND restaurant_id = :restaurant_id LIMIT 1",
            [':id' => $id, ':restaurant_id' => $restaurantId]
        );
        if ($item) {
            $item['variants'] = ItemVariant::getByItemId($id);
        }
        return $item;
    }

    public static function allByRestaurant(int $restaurantId, bool $activeOnly = false): array {
        $sql = "SELECT * FROM items WHERE restaurant_id = :restaurant_id";
        if ($activeOnly) {
            $sql .= " AND active = 1";
        }
        $sql .= " ORDER BY name ASC";
        
        $items = Database::fetchAll($sql, [':restaurant_id' => $restaurantId]);
        
        // Attach active variants
        foreach ($items as &$item) {
            $item['variants'] = ItemVariant::getByItemId((int)$item['id'], $activeOnly);
            // Compute display price helper (e.g. "From ₹30", "₹30 / piece", "₹120 / kg")
            $item['display_price'] = self::formatDisplayPrice($item);
        }
        unset($item);

        return $items;
    }

    public static function formatDisplayPrice(array $item): string {
        $variants = $item['variants'] ?? [];
        if (empty($variants)) {
            return '₹0.00';
        }

        if ($item['item_type'] === self::TYPE_PORTION) {
            $prices = array_column($variants, 'price');
            $minPrice = min($prices);
            return 'From ₹' . number_format((float)$minPrice, 0);
        }

        if ($item['item_type'] === self::TYPE_WEIGHT) {
            $first = $variants[0];
            return '₹' . number_format((float)$first['price'], 0) . ' / ' . $item['base_unit'];
        }

        // Per piece
        $first = $variants[0];
        return '₹' . number_format((float)$first['price'], 0) . ' / piece';
    }

    public static function create(int $restaurantId, array $data, array $variants): int {
        $now = date('Y-m-d H:i:s');
        $sql = "INSERT INTO items (restaurant_id, name, item_type, base_unit, active, created_at, updated_at) 
                VALUES (:restaurant_id, :name, :item_type, :base_unit, :active, :created_at, :updated_at)";
        
        Database::execute($sql, [
            ':restaurant_id' => $restaurantId,
            ':name' => trim($data['name']),
            ':item_type' => $data['item_type'] ?? self::TYPE_PIECE,
            ':base_unit' => trim($data['base_unit'] ?? 'piece'),
            ':active' => isset($data['active']) ? (int)$data['active'] : 1,
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);

        $itemId = (int)Database::lastInsertId();

        // Insert variants
        foreach ($variants as $variant) {
            if (!empty($variant['price']) || isset($variant['price'])) {
                ItemVariant::create($itemId, $variant);
            }
        }

        return $itemId;
    }

    public static function update(int $id, int $restaurantId, array $data, ?array $variants = null): bool {
        $item = self::findById($id, $restaurantId);
        if (!$item) return false;

        $now = date('Y-m-d H:i:s');
        $sql = "UPDATE items SET 
                name = :name, 
                item_type = :item_type, 
                base_unit = :base_unit, 
                active = :active, 
                updated_at = :updated_at 
                WHERE id = :id AND restaurant_id = :restaurant_id";
        
        Database::execute($sql, [
            ':id' => $id,
            ':restaurant_id' => $restaurantId,
            ':name' => trim($data['name']),
            ':item_type' => $data['item_type'] ?? $item['item_type'],
            ':base_unit' => trim($data['base_unit'] ?? $item['base_unit']),
            ':active' => isset($data['active']) ? (int)$data['active'] : $item['active'],
            ':updated_at' => $now,
        ]);

        if ($variants !== null) {
            // Replace or sync variants
            ItemVariant::deleteByItemId($id);
            foreach ($variants as $variant) {
                if (isset($variant['price']) && $variant['price'] !== '') {
                    ItemVariant::create($id, $variant);
                }
            }
        }

        return true;
    }

    public static function toggleActive(int $id, int $restaurantId): bool {
        $item = self::findById($id, $restaurantId);
        if (!$item) return false;
        $newActive = $item['active'] == 1 ? 0 : 1;
        $now = date('Y-m-d H:i:s');
        Database::execute(
            "UPDATE items SET active = :active, updated_at = :updated_at WHERE id = :id AND restaurant_id = :restaurant_id",
            [':active' => $newActive, ':updated_at' => $now, ':id' => $id, ':restaurant_id' => $restaurantId]
        );
        return true;
    }
}
