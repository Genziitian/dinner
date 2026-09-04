<?php
/**
 * Mill Order Model
 * Atta & Oil Mill Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/MillCustomer.php';

class MillOrder {
    public const STATUS_RECEIVED   = 'received';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_READY      = 'ready';
    public const STATUS_DELIVERED  = 'delivered';
    public const STATUS_CANCELLED  = 'cancelled';

    public const PAYMENT_PAID   = 'paid';
    public const PAYMENT_UNPAID = 'unpaid';

    public static function findById(int $id): ?array {
        return Database::fetchOne(
            "SELECT * FROM mill_orders WHERE id = :id LIMIT 1",
            [':id' => $id]
        );
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');
        $time = date('H:i:s');
        $restaurantId = (int)$data['restaurant_id'];

        // Find or auto-remember customer
        $customer = MillCustomer::findOrCreate(
            $restaurantId,
            trim($data['customer_name']),
            trim($data['customer_phone'])
        );

        // Daily sequential order number for this mill
        $orderRow = Database::fetchOne(
            "SELECT COALESCE(MAX(order_number), 0) + 1 AS next_num 
             FROM mill_orders 
             WHERE restaurant_id = :rest_id AND order_date = :today",
            [':rest_id' => $restaurantId, ':today' => $today]
        );
        $orderNumber = (int)($orderRow['next_num'] ?? 1);

        $weight = (float)$data['weight_kg'];
        $rate = (float)$data['rate_per_kg'];
        $total = round($weight * $rate, 2);

        $sql = "INSERT INTO mill_orders (
                    restaurant_id, order_number, order_date, order_time,
                    customer_id, customer_name, customer_phone,
                    service_id, service_name, weight_kg, rate_per_kg, total_amount,
                    payment_status, payment_method, status, notes,
                    created_at, updated_at
                ) VALUES (
                    :restaurant_id, :order_number, :order_date, :order_time,
                    :customer_id, :customer_name, :customer_phone,
                    :service_id, :service_name, :weight_kg, :rate_per_kg, :total_amount,
                    :payment_status, :payment_method, :status, :notes,
                    :created_at, :updated_at
                )";

        Database::execute($sql, [
            ':restaurant_id'  => $restaurantId,
            ':order_number'   => $orderNumber,
            ':order_date'     => $today,
            ':order_time'     => $time,
            ':customer_id'    => $customer['id'],
            ':customer_name'  => $customer['name'],
            ':customer_phone' => $customer['phone'],
            ':service_id'     => !empty($data['service_id']) ? (int)$data['service_id'] : null,
            ':service_name'   => trim($data['service_name']),
            ':weight_kg'      => $weight,
            ':rate_per_kg'    => $rate,
            ':total_amount'   => $total,
            ':payment_status' => $data['payment_status'] ?? self::PAYMENT_UNPAID,
            ':payment_method' => !empty($data['payment_method']) ? trim($data['payment_method']) : 'Cash',
            ':status'         => $data['status'] ?? self::STATUS_RECEIVED,
            ':notes'          => !empty($data['notes']) ? trim($data['notes']) : null,
            ':created_at'     => $now,
            ':updated_at'     => $now,
        ]);

        return (int)Database::lastInsertId();
    }

    public static function allByRestaurant(int $restaurantId, array $filters = []): array {
        $params = [':restaurant_id' => $restaurantId];
        $sql = "SELECT * FROM mill_orders WHERE restaurant_id = :restaurant_id";

        if (!empty($filters['search'])) {
            $rawSearch = trim((string)$filters['search']);
            $search = '%' . $rawSearch . '%';
            $numDigits = preg_replace('/[^0-9]/', '', $rawSearch);
            $cleanNum = !empty($numDigits) ? (int)$numDigits : -1;

            $sql .= " AND (customer_name LIKE :search_name OR customer_phone LIKE :search_phone OR order_number = :search_num OR id = :search_id)";
            $params[':search_name'] = $search;
            $params[':search_phone'] = $search;
            $params[':search_num'] = $cleanNum;
            $params[':search_id'] = $cleanNum;
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        } elseif (empty($filters['status']) || !empty($filters['exclude_cancelled'])) {
            $sql .= " AND status != 'cancelled'";
        }

        if (!empty($filters['payment_status'])) {
            $sql .= " AND payment_status = :payment_status";
            $params[':payment_status'] = $filters['payment_status'];
        }

        if (!empty($filters['customer_phone'])) {
            $cleanPhone = preg_replace('/[^0-9]/', '', (string)$filters['customer_phone']);
            $sql .= " AND customer_phone = :cust_phone";
            $params[':cust_phone'] = $cleanPhone;
        }

        if (!empty($filters['date'])) {
            $sql .= " AND order_date = :order_date";
            $params[':order_date'] = $filters['date'];
        }

        $sql .= " ORDER BY order_date DESC, order_time DESC, id DESC";

        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
        }

        return Database::fetchAll($sql, $params);
    }

    public static function delete(int $id, int $restaurantId): bool {
        Database::execute(
            "DELETE FROM mill_orders WHERE id = :id AND restaurant_id = :restaurant_id",
            [':id' => $id, ':restaurant_id' => $restaurantId]
        );
        return true;
    }

    public static function updateStatus(int $id, string $status): bool {
        $now = date('Y-m-d H:i:s');
        Database::execute(
            "UPDATE mill_orders SET status = :status, updated_at = :updated_at WHERE id = :id",
            [':status' => $status, ':updated_at' => $now, ':id' => $id]
        );
        return true;
    }

    public static function updatePayment(int $id, string $paymentStatus, ?string $paymentMethod = null): bool {
        $now = date('Y-m-d H:i:s');
        $params = [
            ':id'             => $id,
            ':payment_status' => $paymentStatus,
            ':updated_at'     => $now,
        ];
        $sql = "UPDATE mill_orders SET payment_status = :payment_status, updated_at = :updated_at";
        if ($paymentMethod !== null) {
            $sql .= ", payment_method = :payment_method";
            $params[':payment_method'] = $paymentMethod;
        }
        $sql .= " WHERE id = :id";
        Database::execute($sql, $params);
        return true;
    }

    public static function getDailySummary(int $restaurantId, string $date): array {
        $sql = "SELECT 
                    COUNT(CASE WHEN status != 'cancelled' THEN id END) AS total_orders,
                    SUM(CASE WHEN status IN ('received', 'processing') THEN 1 ELSE 0 END) AS pending_orders,
                    SUM(CASE WHEN status = 'ready' THEN 1 ELSE 0 END) AS ready_orders,
                    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) AS delivered_orders,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_orders,
                    COALESCE(SUM(CASE WHEN status != 'cancelled' THEN total_amount ELSE 0 END), 0) AS total_amount,
                    COALESCE(SUM(CASE WHEN (payment_status = 'paid' OR status = 'delivered') AND status != 'cancelled' THEN total_amount ELSE 0 END), 0) AS paid_amount,
                    COALESCE(SUM(CASE WHEN payment_status = 'unpaid' AND status != 'delivered' AND status != 'cancelled' THEN total_amount ELSE 0 END), 0) AS unpaid_amount,
                    COALESCE(SUM(CASE WHEN status != 'cancelled' THEN weight_kg ELSE 0 END), 0) AS total_weight_kg
                FROM mill_orders 
                WHERE restaurant_id = :restaurant_id AND order_date = :date";

        $res = Database::fetchOne($sql, [':restaurant_id' => $restaurantId, ':date' => $date]);
        return $res ?: [
            'total_orders'     => 0,
            'pending_orders'   => 0,
            'ready_orders'     => 0,
            'delivered_orders' => 0,
            'cancelled_orders' => 0,
            'total_amount'     => 0.00,
            'paid_amount'      => 0.00,
            'unpaid_amount'    => 0.00,
            'total_weight_kg'  => 0.00,
        ];
    }

    public static function getCustomerOrders(int $restaurantId, string $phone): array {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        return Database::fetchAll(
            "SELECT * FROM mill_orders 
             WHERE restaurant_id = :restaurant_id AND customer_phone = :phone AND status != 'cancelled'
             ORDER BY order_date DESC, order_time DESC",
            [':restaurant_id' => $restaurantId, ':phone' => $cleanPhone]
        );
    }

    public static function getEarningsSummary(int $restaurantId, string $startDate, string $endDate): array {
        $sql = "SELECT 
                    COUNT(id) AS total_orders,
                    COALESCE(SUM(total_amount), 0) AS total_earnings,
                    COALESCE(SUM(CASE WHEN payment_status = 'paid' OR status = 'delivered' THEN total_amount ELSE 0 END), 0) AS paid_earnings,
                    COALESCE(SUM(CASE WHEN payment_status = 'unpaid' AND status != 'delivered' THEN total_amount ELSE 0 END), 0) AS unpaid_earnings,
                    COALESCE(SUM(weight_kg), 0) AS total_weight_kg
                FROM mill_orders 
                WHERE restaurant_id = :restaurant_id 
                  AND order_date BETWEEN :start_date AND :end_date
                  AND status != 'cancelled'";

        $summary = Database::fetchOne($sql, [
            ':restaurant_id' => $restaurantId,
            ':start_date'    => $startDate,
            ':end_date'      => $endDate
        ]);

        $summaryData = [
            'total_orders'    => (int)($summary['total_orders'] ?? 0),
            'total_earnings'  => (float)($summary['total_earnings'] ?? 0),
            'paid_earnings'   => (float)($summary['paid_earnings'] ?? 0),
            'unpaid_earnings' => (float)($summary['unpaid_earnings'] ?? 0),
            'total_weight_kg' => (float)($summary['total_weight_kg'] ?? 0),
        ];

        // Service breakdown
        $servicesSql = "SELECT 
                            service_name,
                            COUNT(id) AS order_count,
                            COALESCE(SUM(weight_kg), 0) AS total_weight_kg,
                            COALESCE(SUM(total_amount), 0) AS total_amount
                        FROM mill_orders
                        WHERE restaurant_id = :restaurant_id
                          AND order_date BETWEEN :start_date AND :end_date
                          AND status != 'cancelled'
                        GROUP BY service_name
                        ORDER BY total_amount DESC";
        $services = Database::fetchAll($servicesSql, [
            ':restaurant_id' => $restaurantId,
            ':start_date'    => $startDate,
            ':end_date'      => $endDate
        ]);
        $formattedServices = [];
        foreach ($services as $s) {
            $formattedServices[] = [
                'service_name'    => $s['service_name'] ?: 'General Grinding',
                'order_count'     => (int)$s['order_count'],
                'total_weight_kg' => (float)$s['total_weight_kg'],
                'total_amount'    => (float)$s['total_amount'],
            ];
        }

        // Orders in this period
        $ordersSql = "SELECT * FROM mill_orders
                      WHERE restaurant_id = :restaurant_id
                        AND order_date BETWEEN :start_date AND :end_date
                        AND status != 'cancelled'
                      ORDER BY order_date DESC, order_time DESC, id DESC
                      LIMIT 100";
        $orders = Database::fetchAll($ordersSql, [
            ':restaurant_id' => $restaurantId,
            ':start_date'    => $startDate,
            ':end_date'      => $endDate
        ]);

        return [
            'summary'  => $summaryData,
            'services' => $formattedServices,
            'orders'   => $orders
        ];
    }

    /**
     * Export all data for backup
     */
    public static function exportAll(int $restaurantId): array {
        $orders = Database::fetchAll(
            "SELECT * FROM mill_orders WHERE restaurant_id = :restaurant_id ORDER BY id ASC",
            [':restaurant_id' => $restaurantId]
        );
        $customers = Database::fetchAll(
            "SELECT * FROM mill_customers WHERE restaurant_id = :restaurant_id ORDER BY id ASC",
            [':restaurant_id' => $restaurantId]
        );
        $services = Database::fetchAll(
            "SELECT * FROM mill_services WHERE restaurant_id = :restaurant_id ORDER BY id ASC",
            [':restaurant_id' => $restaurantId]
        );

        return [
            'version'     => '1.0',
            'exported_at' => date('Y-m-d H:i:s'),
            'restaurant_id' => $restaurantId,
            'customers'   => $customers,
            'services'    => $services,
            'orders'      => $orders,
        ];
    }

    /**
     * Restore from backup payload
     */
    public static function restoreBackup(int $restaurantId, array $payload): array {
        $restoredCustomers = 0;
        $restoredServices = 0;
        $restoredOrders = 0;

        // Restore services
        if (!empty($payload['services']) && is_array($payload['services'])) {
            foreach ($payload['services'] as $s) {
                if (empty($s['name'])) continue;
                $exists = Database::fetchOne(
                    "SELECT id FROM mill_services WHERE restaurant_id = :rest_id AND name = :name",
                    [':rest_id' => $restaurantId, ':name' => trim($s['name'])]
                );
                if (!$exists) {
                    MillService::create([
                        'restaurant_id' => $restaurantId,
                        'name'          => trim($s['name']),
                        'name_hi'       => $s['name_hi'] ?? null,
                        'rate_per_kg'   => (float)($s['rate_per_kg'] ?? 0),
                        'active'        => (int)($s['active'] ?? 1),
                    ]);
                    $restoredServices++;
                }
            }
        }

        // Restore customers
        if (!empty($payload['customers']) && is_array($payload['customers'])) {
            foreach ($payload['customers'] as $c) {
                if (empty($c['phone']) || empty($c['name'])) continue;
                MillCustomer::findOrCreate($restaurantId, (string)$c['name'], (string)$c['phone']);
                $restoredCustomers++;
            }
        }

        // Restore orders
        if (!empty($payload['orders']) && is_array($payload['orders'])) {
            foreach ($payload['orders'] as $o) {
                if (empty($o['customer_phone']) || empty($o['service_name'])) continue;
                // Check if order already exists by order_date and order_number
                $exists = Database::fetchOne(
                    "SELECT id FROM mill_orders 
                     WHERE restaurant_id = :rest_id AND order_date = :odate AND order_number = :onum",
                    [
                        ':rest_id' => $restaurantId,
                        ':odate'   => $o['order_date'],
                        ':onum'    => (int)$o['order_number'],
                    ]
                );
                if (!$exists) {
                    $customer = MillCustomer::findOrCreate($restaurantId, (string)$o['customer_name'], (string)$o['customer_phone']);
                    $now = date('Y-m-d H:i:s');
                    Database::execute(
                        "INSERT INTO mill_orders (
                            restaurant_id, order_number, order_date, order_time,
                            customer_id, customer_name, customer_phone,
                            service_id, service_name, weight_kg, rate_per_kg, total_amount,
                            payment_status, payment_method, status, notes,
                            created_at, updated_at
                        ) VALUES (
                            :restaurant_id, :order_number, :order_date, :order_time,
                            :customer_id, :customer_name, :customer_phone,
                            :service_id, :service_name, :weight_kg, :rate_per_kg, :total_amount,
                            :payment_status, :payment_method, :status, :notes,
                            :created_at, :updated_at
                        )",
                        [
                            ':restaurant_id'  => $restaurantId,
                            ':order_number'   => (int)$o['order_number'],
                            ':order_date'     => $o['order_date'],
                            ':order_time'     => $o['order_time'] ?? '12:00:00',
                            ':customer_id'    => $customer['id'],
                            ':customer_name'  => $customer['name'],
                            ':customer_phone' => $customer['phone'],
                            ':service_id'     => null,
                            ':service_name'   => $o['service_name'],
                            ':weight_kg'      => (float)$o['weight_kg'],
                            ':rate_per_kg'    => (float)$o['rate_per_kg'],
                            ':total_amount'   => (float)$o['total_amount'],
                            ':payment_status' => $o['payment_status'] ?? 'unpaid',
                            ':payment_method' => $o['payment_method'] ?? 'Cash',
                            ':status'         => $o['status'] ?? 'received',
                            ':notes'          => $o['notes'] ?? null,
                            ':created_at'     => $o['created_at'] ?? $now,
                            ':updated_at'     => $o['updated_at'] ?? $now,
                        ]
                    );
                    $restoredOrders++;
                }
            }
        }

        return [
            'services'  => $restoredServices,
            'customers' => $restoredCustomers,
            'orders'    => $restoredOrders,
        ];
    }
}
