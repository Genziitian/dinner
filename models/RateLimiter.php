<?php
/**
 * Rate Limiter Model (Brute-Force Protection)
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class RateLimiter {
    /**
     * Check if IP or username is rate-limited
     */
    public static function isRateLimited(string $ip, string $username): bool {
        $maxAttempts = (int)env('LOGIN_MAX_ATTEMPTS', 5);
        $windowMinutes = (int)env('LOGIN_LOCKOUT_MINUTES', 15);
        $cutoff = date('Y-m-d H:i:s', time() - ($windowMinutes * 60));

        // Check attempts by IP or username in window
        $count = (int)Database::fetchValue(
            "SELECT COUNT(*) FROM login_attempts 
             WHERE (ip_address = :ip OR username = :username) AND attempted_at >= :cutoff",
            [':ip' => $ip, ':username' => $username, ':cutoff' => $cutoff]
        );

        return $count >= $maxAttempts;
    }

    /**
     * Record a failed login attempt
     */
    public static function recordAttempt(string $ip, string $username): void {
        $now = date('Y-m-d H:i:s');
        Database::execute(
            "INSERT INTO login_attempts (ip_address, username, attempted_at) VALUES (:ip, :username, :attempted_at)",
            [':ip' => $ip, ':username' => $username, ':attempted_at' => $now]
        );
    }

    /**
     * Clear login attempts on successful login
     */
    public static function clearAttempts(string $ip, string $username): void {
        Database::execute(
            "DELETE FROM login_attempts WHERE ip_address = :ip OR username = :username",
            [':ip' => $ip, ':username' => $username]
        );
    }

    /**
     * Purge old attempts older than 24 hours
     */
    public static function purgeOldAttempts(): void {
        $cutoff = date('Y-m-d H:i:s', time() - 86400);
        Database::execute("DELETE FROM login_attempts WHERE attempted_at < :cutoff", [':cutoff' => $cutoff]);
    }
}
