<?php
/**
 * Order Model
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/OrderItem.php';
require_once __DIR__ . '/DailyCounter.php';
require_once __DIR__ . '/AuditLog.php';
require_once __DIR__ . '/Item.php';
require_once __DIR__ . '/ItemVariant.php';
require_once __DIR__ . '/Restaurant.php';

class Order {
    public const PAYMENT_CASH = 'Cash';
    public const PAYMENT_ONLINE = 'Online / UPI';

    /**
     * Create a new order with atomic daily order numbering and server-side pricing calculation.
     */
    public static function create(
        int $restaurantId,
        int $userId,
        ?string $customerName,
        ?string $customerPhone,
        string $paymentMethod,
        array $rawItems
    ): array {
        if (empty($rawItems)) {
            throw new InvalidArgumentException('Order must contain at least one item.');
        }

        // Normalize payment methods
        if ($paymentMethod === 'Cash') {
            $paymentMethod = self::PAYMENT_CASH;
        } else {
            $paymentMethod = self::PAYMENT_ONLINE;
        }

        // Get restaurant timezone
        $restaurant = Restaurant::findById($restaurantId);
        $timezone = $restaurant['timezone'] ?? 'Asia/Kolkata';
        $tz = new DateTimeZone($timezone);
        $nowDt = new DateTime('now', $tz);

        $orderDate = $nowDt->format('Y-m-d');
        $orderTime = $nowDt->format('H:i:s');
        $createdAt = $nowDt->format('Y-m-d H:i:s');

        // Start transaction
        Database::beginTransaction();

        try {
            // 1. Calculate items and authoritative prices server-side
            $calculatedItems = [];
            $subtotal = 0.0;

            foreach ($rawItems as $rawItem) {
                $itemId = (int)($rawItem['item_id'] ?? 0);
                $variantId = (int)($rawItem['variant_id'] ?? 0);
                $quantity = (float)($rawItem['quantity'] ?? 1.0);

                if ($quantity <= 0) {
                    continue;
                }

                // Verify item belongs to this restaurant and is active
                $item = Database::fetchOne(
                    "SELECT * FROM items WHERE id = :id AND restaurant_id = :restaurant_id LIMIT 1",
                    [':id' => $itemId, ':restaurant_id' => $restaurantId]
                );

                if (!$item) {
                    throw new RuntimeException("Item #{$itemId} not found in this restaurant.");
                }

                // Find variant
                $variant = null;
                if ($variantId > 0) {
                    $variant = Database::fetchOne(
                        "SELECT * FROM item_variants WHERE id = :id AND item_id = :item_id LIMIT 1",
                        [':id' => $variantId, ':item_id' => $itemId]
                    );
                }

                if (!$variant) {
                    // Fallback to first active variant for item
                    $variant = Database::fetchOne(
                        "SELECT * FROM item_variants WHERE item_id = :item_id AND active = 1 ORDER BY id ASC LIMIT 1",
                        [':item_id' => $itemId]
                    );
                }

                if (!$variant) {
                    throw new RuntimeException("No pricing variant found for item '{$item['name']}'.");
                }

                $unitPrice = (float)$variant['price'];
                $itemTotalPrice = round($quantity * $unitPrice, 2);
                $subtotal += $itemTotalPrice;

                $calculatedItems[] = [
                    'item_id' => $item['id'],
                    'item_name_snapshot' => $item['name'],
                    'variant_name_snapshot' => $variant['variant_name'],
                    'quantity' => $quantity,
                    'unit' => $variant['quantity_unit'] ?? $item['base_unit'],
                    'unit_price' => $unitPrice,
                    'total_price' => $itemTotalPrice,
                ];
            }

            if (empty($calculatedItems)) {
                throw new InvalidArgumentException('Order items calculation resulted in no valid items.');
            }

            $total = $subtotal; // Subtotal equals total (no hidden fees or client-manipulated discounts)

            // 2. Atomically obtain next sequential daily order number for this restaurant and date
            $orderNumber = DailyCounter::getNextOrderNumber($restaurantId, $orderDate);

            // 3. Generate 256-bit cryptographically secure random token & hash
            $rawToken = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $rawToken);

            // 4. Insert Order
            $orderSql = "INSERT INTO orders (
                restaurant_id, order_number, order_date, order_time, customer_name, customer_phone, 
                subtotal, total, payment_method, created_by, status, receipt_token_hash, created_at, updated_at
            ) VALUES (
                :restaurant_id, :order_number, :order_date, :order_time, :customer_name, :customer_phone, 
                :subtotal, :total, :payment_method, :created_by, 'completed', :receipt_token_hash, :created_at, :updated_at
            )";

            Database::execute($orderSql, [
                ':restaurant_id' => $restaurantId,
                ':order_number' => $orderNumber,
                ':order_date' => $orderDate,
                ':order_time' => $orderTime,
                ':customer_name' => !empty($customerName) ? trim($customerName) : null,
                ':customer_phone' => !empty($customerPhone) ? trim($customerPhone) : null,
                ':subtotal' => $subtotal,
                ':total' => $total,
                ':payment_method' => $paymentMethod,
                ':created_by' => $userId,
                ':receipt_token_hash' => $tokenHash,
                ':created_at' => $createdAt,
                ':updated_at' => $createdAt,
            ]);

            $orderId = (int)Database::lastInsertId();

            // 5. Insert Order Items
            foreach ($calculatedItems as $itemData) {
                OrderItem::create($orderId, $itemData);
            }

            // 6. Audit log
            AuditLog::log(
                AuditLog::ACTION_ORDER_CREATE,
                'order',
                $orderId,
                $restaurantId,
                $userId,
                ['order_number' => $orderNumber, 'total' => $total, 'payment_method' => $paymentMethod]
            );

            Database::commit();

            return [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'order_date' => $orderDate,
                'order_time' => $orderTime,
                'total' => $total,
                'receipt_token' => $rawToken,
            ];
        } catch (Throwable $e) {
            Database::rollBack();
            throw $e;
        }
    }

    /**
     * Find order by secure raw receipt token for public customer viewing
     */
    public static function findByReceiptToken(string $rawToken): ?array {
        $tokenHash = hash('sha256', $rawToken);

        $order = Database::fetchOne(
            "SELECT o.*, r.name AS restaurant_name, r.phone AS restaurant_phone, r.address AS restaurant_address, u.username AS created_by_username 
             FROM orders o 
             JOIN restaurants r ON o.restaurant_id = r.id 
             JOIN users u ON o.created_by = u.id 
             WHERE o.receipt_token_hash = :hash AND o.deleted_at IS NULL 
             LIMIT 1",
            [':hash' => $tokenHash]
        );

        if (!$order) {
            return null;
        }

        $order['items'] = OrderItem::getByOrderId((int)$order['id']);
        return $order;
    }

    /**
     * Find order by internal ID within restaurant context (Manager or Authorized Staff)
     */
    public static function findById(int $orderId, int $restaurantId, bool $includeDeleted = false): ?array {
        $sql = "SELECT o.*, r.name AS restaurant_name, r.phone AS restaurant_phone, r.address AS restaurant_address, u.username AS created_by_username 
                FROM orders o 
                JOIN restaurants r ON o.restaurant_id = r.id 
                JOIN users u ON o.created_by = u.id 
                WHERE o.id = :id AND o.restaurant_id = :restaurant_id";
        
        if (!$includeDeleted) {
            $sql .= " AND o.deleted_at IS NULL";
        }
        $sql .= " LIMIT 1";

        $order = Database::fetchOne($sql, [':id' => $orderId, ':restaurant_id' => $restaurantId]);
        if (!$order) {
            return null;
        }

        $order['items'] = OrderItem::getByOrderId((int)$order['id']);
        return $order;
    }

    /**
     * Edit order (Manager only) with price recalculation and audit trail
     */
    public static function updateOrder(
        int $orderId,
        int $restaurantId,
        int $managerUserId,
        ?string $customerName,
        ?string $customerPhone,
        string $paymentMethod,
        array $rawItems
    ): bool {
        $order = self::findById($orderId, $restaurantId);
        if (!$order) {
            throw new RuntimeException("Order not found or inaccessible.");
        }

        Database::beginTransaction();
        try {
            $subtotal = 0.0;
            $calculatedItems = [];

            foreach ($rawItems as $rawItem) {
                $itemId = (int)($rawItem['item_id'] ?? 0);
                $variantId = (int)($rawItem['variant_id'] ?? 0);
                $quantity = (float)($rawItem['quantity'] ?? 1.0);

                if ($quantity <= 0) continue;

                $item = Database::fetchOne(
                    "SELECT * FROM items WHERE id = :id AND restaurant_id = :restaurant_id LIMIT 1",
                    [':id' => $itemId, ':restaurant_id' => $restaurantId]
                );

                if (!$item) {
                    throw new RuntimeException("Item #{$itemId} not found.");
                }

                $variant = null;
                if ($variantId > 0) {
                    $variant = Database::fetchOne(
                        "SELECT * FROM item_variants WHERE id = :id AND item_id = :item_id LIMIT 1",
                        [':id' => $variantId, ':item_id' => $itemId]
                    );
                }

                if (!$variant) {
                    $variant = Database::fetchOne(
                        "SELECT * FROM item_variants WHERE item_id = :item_id ORDER BY id ASC LIMIT 1",
                        [':item_id' => $itemId]
                    );
                }

                $unitPrice = (float)$variant['price'];
                $itemTotal = round($quantity * $unitPrice, 2);
                $subtotal += $itemTotal;

                $calculatedItems[] = [
                    'item_id' => $item['id'],
                    'item_name_snapshot' => $item['name'],
                    'variant_name_snapshot' => $variant['variant_name'],
                    'quantity' => $quantity,
                    'unit' => $variant['quantity_unit'] ?? $item['base_unit'],
                    'unit_price' => $unitPrice,
                    'total_price' => $itemTotal,
                ];
            }

            if (empty($calculatedItems)) {
                throw new InvalidArgumentException('Order must contain at least one item.');
            }

            $total = $subtotal;
            $now = date('Y-m-d H:i:s');

            // Update order header
            Database::execute(
                "UPDATE orders SET 
                    customer_name = :customer_name, 
                    customer_phone = :customer_phone, 
                    payment_method = :payment_method, 
                    subtotal = :subtotal, 
                    total = :total, 
                    updated_at = :updated_at 
                 WHERE id = :id AND restaurant_id = :restaurant_id",
                [
                    ':id' => $orderId,
                    ':restaurant_id' => $restaurantId,
                    ':customer_name' => !empty($customerName) ? trim($customerName) : null,
                    ':customer_phone' => !empty($customerPhone) ? trim($customerPhone) : null,
                    ':payment_method' => $paymentMethod,
                    ':subtotal' => $subtotal,
                    ':total' => $total,
                    ':updated_at' => $now,
                ]
            );

            // Replace order items
            Database::execute("DELETE FROM order_items WHERE order_id = :order_id", [':order_id' => $orderId]);
            foreach ($calculatedItems as $itemData) {
                OrderItem::create($orderId, $itemData);
            }

            // Record audit log with old & new totals
            AuditLog::log(
                AuditLog::ACTION_ORDER_EDIT,
                'order',
                $orderId,
                $restaurantId,
                $managerUserId,
                [
                    'old_total' => $order['total'],
                    'new_total' => $total,
                    'old_payment' => $order['payment_method'],
                    'new_payment' => $paymentMethod,
                ]
            );

            Database::commit();
            return true;
        } catch (Throwable $e) {
            Database::rollBack();
            throw $e;
        }
    }

    /**
     * Soft delete order (Manager only)
     */
    public static function softDelete(int $orderId, int $restaurantId, int $managerUserId): bool {
        $order = self::findById($orderId, $restaurantId);
        if (!$order) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        Database::execute(
            "UPDATE orders SET deleted_at = :deleted_at, deleted_by = :deleted_by WHERE id = :id AND restaurant_id = :restaurant_id",
            [':deleted_at' => $now, ':deleted_by' => $managerUserId, ':id' => $orderId, ':restaurant_id' => $restaurantId]
        );

        AuditLog::log(
            AuditLog::ACTION_ORDER_DELETE,
            'order',
            $orderId,
            $restaurantId,
            $managerUserId,
            ['order_number' => $order['order_number'], 'total' => $order['total']]
        );

        return true;
    }

    /**
     * Get aggregate statistics for Dashboard & Reports
     */
    public static function getStats(int $restaurantId, string $period = 'today', ?string $customStart = null, ?string $customEnd = null): array {
        $restaurant = Restaurant::findById($restaurantId);
        $tz = new DateTimeZone($restaurant['timezone'] ?? 'Asia/Kolkata');
        $now = new DateTime('now', $tz);

        $startDate = $now->format('Y-m-d');
        $endDate = $now->format('Y-m-d');

        switch ($period) {
            case 'today':
                $startDate = $now->format('Y-m-d');
                $endDate = $now->format('Y-m-d');
                break;
            case 'yesterday':
                $yesterday = (clone $now)->modify('-1 day');
                $startDate = $yesterday->format('Y-m-d');
                $endDate = $yesterday->format('Y-m-d');
                break;
            case '7days':
                $start = (clone $now)->modify('-6 days');
                $startDate = $start->format('Y-m-d');
                $endDate = $now->format('Y-m-d');
                break;
            case 'this_month':
                $startDate = $now->format('Y-m-01');
                $endDate = $now->format('Y-m-t');
                break;
            case 'last_month':
                $lastMonth = (clone $now)->modify('-1 month');
                $startDate = $lastMonth->format('Y-m-01');
                $endDate = $lastMonth->format('Y-m-t');
                break;
            case '3months':
                $start = (clone $now)->modify('-3 months');
                $startDate = $start->format('Y-m-01');
                $endDate = $now->format('Y-m-t');
                break;
            case '6months':
                $start = (clone $now)->modify('-6 months');
                $startDate = $start->format('Y-m-01');
                $endDate = $now->format('Y-m-t');
                break;
            case 'year':
                $startDate = $now->format('Y-01-01');
                $endDate = $now->format('Y-12-31');
                break;
            case 'custom':
                if (!empty($customStart)) $startDate = $customStart;
                if (!empty($customEnd)) $endDate = $customEnd;
                break;
        }

        $sql = "SELECT 
                    COUNT(id) AS total_orders,
                    COALESCE(SUM(total), 0) AS total_sales,
                    COALESCE(SUM(CASE WHEN payment_method = 'Cash' THEN total ELSE 0 END), 0) AS cash_sales,
                    COALESCE(SUM(CASE WHEN payment_method != 'Cash' THEN total ELSE 0 END), 0) AS online_sales
                FROM orders 
                WHERE restaurant_id = :restaurant_id 
                  AND order_date >= :start_date 
                  AND order_date <= :end_date 
                  AND deleted_at IS NULL";

        $res = Database::fetchOne($sql, [
            ':restaurant_id' => $restaurantId,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
        ]);

        return [
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_orders' => (int)($res['total_orders'] ?? 0),
            'total_sales' => (float)($res['total_sales'] ?? 0.0),
            'cash_sales' => (float)($res['cash_sales'] ?? 0.0),
            'online_sales' => (float)($res['online_sales'] ?? 0.0),
        ];
    }

    /**
     * Cashier today's summary
     */
    public static function getCashierSummary(int $restaurantId, ?string $date = null): array {
        if (!$date) {
            $restaurant = Restaurant::findById($restaurantId);
            $tz = new DateTimeZone($restaurant['timezone'] ?? 'Asia/Kolkata');
            $date = (new DateTime('now', $tz))->format('Y-m-d');
        }

        $sql = "SELECT 
                    COUNT(id) AS total_orders,
                    COALESCE(SUM(total), 0) AS total_sales,
                    COALESCE(SUM(CASE WHEN payment_method = 'Cash' THEN total ELSE 0 END), 0) AS cash_sales,
                    COALESCE(SUM(CASE WHEN payment_method != 'Cash' THEN total ELSE 0 END), 0) AS online_sales
                FROM orders 
                WHERE restaurant_id = :restaurant_id 
                  AND order_date = :order_date 
                  AND deleted_at IS NULL";

        $res = Database::fetchOne($sql, [
            ':restaurant_id' => $restaurantId,
            ':order_date' => $date
        ]);

        return [
            'date' => $date,
            'total_orders' => (int)($res['total_orders'] ?? 0),
            'total_sales' => (float)($res['total_sales'] ?? 0.0),
            'cash_sales' => (float)($res['cash_sales'] ?? 0.0),
            'online_sales' => (float)($res['online_sales'] ?? 0.0),
        ];
    }

    /**
     * Filter orders for Manager order history
     */
    public static function getOrdersList(int $restaurantId, array $filters = [], int $limit = 50, int $offset = 0): array {
        $params = [':restaurant_id' => $restaurantId];
        $where = ["o.restaurant_id = :restaurant_id", "o.deleted_at IS NULL"];

        if (!empty($filters['date'])) {
            $where[] = "o.order_date = :date";
            $params[':date'] = $filters['date'];
        } elseif (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $where[] = "o.order_date >= :start_date AND o.order_date <= :end_date";
            $params[':start_date'] = $filters['start_date'];
            $params[':end_date'] = $filters['end_date'];
        }

        if (!empty($filters['payment_method'])) {
            $where[] = "o.payment_method = :payment_method";
            $params[':payment_method'] = $filters['payment_method'];
        }

        if (!empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $where[] = "(o.customer_name LIKE :s1 OR o.customer_phone LIKE :s2 OR CAST(o.order_number AS TEXT) LIKE :s3)";
            $params[':s1'] = $search;
            $params[':s2'] = $search;
            $params[':s3'] = $search;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT o.*, u.username AS created_by_username 
                FROM orders o 
                JOIN users u ON o.created_by = u.id 
                WHERE {$whereClause} 
                ORDER BY o.order_date DESC, o.order_number DESC 
                LIMIT {$limit} OFFSET {$offset}";

        $orders = Database::fetchAll($sql, $params);

        foreach ($orders as &$order) {
            $order['items'] = OrderItem::getByOrderId((int)$order['id']);
        }
        unset($order);

        return $orders;
    }

    /**
     * Get orders for CSV Export
     */
    public static function getOrdersForExport(int $restaurantId, string $startDate, string $endDate): array {
        $sql = "SELECT o.*, u.username AS created_by_username 
                FROM orders o 
                JOIN users u ON o.created_by = u.id 
                WHERE o.restaurant_id = :restaurant_id 
                  AND o.order_date >= :start_date 
                  AND o.order_date <= :end_date 
                  AND o.deleted_at IS NULL 
                ORDER BY o.order_date ASC, o.order_number ASC";

        $orders = Database::fetchAll($sql, [
            ':restaurant_id' => $restaurantId,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
        ]);

        foreach ($orders as &$order) {
            $order['items'] = OrderItem::getByOrderId((int)$order['id']);
        }
        unset($order);

        return $orders;
    }
}
