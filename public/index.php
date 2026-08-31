<?php
/**
 * Front Controller & Request Router
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

// Built-in PHP server static asset passthrough
if (php_sapi_name() === 'cli-server') {
    $filePath = __DIR__ . parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if (is_file($filePath)) {
        return false;
    }
}

define('ROOT_PATH', dirname(__DIR__));

// Load configuration
require_once ROOT_PATH . '/config/app.php';

// Autoload Controllers
require_once ROOT_PATH . '/controllers/AuthController.php';
require_once ROOT_PATH . '/controllers/CashierController.php';
require_once ROOT_PATH . '/controllers/ManagerController.php';
require_once ROOT_PATH . '/controllers/ItemController.php';
require_once ROOT_PATH . '/controllers/OrderController.php';
require_once ROOT_PATH . '/controllers/ExportController.php';
require_once ROOT_PATH . '/controllers/ReceiptController.php';
require_once ROOT_PATH . '/controllers/AdminController.php';
require_once ROOT_PATH . '/controllers/ApiController.php';

// Handle CORS Preflight for Mobile App
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-API-Token');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    http_response_code(200);
    exit;
}

// Parse Request URI and Method
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Normalize path
$path = '/' . trim($uri, '/');
if ($path === '//') $path = '/';

// Router Dispatcher
try {
    // 0. REST API v1 Routes (Mobile Client)
    if (str_starts_with($path, '/api/v1/')) {
        $api = new ApiController();
        if ($path === '/api/v1/auth/login' && $method === 'POST') {
            $api->login();
        } elseif ($path === '/api/v1/auth/me' && $method === 'GET') {
            $api->me();
        } elseif ($path === '/api/v1/items' && $method === 'GET') {
            $api->getItems();
        } elseif ($path === '/api/v1/items' && $method === 'POST') {
            $api->createItem();
        } elseif (preg_match('#^/api/v1/items/(\d+)/toggle$#', $path, $m) && $method === 'POST') {
            $api->toggleItem((int)$m[1]);
        } elseif ($path === '/api/v1/orders' && $method === 'POST') {
            $api->createOrder();
        } elseif ($path === '/api/v1/orders' && $method === 'GET') {
            $api->getOrders();
        } elseif (preg_match('#^/api/v1/orders/(\d+)$#', $path, $m) && $method === 'GET') {
            $api->getOrder((int)$m[1]);
        } elseif ($path === '/api/v1/cashier/summary' && $method === 'GET') {
            $api->getCashierSummary();
        } elseif ($path === '/api/v1/manager/dashboard' && $method === 'GET') {
            $api->getManagerDashboard();
        } elseif ($path === '/api/v1/manager/reports' && $method === 'GET') {
            $api->getReports();
        } elseif ($path === '/api/v1/manager/staff' && $method === 'GET') {
            $api->getManagerStaff();
        } elseif ($path === '/api/v1/manager/staff' && $method === 'POST') {
            $api->createManagerStaff();
        } elseif (preg_match('#^/api/v1/manager/staff/(\d+)$#', $path, $m) && $method === 'POST') {
            $api->updateManagerStaff((int)$m[1]);
        } elseif (preg_match('#^/api/v1/manager/staff/(\d+)/toggle$#', $path, $m) && $method === 'POST') {
            $api->toggleManagerStaff((int)$m[1]);
        } elseif ($path === '/api/v1/manager/export/data' && $method === 'GET') {
            $api->getExportData();
        } elseif ($path === '/api/v1/admin/overview' && $method === 'GET') {
            $api->getAdminOverview();
        } elseif ($path === '/api/v1/admin/restaurants' && $method === 'POST') {
            $api->createRestaurant();
        } elseif (preg_match('#^/api/v1/admin/restaurants/(\d+)$#', $path, $m) && $method === 'POST') {
            $api->updateRestaurant((int)$m[1]);
        } elseif (preg_match('#^/api/v1/admin/restaurants/(\d+)/toggle$#', $path, $m) && $method === 'POST') {
            $api->toggleRestaurant((int)$m[1]);
        } elseif ($path === '/api/v1/admin/users' && $method === 'POST') {
            $api->createUser();
        } elseif (preg_match('#^/api/v1/admin/users/(\d+)$#', $path, $m) && $method === 'POST') {
            $api->updateAdminUser((int)$m[1]);
        } elseif (preg_match('#^/api/v1/admin/users/(\d+)/toggle$#', $path, $m) && $method === 'POST') {
            $api->toggleUser((int)$m[1]);
        } elseif (preg_match('#^/api/v1/receipt/([a-zA-Z0-9_-]+)$#', $path, $m) && $method === 'GET') {
            $api->getReceipt($m[1]);
        } else {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'API route not found']);
        }
        exit;
    }
    // 1. Root route
    if ($path === '/') {
        startSecureSession();
        if (auth_check()) {
            $user = current_user();
            match ($user['role']) {
                User::ROLE_SUPERADMIN => header('Location: ' . url('admin/dashboard')),
                User::ROLE_MANAGER => header('Location: ' . url('manager/dashboard')),
                User::ROLE_CASHIER => header('Location: ' . url('cashier/order')),
                default => header('Location: ' . url('login')),
            };
        } else {
            header('Location: ' . url('login'));
        }
        exit;
    }

    // 2. Authentication Routes
    if ($path === '/login') {
        $c = new AuthController();
        if ($method === 'POST') {
            $c->login();
        } else {
            $c->showLogin();
        }
        exit;
    }

    if ($path === '/logout') {
        $c = new AuthController();
        $c->logout();
        exit;
    }

    // 2.1 Legal & Policy Routes (Publicly Accessible)
    if ($path === '/privacy-policy' || $path === '/privacy') {
        startSecureSession();
        sendSecurityHeaders();
        require ROOT_PATH . '/views/legal/privacy.php';
        exit;
    }

    if ($path === '/terms-and-conditions' || $path === '/terms') {
        startSecureSession();
        sendSecurityHeaders();
        require ROOT_PATH . '/views/legal/terms.php';
        exit;
    }

    // 3. Cashier Routes
    if ($path === '/cashier/order') {
        (new CashierController())->order();
        exit;
    }

    if ($path === '/cashier/summary') {
        (new CashierController())->summary();
        exit;
    }

    // 4. Order & Receipt Processing
    if ($path === '/api/orders' || $path === '/orders/create') {
        (new OrderController())->store();
        exit;
    }

    if ($path === '/receipt/view') {
        (new OrderController())->view();
        exit;
    }

    // Public QR receipt URL: /receipt/{token}
    if (preg_match('#^/receipt/([a-zA-Z0-9_\-]+)$#', $path, $matches)) {
        (new ReceiptController())->show($matches[1]);
        exit;
    }

    // 5. Manager Routes
    if ($path === '/manager/dashboard') {
        (new ManagerController())->dashboard();
        exit;
    }

    if ($path === '/manager/orders') {
        (new ManagerController())->orders();
        exit;
    }

    if ($path === '/manager/orders/view') {
        (new ManagerController())->orderView();
        exit;
    }

    if ($path === '/manager/orders/edit') {
        $c = new OrderController();
        if ($method === 'POST') {
            $c->update();
        } else {
            $c->edit();
        }
        exit;
    }

    if ($path === '/manager/orders/delete') {
        (new OrderController())->delete();
        exit;
    }

    // Manager Items
    if ($path === '/manager/items') {
        (new ItemController())->index();
        exit;
    }

    if ($path === '/manager/items/create') {
        $c = new ItemController();
        if ($method === 'POST') {
            $c->store();
        } else {
            $c->create();
        }
        exit;
    }

    if ($path === '/manager/items/edit') {
        $c = new ItemController();
        if ($method === 'POST') {
            $c->update();
        } else {
            $c->edit();
        }
        exit;
    }

    if ($path === '/manager/items/toggle') {
        (new ItemController())->toggle();
        exit;
    }

    // Manager Reports & Exports
    if ($path === '/manager/reports') {
        (new ManagerController())->reports();
        exit;
    }

    if ($path === '/manager/exports') {
        (new ExportController())->index();
        exit;
    }

    if ($path === '/manager/exports/download') {
        (new ExportController())->download();
        exit;
    }

    // Manager Users
    if ($path === '/manager/users') {
        (new ManagerController())->users();
        exit;
    }

    if ($path === '/manager/users/create') {
        $c = new ManagerController();
        if ($method === 'POST') {
            $c->userStore();
        } else {
            $c->userCreate();
        }
        exit;
    }

    if ($path === '/manager/users/edit') {
        $c = new ManagerController();
        if ($method === 'POST') {
            $c->userUpdate();
        } else {
            $c->userEdit();
        }
        exit;
    }

    if ($path === '/manager/audit') {
        (new ManagerController())->audit();
        exit;
    }

    if ($path === '/manager/settings' || $path === '/profile') {
        $c = new ManagerController();
        if ($method === 'POST') {
            $c->updateSettings();
        } else {
            $c->settings();
        }
        exit;
    }

    // 6. Super Admin Routes
    if ($path === '/admin/dashboard') {
        (new AdminController())->dashboard();
        exit;
    }

    if ($path === '/admin/restaurants') {
        (new AdminController())->restaurants();
        exit;
    }

    if ($path === '/admin/restaurants/create') {
        $c = new AdminController();
        if ($method === 'POST') {
            $c->restaurantStore();
        } else {
            $c->restaurantCreate();
        }
        exit;
    }

    if ($path === '/admin/restaurants/edit') {
        $c = new AdminController();
        if ($method === 'POST') {
            $c->restaurantUpdate();
        } else {
            $c->restaurantEdit();
        }
        exit;
    }

    if ($path === '/admin/restaurants/toggle') {
        (new AdminController())->restaurantToggle();
        exit;
    }

    if ($path === '/admin/users') {
        (new AdminController())->users();
        exit;
    }

    if ($path === '/admin/users/create') {
        $c = new AdminController();
        if ($method === 'POST') {
            $c->userStore();
        } else {
            $c->userCreate();
        }
        exit;
    }

    if ($path === '/admin/users/edit') {
        $c = new AdminController();
        if ($method === 'POST') {
            $c->userUpdate();
        } else {
            $c->userEdit();
        }
        exit;
    }

    // 7. Route Not Found
    http_response_code(404);
    $viewFile = ROOT_PATH . '/views/errors/404.php';
    if (file_exists($viewFile)) {
        require_once $viewFile;
    } else {
        echo "404 Not Found";
    }

} catch (Throwable $e) {
    error_log("Unhandled Application Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(500);
    $isDebug = (bool)env('APP_DEBUG', false);
    if ($isDebug) {
        echo "<h1>Server Error</h1><p>" . htmlspecialchars($e->getMessage(), ENT_QUOTES) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES) . "</pre>";
    } else {
        $viewFile = ROOT_PATH . '/views/errors/500.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "An internal server error occurred. Please contact the administrator.";
        }
    }
}
