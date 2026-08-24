<?php
/**
 * Application Configuration & Helper Functions
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

// Prevent direct access
if (defined('ROOT_PATH') === false) {
    define('ROOT_PATH', dirname(__DIR__));
}

// 1. Simple .env file parser
function loadEnv(string $path): void {
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            // Remove quotes if present
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

// Load environment variables
loadEnv(ROOT_PATH . '/.env');

// Helper to get environment variable with fallback
function env(string $key, mixed $default = null): mixed {
    $val = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    if ($val === false || $val === null) {
        return $default;
    }
    return match (strtolower((string)$val)) {
        'true', '(true)' => true,
        'false', '(false)' => false,
        'empty', '(empty)' => '',
        'null', '(null)' => null,
        default => $val,
    };
}

// Application settings
$appConfig = [
    'name' => env('APP_NAME', 'DinePOS'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool)env('APP_DEBUG', false),
    'url' => rtrim((string)env('APP_URL', 'http://localhost:8000'), '/'),
    'timezone' => env('APP_TIMEZONE', 'Asia/Kolkata'),
    'secret' => env('APP_SECRET', 'dinepos-default-app-secret-change-in-prod'),
    'session_lifetime' => (int)env('SESSION_LIFETIME', 28800),
    'login_max_attempts' => (int)env('LOGIN_MAX_ATTEMPTS', 5),
    'login_lockout_minutes' => (int)env('LOGIN_LOCKOUT_MINUTES', 15),
];

// Set default timezone
date_default_timezone_set($appConfig['timezone']);

// Error handling based on environment
if ($appConfig['debug']) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

// Secure session start with security directives
function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        $lifetime = (int)env('SESSION_LIFETIME', 28800);
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                    ($_SERVER['SERVER_PORT'] ?? '') === '443';

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.gc_maxlifetime', (string)$lifetime);

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => '/',
            'domain' => '',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_name('DINEPOS_SESSID');
        session_start();
    }
}

// Send Security Headers
function sendSecurityHeaders(): void {
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        // Basic CSP that allows internal scripts and inline data for PWA/icons/print
        header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' data: blob:; img-src 'self' data: blob:; style-src 'self' 'unsafe-inline'; font-src 'self' data:;");
    }
}

// -------------------------------------------------------------
// Helper functions
// -------------------------------------------------------------

/**
 * Escape HTML output to prevent XSS
 */
function e(?string $string): string {
    return htmlspecialchars((string)($string ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Generate or get CSRF token
 */
function csrf_token(): string {
    startSecureSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output hidden input with CSRF token
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Verify CSRF token from POST or Header
 */
function verify_csrf(): bool {
    startSecureSession();
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], (string)$token);
}

/**
 * Generate full URL
 */
function url(string $path = ''): string {
    $path = ltrim($path, '/');
    return $path !== '' ? '/' . $path : '/';
}

/**
 * Generate asset URL
 */
function asset(string $path): string {
    return '/assets/' . ltrim($path, '/');
}

/**
 * Check if user is authenticated
 */
function auth_check(): bool {
    startSecureSession();
    return !empty($_SESSION['user_id']) && !empty($_SESSION['role']);
}

/**
 * Get current authenticated user
 */
function current_user(): ?array {
    startSecureSession();
    if (!auth_check()) {
        return null;
    }
    return [
        'id' => (int)$_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'role' => $_SESSION['role'] ?? '',
        'restaurant_id' => !empty($_SESSION['restaurant_id']) ? (int)$_SESSION['restaurant_id'] : null,
        'restaurant_name' => $_SESSION['restaurant_name'] ?? '',
        'restaurant_timezone' => $_SESSION['restaurant_timezone'] ?? 'Asia/Kolkata',
    ];
}

/**
 * Format currency with Indian Rupee symbol by default
 */
function format_currency(float|int|string|null $amount): string {
    $val = (float)($amount ?? 0.0);
    return '₹' . number_format($val, 2);
}

/**
 * Format date in user-friendly format
 */
function format_date(?string $date): string {
    if (!$date) return '-';
    $time = strtotime($date);
    return $time ? date('d M Y', $time) : $date;
}

/**
 * Format time in 12-hour AM/PM format
 */
function format_time(?string $time): string {
    if (!$time) return '-';
    $t = strtotime($time);
    return $t ? date('h:i A', $t) : $time;
}

/**
 * Set flash message
 */
function set_flash(string $type, string $message): void {
    startSecureSession();
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function get_flash(): ?array {
    startSecureSession();
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

return $appConfig;
