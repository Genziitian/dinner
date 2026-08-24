<?php
/**
 * Base Controller
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/models/Restaurant.php';

abstract class BaseController {
    /**
     * Render view template with data
     */
    protected function render(string $viewPath, array $data = []): void {
        startSecureSession();
        sendSecurityHeaders();

        // Extract variables to view scope
        extract($data, EXTR_SKIP);

        $viewFile = ROOT_PATH . '/views/' . ltrim($viewPath, '/') . '.php';
        if (!file_exists($viewFile)) {
            throw new RuntimeException("View file not found: {$viewPath}");
        }

        // Make current user available in views
        $currentUser = current_user();
        $flash = get_flash();

        require_once ROOT_PATH . '/views/layouts/header.php';
        require $viewFile;
        require_once ROOT_PATH . '/views/layouts/footer.php';
    }

    /**
     * Render standalone view without global header/footer (e.g. print receipt or login)
     */
    protected function renderStandalone(string $viewPath, array $data = []): void {
        startSecureSession();
        sendSecurityHeaders();

        extract($data, EXTR_SKIP);
        $viewFile = ROOT_PATH . '/views/' . ltrim($viewPath, '/') . '.php';
        if (!file_exists($viewFile)) {
            throw new RuntimeException("View file not found: {$viewPath}");
        }

        $currentUser = current_user();
        $flash = get_flash();

        require $viewFile;
    }

    /**
     * Send JSON response
     */
    protected function json(array $data, int $status = 200): void {
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: application/json; charset=utf-8');
            sendSecurityHeaders();
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Redirect to another URL
     */
    protected function redirect(string $path, ?string $flashType = null, ?string $flashMsg = null): void {
        startSecureSession();
        if ($flashType !== null && $flashMsg !== null) {
            set_flash($flashType, $flashMsg);
        }
        $target = str_starts_with($path, 'http') ? $path : url($path);
        header("Location: {$target}");
        exit;
    }

    /**
     * Require logged in user
     */
    protected function requireAuth(): array {
        startSecureSession();
        if (!auth_check()) {
            $this->redirect('/login', 'warning', 'Please log in to access this page.');
        }
        $user = current_user();
        if (!$user) {
            $this->redirect('/login');
        }
        return $user;
    }

    /**
     * Require specific user role
     */
    protected function requireRole(string|array $allowedRoles): array {
        $user = $this->requireAuth();
        $roles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];

        if (!in_array($user['role'], $roles, true)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Unauthorized role access.'], 403);
            }
            http_response_code(403);
            $this->renderStandalone('errors/403', ['title' => 'Forbidden Access']);
            exit;
        }

        return $user;
    }

    /**
     * Require restaurant data isolation
     */
    protected function requireRestaurantId(): int {
        $user = $this->requireAuth();
        if ($user['role'] === User::ROLE_SUPERADMIN) {
            // Superadmin can pass restaurant_id or use default
            $rId = (int)($_GET['restaurant_id'] ?? $_SESSION['admin_restaurant_id'] ?? 1);
            return $rId;
        }

        if (empty($user['restaurant_id'])) {
            http_response_code(403);
            die("Access Denied: User is not assigned to a restaurant.");
        }

        return (int)$user['restaurant_id'];
    }

    /**
     * Verify CSRF token on POST/PUT/DELETE
     */
    protected function validateCsrf(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                if ($this->isAjax()) {
                    $this->json(['success' => false, 'message' => 'Invalid or expired CSRF security token.'], 403);
                }
                http_response_code(403);
                die('CSRF Security Validation Failed. Please refresh and try again.');
            }
        }
    }

    /**
     * Check if request is AJAX / JSON
     */
    protected function isAjax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               (!empty($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) ||
               (!empty($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'));
    }
}
