<?php
/**
 * Android Native Design System Footer Layout Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
$currentUser = current_user();
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
</main>

<?php if ($currentUser): ?>
<!-- Android Material 3 Bottom Navigation Dock -->
<div class="app-bottom-nav">
    <div class="app-bottom-nav-surface">
        <?php if ($currentUser['role'] === User::ROLE_CASHIER): ?>
            <!-- Cashier Navigation: POS | Orders | Shift | Profile -->
            <a href="<?= url('cashier/order') ?>" class="app-nav-item <?= (str_contains($currentUri, 'cashier/order') || $currentUri === '/') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="nav-label">POS</span>
            </a>
            <a href="<?= url('manager/orders') ?>" class="app-nav-item <?= str_contains($currentUri, 'manager/orders') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="nav-label">Orders</span>
            </a>
            <a href="<?= url('cashier/summary') ?>" class="app-nav-item <?= str_contains($currentUri, 'cashier/summary') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="nav-label">Shift</span>
            </a>
            <a href="<?= url('manager/settings') ?>" class="app-nav-item <?= (str_contains($currentUri, 'manager/settings') || str_contains($currentUri, 'profile')) ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span class="nav-label">Profile</span>
            </a>

        <?php elseif ($currentUser['role'] === User::ROLE_MANAGER): ?>
            <!-- Manager Navigation: Home | Orders | Elevated Center (+) FAB | Menu | Profile -->
            <a href="<?= url('manager/dashboard') ?>" class="app-nav-item <?= (str_contains($currentUri, 'manager/dashboard') || $currentUri === '/') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <span class="nav-label">Home</span>
            </a>

            <a href="<?= url('manager/orders') ?>" class="app-nav-item <?= str_contains($currentUri, 'manager/orders') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="nav-label">Orders</span>
            </a>

            <!-- Elevated Center '+' Floating Action Button -->
            <div class="app-nav-fab-wrap">
                <a href="<?= url('cashier/order') ?>" class="app-nav-fab-btn" title="New Order (POS Billing)">
                    <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                </a>
            </div>

            <a href="<?= url('manager/items') ?>" class="app-nav-item <?= str_contains($currentUri, 'manager/items') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="nav-label">Menu</span>
            </a>

            <a href="<?= url('manager/settings') ?>" class="app-nav-item <?= (str_contains($currentUri, 'manager/settings') || str_contains($currentUri, 'profile')) ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span class="nav-label">Profile</span>
            </a>

        <?php elseif ($currentUser['role'] === User::ROLE_SUPERADMIN): ?>
            <!-- Super Admin Navigation: Admin | Restaurants | Users | Profile -->
            <a href="<?= url('admin/dashboard') ?>" class="app-nav-item <?= (str_contains($currentUri, 'admin/dashboard') || $currentUri === '/') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </div>
                <span class="nav-label">Admin</span>
            </a>
            <a href="<?= url('admin/restaurants') ?>" class="app-nav-item <?= str_contains($currentUri, 'admin/restaurants') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="nav-label">Restaurants</span>
            </a>
            <a href="<?= url('admin/users') ?>" class="app-nav-item <?= str_contains($currentUri, 'admin/users') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="nav-label">Users</span>
            </a>
            <a href="<?= url('manager/settings') ?>" class="app-nav-item <?= str_contains($currentUri, 'profile') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span class="nav-label">Profile</span>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Core JavaScript -->
<script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('js/qrcode.min.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>

</body>
</html>
