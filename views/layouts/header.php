<?php
/**
 * Header Layout Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
$currentUser = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="DinePOS">
    <link rel="manifest" href="<?= url('manifest.json') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= asset('icons/icon.svg') ?>">
    <link rel="apple-touch-icon" href="<?= asset('icons/icon-192.png') ?>">

    <title><?= e($title ?? 'DinePOS - Restaurant Billing') ?></title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css?v=' . filemtime(ROOT_PATH . '/public/assets/css/app.css')) ?>">
</head>
<body>

<?php if ($currentUser): ?>
<!-- Global Desktop Navigation -->
<nav class="app-navbar">
    <div class="container-fluid d-flex justify-content-between align-items-center" style="max-width: 1400px;">
        <a href="<?= url('/') ?>" class="brand">
            <div class="brand-icon-box">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <span>DinePOS</span>
            <?php if (!empty($currentUser['restaurant_name'])): ?>
                <span class="d-none d-md-inline text-muted fw-normal" style="font-size: 0.85rem;">| <?= e($currentUser['restaurant_name']) ?></span>
            <?php endif; ?>
            <span class="badge-role"><?= e($currentUser['role']) ?></span>
        </a>

        <!-- Desktop Navigation Links -->
        <div class="app-nav-links desktop-only">
            <?php if ($currentUser['role'] === User::ROLE_CASHIER): ?>
                <a href="<?= url('cashier/order') ?>">New Order</a>
                <a href="<?= url('cashier/summary') ?>">Today's Summary</a>
            <?php elseif ($currentUser['role'] === User::ROLE_MANAGER): ?>
                <a href="<?= url('manager/dashboard') ?>">Dashboard</a>
                <a href="<?= url('cashier/order') ?>">New Order</a>
                <a href="<?= url('manager/orders') ?>">Orders</a>
                <a href="<?= url('manager/items') ?>">Items</a>
                <a href="<?= url('manager/reports') ?>">Reports</a>
                <a href="<?= url('manager/exports') ?>">Exports</a>
                <a href="<?= url('manager/users') ?>">Staff</a>
                <a href="<?= url('manager/audit') ?>">Audit</a>
                <a href="<?= url('manager/settings') ?>">Settings</a>
            <?php elseif ($currentUser['role'] === User::ROLE_SUPERADMIN): ?>
                <a href="<?= url('admin/dashboard') ?>">Dashboard</a>
                <a href="<?= url('admin/restaurants') ?>">Restaurants</a>
                <a href="<?= url('admin/users') ?>">Users</a>
            <?php endif; ?>
            
            <a href="<?= url('logout') ?>" class="text-danger">Logout (<?= e($currentUser['username']) ?>)</a>
        </div>
    </div>
</nav>
<?php endif; ?>

<main class="container-fluid py-3 flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/flash.php'; ?>
