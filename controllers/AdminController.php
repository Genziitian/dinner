<?php
/**
 * Super Admin Controller
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once ROOT_PATH . '/models/Restaurant.php';
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/models/AuditLog.php';

class AdminController extends BaseController {
    /**
     * Super Admin Dashboard
     */
    public function dashboard(): void {
        $user = $this->requireRole(User::ROLE_SUPERADMIN);

        $restaurants = Restaurant::all();
        $users = User::all();

        $this->render('admin/dashboard', [
            'title' => 'Super Admin Dashboard | DinePOS',
            'restaurants' => $restaurants,
            'users' => $users,
            'totalRestaurants' => count($restaurants),
            'totalUsers' => count($users),
        ]);
    }

    /**
     * Restaurants List
     */
    public function restaurants(): void {
        $this->requireRole(User::ROLE_SUPERADMIN);
        $restaurants = Restaurant::all();

        $this->render('admin/restaurants', [
            'title' => 'Restaurants Management | DinePOS',
            'restaurants' => $restaurants,
        ]);
    }

    /**
     * Create Restaurant Form
     */
    public function restaurantCreate(): void {
        $this->requireRole(User::ROLE_SUPERADMIN);

        $this->render('admin/restaurant_form', [
            'title' => 'Create Restaurant | DinePOS',
            'restaurant' => null,
            'isEdit' => false,
        ]);
    }

    /**
     * Store Restaurant
     */
    public function restaurantStore(): void {
        $user = $this->requireRole(User::ROLE_SUPERADMIN);
        $this->validateCsrf();

        $name = trim((string)($_POST['name'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));
        $address = trim((string)($_POST['address'] ?? ''));
        $timezone = trim((string)($_POST['timezone'] ?? 'Asia/Kolkata'));
        $status = (string)($_POST['status'] ?? 'active');

        if (empty($name)) {
            $this->redirect('/admin/restaurants/create', 'danger', 'Restaurant name is required.');
        }

        try {
            $restId = Restaurant::create([
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'timezone' => $timezone,
                'status' => $status,
            ]);

            AuditLog::log(AuditLog::ACTION_SETTINGS_UPDATE, 'restaurant', $restId, $restId, $user['id'], ['name' => $name]);

            $this->redirect('/admin/restaurants', 'success', "Restaurant '{$name}' created successfully.");
        } catch (Throwable $e) {
            $this->redirect('/admin/restaurants/create', 'danger', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Edit Restaurant Form
     */
    public function restaurantEdit(): void {
        $this->requireRole(User::ROLE_SUPERADMIN);
        $id = (int)($_GET['id'] ?? 0);
        $restaurant = Restaurant::findById($id);

        if (!$restaurant) {
            $this->redirect('/admin/restaurants', 'danger', 'Restaurant not found.');
        }

        $this->render('admin/restaurant_form', [
            'title' => "Edit Restaurant - {$restaurant['name']} | DinePOS",
            'restaurant' => $restaurant,
            'isEdit' => true,
        ]);
    }

    /**
     * Update Restaurant
     */
    public function restaurantUpdate(): void {
        $user = $this->requireRole(User::ROLE_SUPERADMIN);
        $this->validateCsrf();
        $id = (int)($_POST['id'] ?? 0);

        $name = trim((string)($_POST['name'] ?? ''));
        $phone = trim((string)($_POST['phone'] ?? ''));
        $address = trim((string)($_POST['address'] ?? ''));
        $timezone = trim((string)($_POST['timezone'] ?? 'Asia/Kolkata'));
        $status = (string)($_POST['status'] ?? 'active');

        if (empty($name)) {
            $this->redirect("/admin/restaurants/edit?id={$id}", 'danger', 'Restaurant name is required.');
        }

        try {
            Restaurant::update($id, [
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'timezone' => $timezone,
                'status' => $status,
            ]);

            AuditLog::log(AuditLog::ACTION_SETTINGS_UPDATE, 'restaurant', $id, $id, $user['id'], ['name' => $name, 'status' => $status]);

            $this->redirect('/admin/restaurants', 'success', "Restaurant '{$name}' updated successfully.");
        } catch (Throwable $e) {
            $this->redirect("/admin/restaurants/edit?id={$id}", 'danger', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Toggle Restaurant Status
     */
    public function restaurantToggle(): void {
        $user = $this->requireRole(User::ROLE_SUPERADMIN);
        $this->validateCsrf();
        $id = (int)($_POST['id'] ?? 0);

        Restaurant::toggleStatus($id);
        AuditLog::log(AuditLog::ACTION_SETTINGS_UPDATE, 'restaurant', $id, $id, $user['id'], ['action' => 'toggle_status']);

        $this->redirect('/admin/restaurants', 'success', 'Restaurant status updated.');
    }

    /**
     * Global Users List
     */
    public function users(): void {
        $this->requireRole(User::ROLE_SUPERADMIN);
        $users = User::all();

        $this->render('admin/users', [
            'title' => 'Global Users | DinePOS',
            'users' => $users,
        ]);
    }

    /**
     * Create Global User Form
     */
    public function userCreate(): void {
        $this->requireRole(User::ROLE_SUPERADMIN);
        $restaurants = Restaurant::all(true);

        $this->render('admin/user_form', [
            'title' => 'Create User | DinePOS',
            'userObj' => null,
            'restaurants' => $restaurants,
            'isEdit' => false,
        ]);
    }

    /**
     * Store Global User
     */
    public function userStore(): void {
        $user = $this->requireRole(User::ROLE_SUPERADMIN);
        $this->validateCsrf();

        $username = strtolower(trim((string)($_POST['username'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        $role = (string)($_POST['role'] ?? User::ROLE_CASHIER);
        $restaurantId = !empty($_POST['restaurant_id']) ? (int)$_POST['restaurant_id'] : null;
        $status = (string)($_POST['status'] ?? 'active');

        if ($role !== User::ROLE_SUPERADMIN && empty($restaurantId)) {
            $this->redirect('/admin/users/create', 'danger', 'Restaurant is required for manager and cashier roles.');
        }

        try {
            $newUserId = User::create([
                'restaurant_id' => $restaurantId,
                'username' => $username,
                'password' => $password,
                'role' => $role,
                'status' => $status,
            ]);

            AuditLog::log(AuditLog::ACTION_USER_CREATE, 'user', $newUserId, $restaurantId, $user['id'], ['username' => $username, 'role' => $role]);

            $this->redirect('/admin/users', 'success', "User '{$username}' created successfully.");
        } catch (Throwable $e) {
            $this->redirect('/admin/users/create', 'danger', $e->getMessage());
        }
    }

    /**
     * Edit Global User Form
     */
    public function userEdit(): void {
        $this->requireRole(User::ROLE_SUPERADMIN);
        $id = (int)($_GET['id'] ?? 0);
        $userObj = User::findById($id);

        if (!$userObj) {
            $this->redirect('/admin/users', 'danger', 'User not found.');
        }

        $restaurants = Restaurant::all(true);

        $this->render('admin/user_form', [
            'title' => "Edit User - {$userObj['username']} | DinePOS",
            'userObj' => $userObj,
            'restaurants' => $restaurants,
            'isEdit' => true,
        ]);
    }

    /**
     * Update Global User
     */
    public function userUpdate(): void {
        $user = $this->requireRole(User::ROLE_SUPERADMIN);
        $this->validateCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $userObj = User::findById($id);

        if (!$userObj) {
            $this->redirect('/admin/users', 'danger', 'User not found.');
        }

        $role = (string)($_POST['role'] ?? $userObj['role']);
        $status = (string)($_POST['status'] ?? $userObj['status']);
        $password = (string)($_POST['password'] ?? '');

        try {
            User::update($id, [
                'role' => $role,
                'status' => $status,
                'password' => $password !== '' ? $password : null,
            ]);

            AuditLog::log(AuditLog::ACTION_USER_EDIT, 'user', $id, $userObj['restaurant_id'] ? (int)$userObj['restaurant_id'] : null, $user['id'], ['username' => $userObj['username']]);

            $this->redirect('/admin/users', 'success', "User '{$userObj['username']}' updated successfully.");
        } catch (Throwable $e) {
            $this->redirect("/admin/users/edit?id={$id}", 'danger', $e->getMessage());
        }
    }
}
