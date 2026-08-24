<?php
/**
 * User Model
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class User {
    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_CASHIER = 'cashier';

    public static function findById(int $id): ?array {
        return Database::fetchOne(
            "SELECT u.*, r.name AS restaurant_name, r.timezone AS restaurant_timezone, r.status AS restaurant_status 
             FROM users u 
             LEFT JOIN restaurants r ON u.restaurant_id = r.id 
             WHERE u.id = :id LIMIT 1",
            [':id' => $id]
        );
    }

    public static function findByUsername(string $username): ?array {
        return Database::fetchOne(
            "SELECT u.*, r.name AS restaurant_name, r.timezone AS restaurant_timezone, r.status AS restaurant_status 
             FROM users u 
             LEFT JOIN restaurants r ON u.restaurant_id = r.id 
             WHERE u.username = :username LIMIT 1",
            [':username' => $username]
        );
    }

    public static function allByRestaurant(int $restaurantId): array {
        return Database::fetchAll(
            "SELECT id, restaurant_id, username, role, status, created_at, updated_at 
             FROM users 
             WHERE restaurant_id = :restaurant_id 
             ORDER BY role ASC, username ASC",
            [':restaurant_id' => $restaurantId]
        );
    }

    public static function all(): array {
        return Database::fetchAll(
            "SELECT u.id, u.restaurant_id, u.username, u.role, u.status, u.created_at, u.updated_at, r.name AS restaurant_name 
             FROM users u 
             LEFT JOIN restaurants r ON u.restaurant_id = r.id 
             ORDER BY u.id ASC"
        );
    }

    public static function validateUsername(string $username): bool {
        // Max 10 chars, letters, numbers, underscore only, min 3 chars
        return (bool)preg_match('/^[a-zA-Z0-9_]{3,10}$/', $username);
    }

    public static function validatePassword(string $password): bool {
        $len = strlen($password);
        return $len >= 8 && $len <= 72;
    }

    public static function create(array $data): int {
        $username = strtolower(trim($data['username']));
        if (!self::validateUsername($username)) {
            throw new InvalidArgumentException('Username must be 3-10 alphanumeric or underscore characters.');
        }

        if (!self::validatePassword($data['password'])) {
            throw new InvalidArgumentException('Password must be between 8 and 72 characters.');
        }

        if (self::findByUsername($username) !== null) {
            throw new InvalidArgumentException('Username already exists.');
        }

        $now = date('Y-m-d H:i:s');
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (restaurant_id, username, password_hash, role, status, failed_login_attempts, locked_until, created_at, updated_at) 
                VALUES (:restaurant_id, :username, :password_hash, :role, :status, 0, NULL, :created_at, :updated_at)";

        Database::execute($sql, [
            ':restaurant_id' => !empty($data['restaurant_id']) ? (int)$data['restaurant_id'] : null,
            ':username' => $username,
            ':password_hash' => $hash,
            ':role' => $data['role'],
            ':status' => $data['status'] ?? 'active',
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);

        return (int)Database::lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $user = self::findById($id);
        if (!$user) return false;

        $now = date('Y-m-d H:i:s');
        $params = [
            ':id' => $id,
            ':role' => $data['role'] ?? $user['role'],
            ':status' => $data['status'] ?? $user['status'],
            ':updated_at' => $now,
        ];

        $passwordSql = '';
        if (!empty($data['password'])) {
            if (!self::validatePassword($data['password'])) {
                throw new InvalidArgumentException('Password must be between 8 and 72 characters.');
            }
            $passwordSql = ', password_hash = :password_hash';
            $params[':password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql = "UPDATE users SET role = :role, status = :status, updated_at = :updated_at {$passwordSql} WHERE id = :id";
        Database::execute($sql, $params);
        return true;
    }

    public static function recordFailedLogin(int $userId): void {
        $user = self::findById($userId);
        if (!$user) return;

        $attempts = (int)$user['failed_login_attempts'] + 1;
        $lockedUntil = null;
        $maxAttempts = (int)env('LOGIN_MAX_ATTEMPTS', 5);
        $lockoutMinutes = (int)env('LOGIN_LOCKOUT_MINUTES', 15);

        if ($attempts >= $maxAttempts) {
            $lockedUntil = date('Y-m-d H:i:s', time() + ($lockoutMinutes * 60));
        }

        Database::execute(
            "UPDATE users SET failed_login_attempts = :attempts, locked_until = :locked_until WHERE id = :id",
            [':attempts' => $attempts, ':locked_until' => $lockedUntil, ':id' => $userId]
        );
    }

    public static function resetFailedLogins(int $userId): void {
        Database::execute(
            "UPDATE users SET failed_login_attempts = 0, locked_until = NULL WHERE id = :id",
            [':id' => $userId]
        );
    }

    public static function isLocked(array $user): bool {
        if (empty($user['locked_until'])) {
            return false;
        }
        $lockTime = strtotime((string)$user['locked_until']);
        if ($lockTime > time()) {
            return true;
        }
        // Lock has expired, reset
        self::resetFailedLogins((int)$user['id']);
        return false;
    }
}
