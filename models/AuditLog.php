<?php
/**
 * Audit Log Model
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class AuditLog {
    public const ACTION_LOGIN = 'LOGIN';
    public const ACTION_FAILED_LOGIN = 'FAILED_LOGIN';
    public const ACTION_LOGOUT = 'LOGOUT';
    public const ACTION_ORDER_CREATE = 'ORDER_CREATE';
    public const ACTION_ORDER_EDIT = 'ORDER_EDIT';
    public const ACTION_ORDER_DELETE = 'ORDER_DELETE';
    public const ACTION_ITEM_CREATE = 'ITEM_CREATE';
    public const ACTION_ITEM_EDIT = 'ITEM_EDIT';
    public const ACTION_ITEM_DEACTIVATE = 'ITEM_DEACTIVATE';
    public const ACTION_ITEM_REACTIVATE = 'ITEM_REACTIVATE';
    public const ACTION_USER_CREATE = 'USER_CREATE';
    public const ACTION_USER_EDIT = 'USER_EDIT';
    public const ACTION_SETTINGS_UPDATE = 'SETTINGS_UPDATE';

    public static function log(
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?int $restaurantId = null,
        ?int $userId = null,
        ?array $metadata = null
    ): void {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 250);
            $now = date('Y-m-d H:i:s');

            $sql = "INSERT INTO audit_logs (restaurant_id, user_id, action, entity_type, entity_id, metadata, ip_address, user_agent, created_at) 
                    VALUES (:restaurant_id, :user_id, :action, :entity_type, :entity_id, :metadata, :ip_address, :user_agent, :created_at)";

            Database::execute($sql, [
                ':restaurant_id' => $restaurantId,
                ':user_id' => $userId,
                ':action' => $action,
                ':entity_type' => $entityType,
                ':entity_id' => $entityId,
                ':metadata' => !empty($metadata) ? json_encode($metadata, JSON_UNESCAPED_UNICODE) : null,
                ':ip_address' => $ip,
                ':user_agent' => $userAgent,
                ':created_at' => $now,
            ]);
        } catch (Throwable $e) {
            // Fail gracefully without crashing the main transaction
            error_log('Audit Log Error: ' . $e->getMessage());
        }
    }

    public static function getByRestaurant(int $restaurantId, int $limit = 100): array {
        return Database::fetchAll(
            "SELECT a.*, u.username, u.role 
             FROM audit_logs a 
             LEFT JOIN users u ON a.user_id = u.id 
             WHERE a.restaurant_id = :restaurant_id 
             ORDER BY a.id DESC 
             LIMIT " . (int)$limit,
            [':restaurant_id' => $restaurantId]
        );
    }
}
