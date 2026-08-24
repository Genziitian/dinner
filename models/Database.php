<?php
/**
 * Database Connection & PDO Singleton Manager
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

class Database {
    private static ?PDO $instance = null;
    private static ?string $currentDriver = null;

    /**
     * Get or initialize the PDO connection instance
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = require ROOT_PATH . '/config/database.php';
            $driver = $config['default'] ?? 'sqlite';
            self::$currentDriver = $driver;

            if (!isset($config['connections'][$driver])) {
                throw new RuntimeException("Database driver '{$driver}' is not configured.");
            }

            $dbConf = $config['connections'][$driver];

            try {
                if ($driver === 'sqlite') {
                    $dbPath = $dbConf['database'];
                    $dir = dirname($dbPath);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    $dsn = "sqlite:{$dbPath}";
                    self::$instance = new PDO($dsn, null, null, $dbConf['options']);
                    // Enable foreign keys in SQLite
                    self::$instance->exec('PRAGMA foreign_keys = ON;');
                } else {
                    $dsn = sprintf(
                        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                        $dbConf['host'],
                        $dbConf['port'],
                        $dbConf['database'],
                        $dbConf['charset']
                    );
                    self::$instance = new PDO(
                        $dsn,
                        $dbConf['username'],
                        $dbConf['password'],
                        $dbConf['options']
                    );
                }
            } catch (PDOException $e) {
                // In production, log error without leaking credentials
                error_log('Database Connection Error: ' . $e->getMessage());
                throw new RuntimeException('Could not connect to the database. Please check configuration.');
            }
        }

        return self::$instance;
    }

    /**
     * Get the active database driver ('mysql' or 'sqlite')
     */
    public static function getDriver(): string {
        if (self::$currentDriver === null) {
            self::getConnection();
        }
        return self::$currentDriver ?? 'mysql';
    }

    /**
     * Begin database transaction
     */
    public static function beginTransaction(): bool {
        return self::getConnection()->beginTransaction();
    }

    /**
     * Commit database transaction
     */
    public static function commit(): bool {
        return self::getConnection()->commit();
    }

    /**
     * Rollback database transaction
     */
    public static function rollBack(): bool {
        if (self::getConnection()->inTransaction()) {
            return self::getConnection()->rollBack();
        }
        return false;
    }

    /**
     * Check if currently in transaction
     */
    public static function inTransaction(): bool {
        return self::getConnection()->inTransaction();
    }

    /**
     * Helper to prepare and execute statement
     */
    public static function execute(string $sql, array $params = []): PDOStatement {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Helper to fetch single row
     */
    public static function fetchOne(string $sql, array $params = []): ?array {
        $stmt = self::execute($sql, $params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false ? $result : null;
    }

    /**
     * Helper to fetch all rows
     */
    public static function fetchAll(string $sql, array $params = []): array {
        $stmt = self::execute($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Helper to fetch a single column value
     */
    public static function fetchValue(string $sql, array $params = []): mixed {
        $stmt = self::execute($sql, $params);
        return $stmt->fetchColumn();
    }

    /**
     * Get last insert ID
     */
    public static function lastInsertId(): string|int {
        return self::getConnection()->lastInsertId();
    }
}
