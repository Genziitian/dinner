<?php
/**
 * Authentication Controller
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/models/RateLimiter.php';
require_once ROOT_PATH . '/models/AuditLog.php';

class AuthController extends BaseController {
    /**
     * Show login form
     */
    public function showLogin(): void {
        startSecureSession();
        if (auth_check()) {
            $user = current_user();
            $this->redirectByRole($user['role']);
            return;
        }

        $this->renderStandalone('auth/login', [
            'title' => 'Login | DinePOS',
        ]);
    }

    /**
     * Handle login submission
     */
    public function login(): void {
        $this->validateCsrf();

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $username = strtolower(trim($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        // 1. Brute force check
        if (RateLimiter::isRateLimited($ip, $username)) {
            $minutes = (int)env('LOGIN_LOCKOUT_MINUTES', 15);
            set_flash('danger', "Too many failed attempts. Please try again in {$minutes} minutes.");
            $this->redirect('/login');
            return;
        }

        // 2. Validate input formats
        if (!User::validateUsername($username) || !User::validatePassword($password)) {
            RateLimiter::recordAttempt($ip, $username);
            $remaining = RateLimiter::getRemainingAttempts($ip, $username);
            AuditLog::log(AuditLog::ACTION_FAILED_LOGIN, 'user', null, null, null, ['username' => $username, 'reason' => 'invalid_format']);
            set_flash('danger', "Invalid username or password. {$remaining} attempt(s) remaining.");
            $this->redirect('/login');
            return;
        }

        // 3. Lookup user
        $user = User::findByUsername($username);
        if (!$user) {
            RateLimiter::recordAttempt($ip, $username);
            $remaining = RateLimiter::getRemainingAttempts($ip, $username);
            AuditLog::log(AuditLog::ACTION_FAILED_LOGIN, 'user', null, null, null, ['username' => $username, 'reason' => 'not_found']);
            set_flash('danger', "Invalid username or password. {$remaining} attempt(s) remaining.");
            $this->redirect('/login');
            return;
        }

        // 4. Check lockout on user account
        if (User::isLocked($user)) {
            set_flash('danger', 'Account is temporarily locked due to multiple failed login attempts. Please try later.');
            $this->redirect('/login');
            return;
        }

        // 5. Check if user or restaurant is active
        if ($user['status'] !== 'active') {
            set_flash('danger', 'Your account has been deactivated. Please contact administrator.');
            $this->redirect('/login');
            return;
        }

        if ($user['role'] !== User::ROLE_SUPERADMIN && (!empty($user['restaurant_status']) && $user['restaurant_status'] !== 'active')) {
            set_flash('danger', 'Restaurant account is currently inactive.');
            $this->redirect('/login');
            return;
        }

        // 6. Verify password
        if (!password_verify($password, $user['password_hash'])) {
            RateLimiter::recordAttempt($ip, $username);
            $remaining = RateLimiter::getRemainingAttempts($ip, $username);
            User::recordFailedLogin((int)$user['id']);
            AuditLog::log(AuditLog::ACTION_FAILED_LOGIN, 'user', (int)$user['id'], $user['restaurant_id'] ? (int)$user['restaurant_id'] : null, null, ['username' => $username, 'reason' => 'bad_password']);
            set_flash('danger', "Invalid username or password. {$remaining} attempt(s) remaining.");
            $this->redirect('/login');
            return;
        }

        // 7. Successful login: clear locks, regenerate session ID
        RateLimiter::clearAttempts($ip, $username);
        User::resetFailedLogins((int)$user['id']);

        session_regenerate_id(true);

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['restaurant_id'] = $user['restaurant_id'] ? (int)$user['restaurant_id'] : null;
        $_SESSION['restaurant_name'] = $user['restaurant_name'] ?? '';
        $_SESSION['restaurant_timezone'] = $user['restaurant_timezone'] ?? 'Asia/Kolkata';
        $_SESSION['shop_type'] = $user['shop_type'] ?? 'restaurant';

        // Re-seed CSRF token on login
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        AuditLog::log(
            AuditLog::ACTION_LOGIN,
            'user',
            (int)$user['id'],
            $user['restaurant_id'] ? (int)$user['restaurant_id'] : null,
            (int)$user['id'],
            ['username' => $user['username'], 'role' => $user['role']]
        );

        $this->redirectByRole($user['role'], $user['shop_type'] ?? 'restaurant');
    }

    /**
     * Handle logout
     */
    public function logout(): void {
        startSecureSession();
        $user = current_user();
        if ($user) {
            AuditLog::log(
                AuditLog::ACTION_LOGOUT,
                'user',
                $user['id'],
                $user['restaurant_id'],
                $user['id']
            );
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();

        $this->redirect('/login', 'info', 'You have been logged out securely.');
    }

    private function redirectByRole(string $role, string $shopType = ''): void {
        if (empty($shopType)) {
            $shopType = $_SESSION['shop_type'] ?? 'restaurant';
        }

        if ($role === User::ROLE_SUPERADMIN) {
            $this->redirect('/admin/dashboard');
            return;
        }

        if ($shopType === 'mill') {
            $this->redirect('/mill/dashboard');
            return;
        }

        match ($role) {
            User::ROLE_MANAGER => $this->redirect('/manager/dashboard'),
            User::ROLE_CASHIER => $this->redirect('/cashier/order'),
            default => $this->redirect('/login'),
        };
    }
}
