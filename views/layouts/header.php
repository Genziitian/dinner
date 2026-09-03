<?php
/**
 * Android Native Design System Header Layout Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
$currentUser = current_user();
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <meta name="theme-color" content="#f8fafc">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="GI ORDER">
    <link rel="manifest" href="<?= url('manifest.json') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= asset('icons/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= asset('icons/favicon-16x16.png') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= asset('icons/icon.svg') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= asset('icons/apple-touch-icon.png') ?>">
    <link rel="shortcut icon" href="<?= asset('icons/favicon.ico') ?>">

    <title><?= e($title ?? 'GI ORDER - Restaurant Billing') ?></title>

    <!-- Stylesheets & Core JS -->
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css?v=' . filemtime(ROOT_PATH . '/public/assets/css/app.css')) ?>">
    <script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>
</head>
<body>

<?php if ($currentUser): ?>
<!-- Global Desktop Navigation Header (>= 992px) -->
<nav class="app-navbar d-none d-lg-block">
    <div class="container-fluid d-flex justify-content-between align-items-center" style="max-width: 1280px; padding: 0 1.5rem;">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= url('/') ?>" class="brand text-decoration-none d-flex align-items-center gap-2">
                <div class="brand-icon-box">
                    <img src="<?= asset('icons/icon-192.png') ?>" alt="GI ORDER POS" class="brand-logo-img">
                </div>
                <span class="brand-title">GI ORDER POS</span>
                <span class="badge-role badge-role-<?= e($currentUser['role']) ?>">
                    <?= $currentUser['role'] === User::ROLE_SUPERADMIN ? 'SUPER ADMIN' : strtoupper(e($currentUser['role'])) ?>
                </span>
            </a>
        </div>

        <!-- Desktop Navigation Links -->
        <div class="app-nav-links d-flex align-items-center gap-1">
            <?php if (($currentUser['shop_type'] ?? '') === 'mill' && $currentUser['role'] !== User::ROLE_SUPERADMIN): ?>
                <a href="<?= url('mill/dashboard') ?>" class="nav-link-pill <?= (str_contains($currentUri, 'mill/dashboard') || $currentUri === '/') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span><?= __t('nav_dashboard') ?></span>
                </a>
                <a href="<?= url('mill/orders/new') ?>" class="nav-link-pill <?= str_contains($currentUri, 'mill/orders/new') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span><?= __t('nav_new_order') ?></span>
                </a>
                <a href="<?= url('mill/orders') ?>" class="nav-link-pill <?= (str_contains($currentUri, 'mill/orders') && !str_contains($currentUri, 'mill/orders/new')) ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span><?= __t('nav_orders') ?></span>
                </a>
                <a href="<?= url('mill/customers') ?>" class="nav-link-pill <?= str_contains($currentUri, 'mill/customers') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span><?= __t('nav_customers') ?></span>
                </a>
                <a href="<?= url('mill/services') ?>" class="nav-link-pill <?= str_contains($currentUri, 'mill/services') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><?= __t('nav_services') ?></span>
                </a>
                <a href="<?= url('mill/backup') ?>" class="nav-link-pill <?= str_contains($currentUri, 'mill/backup') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    <span><?= __t('nav_backup') ?></span>
                </a>
            <?php elseif ($currentUser['role'] === User::ROLE_CASHIER): ?>
                <a href="<?= url('cashier/order') ?>" class="nav-link-pill <?= (str_contains($currentUri, 'cashier/order') || $currentUri === '/') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>New Order</span>
                </a>
                <a href="<?= url('cashier/summary') ?>" class="nav-link-pill <?= str_contains($currentUri, 'cashier/summary') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Today's Summary</span>
                </a>
                <a href="<?= url('manager/orders') ?>" class="nav-link-pill <?= str_contains($currentUri, 'manager/orders') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Orders</span>
                </a>
            <?php elseif ($currentUser['role'] === User::ROLE_MANAGER): ?>
                <a href="<?= url('manager/dashboard') ?>" class="nav-link-pill <?= (str_contains($currentUri, 'manager/dashboard') || $currentUri === '/') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="<?= url('cashier/order') ?>" class="nav-link-pill <?= str_contains($currentUri, 'cashier/order') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>POS</span>
                </a>
                <a href="<?= url('manager/orders') ?>" class="nav-link-pill <?= str_contains($currentUri, 'manager/orders') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Orders</span>
                </a>
                <a href="<?= url('manager/items') ?>" class="nav-link-pill <?= str_contains($currentUri, 'manager/items') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Menu</span>
                </a>
                <a href="<?= url('manager/reports') ?>" class="nav-link-pill <?= str_contains($currentUri, 'manager/reports') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Reports</span>
                </a>
                <a href="<?= url('manager/users') ?>" class="nav-link-pill <?= str_contains($currentUri, 'manager/users') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Staff</span>
                </a>
            <?php elseif ($currentUser['role'] === User::ROLE_SUPERADMIN): ?>
                <a href="<?= url('admin/dashboard') ?>" class="nav-link-pill <?= (str_contains($currentUri, 'admin/dashboard') || $currentUri === '/') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="<?= url('admin/restaurants') ?>" class="nav-link-pill <?= str_contains($currentUri, 'admin/restaurants') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Restaurants</span>
                </a>
                <a href="<?= url('admin/users') ?>" class="nav-link-pill <?= str_contains($currentUri, 'admin/users') ? 'active' : '' ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Users</span>
                </a>
            <?php endif; ?>
        </div>

        <!-- Language Toggle & User Profile Pill & Sign Out -->
        <div class="d-flex align-items-center gap-2">
            <div class="d-flex align-items-center bg-white rounded border px-1 py-0.5" style="font-size: 0.76rem; font-weight: 600;">
                <a href="<?= url('mill/lang?lang=en') ?>" class="text-decoration-none px-1.5 py-0.5 rounded <?= current_lang() === 'en' ? 'bg-primary text-white' : 'text-secondary' ?>">EN</a>
                <span class="text-muted mx-0.5">|</span>
                <a href="<?= url('mill/lang?lang=hi') ?>" class="text-decoration-none px-1.5 py-0.5 rounded <?= current_lang() === 'hi' ? 'bg-primary text-white' : 'text-secondary' ?>">हिन्दी</a>
            </div>

            <a href="<?= url('manager/settings') ?>" class="nav-user-pill text-decoration-none <?= str_contains($currentUri, 'manager/settings') ? 'active' : '' ?>">
                <div class="nav-user-avatar">
                    <?= strtoupper(substr($currentUser['username'], 0, 2)) ?>
                </div>
                <div class="d-flex flex-column text-start d-none d-xl-flex">
                    <span class="nav-user-name"><?= e($currentUser['username']) ?></span>
                    <span class="nav-user-subtitle"><?= !empty($currentUser['restaurant_name']) ? e($currentUser['restaurant_name']) : 'Master Admin' ?></span>
                </div>
            </a>
            <a href="<?= url('logout') ?>" class="nav-logout-btn" title="Sign Out">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </a>
        </div>
    </div>
</nav>

<!-- Mobile Top Bar (< 992px) -->
<div class="app-mobile-top-bar d-lg-none">
    <div class="d-flex justify-content-between align-items-center px-3 py-2">
        <a href="<?= url('/') ?>" class="brand text-decoration-none d-flex align-items-center gap-2">
            <div class="brand-icon-box sm">
                <img src="<?= asset('icons/icon-192.png') ?>" alt="GI ORDER POS" class="brand-logo-img">
            </div>
            <span class="brand-title sm"><?= !empty($currentUser['restaurant_name']) ? e($currentUser['restaurant_name']) : 'GI ORDER' ?></span>
            <span class="badge-role sm"><?= ($currentUser['shop_type'] ?? '') === 'mill' ? 'MILL' : strtoupper(e($currentUser['role'])) ?></span>
        </a>
        <div class="d-flex align-items-center gap-2">
            <div class="d-flex align-items-center bg-white rounded border px-1 py-0.5" style="font-size: 0.72rem; font-weight: 600;">
                <a href="<?= url('mill/lang?lang=en') ?>" class="text-decoration-none px-1.5 py-0.5 rounded <?= current_lang() === 'en' ? 'bg-primary text-white' : 'text-secondary' ?>">EN</a>
                <span class="text-muted mx-0.5">|</span>
                <a href="<?= url('mill/lang?lang=hi') ?>" class="text-decoration-none px-1.5 py-0.5 rounded <?= current_lang() === 'hi' ? 'bg-primary text-white' : 'text-secondary' ?>">हिन्दी</a>
            </div>
            <a href="<?= url('logout') ?>" class="top-bar-icon-btn red" title="Logout">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<main class="flex-grow-1">
    <div class="container-fluid px-2 px-md-3">
        <?php require ROOT_PATH . '/views/layouts/flash.php'; ?>
    </div>
