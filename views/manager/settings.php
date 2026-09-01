<?php
/**
 * Modern Profile & Outlet Details View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
$currentUser = current_user();
$role = strtolower($currentUser['role'] ?? 'manager');

$roleDetails = match($role) {
    'superadmin' => ['title' => 'Master Super Administrator', 'badgeClass' => 'badge-role-superadmin', 'gradient' => 'linear-gradient(135deg, #8b5cf6, #6d28d9)'],
    'manager' => ['title' => 'Restaurant General Manager', 'badgeClass' => 'badge-role-manager', 'gradient' => 'linear-gradient(135deg, #3b82f6, #1d4ed8)'],
    'cashier' => ['title' => 'POS Billing Operator & Cashier', 'badgeClass' => 'badge-role-cashier', 'gradient' => 'linear-gradient(135deg, #10b981, #047857)'],
    default => ['title' => 'Staff Member', 'badgeClass' => 'badge-role-manager', 'gradient' => 'linear-gradient(135deg, #64748b, #475569)']
};
?>

<div class="admin-container" style="max-width: 800px;">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= url($role === 'superadmin' ? 'admin/dashboard' : ($role === 'cashier' ? 'cashier/order' : 'manager/dashboard')) ?>" class="admin-btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.82rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Dashboard</span>
            </a>
        </div>
        <a href="<?= url('logout') ?>" class="nav-logout-btn" title="Logout">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        </a>
    </div>

    <!-- 1. Profile Hero Card -->
    <div class="admin-card text-center p-4 p-md-5 mb-4">
        <div class="nav-user-avatar mx-auto mb-3" style="width: 72px; height: 72px; font-size: 1.65rem; border-radius: 20px; box-shadow: 0 8px 24px rgba(99, 102, 241, 0.25); background: <?= $roleDetails['gradient'] ?>;">
            <?= strtoupper(substr($currentUser['username'], 0, 2)) ?>
        </div>

        <h3 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;"><?= e($currentUser['username']) ?></h3>
        
        <div class="mt-2 mb-4">
            <span class="badge-role <?= $roleDetails['badgeClass'] ?>" style="font-size: 0.85rem; padding: 4px 14px; border-radius: 50rem;">
                <?= e($roleDetails['title']) ?>
            </span>
        </div>

        <!-- 3-Column Stats Row -->
        <div class="row g-2 justify-content-center mb-4">
            <div class="col-4 col-sm-3">
                <div class="p-3 bg-light rounded-3 border" style="border-color: #f1f5f9 !important;">
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">User ID</div>
                    <div class="fw-bold text-dark fs-6 mt-1">#<?= (int)$currentUser['id'] ?></div>
                </div>
            </div>
            <div class="col-4 col-sm-3">
                <div class="p-3 bg-light rounded-3 border" style="border-color: #f1f5f9 !important;">
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Status</div>
                    <div class="fw-bold text-success fs-6 mt-1">🟢 Active</div>
                </div>
            </div>
            <div class="col-4 col-sm-3">
                <div class="p-3 bg-light rounded-3 border" style="border-color: #f1f5f9 !important;">
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Security</div>
                    <div class="fw-bold text-dark fs-6 mt-1">🔒 Bcrypt</div>
                </div>
            </div>
        </div>

        <div class="p-3 rounded-3 bg-light border text-start d-flex align-items-center gap-2" style="font-size: 0.85rem; color: #475569;">
            <svg width="18" height="18" fill="none" stroke="#64748b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>
                <?= $role === 'superadmin' 
                    ? 'Master Administrator account with unrestricted platform-wide privileges across all tenant outlets.' 
                    : ($role === 'manager' ? 'Restaurant Manager privileges restricted to assigned branch outlet.' : 'Cashier billing access restricted to assigned branch outlet.') ?>
            </span>
        </div>
    </div>

    <!-- 2. Super Admin Quick Navigation Links OR Outlet Details -->
    <?php if ($role === 'superadmin'): ?>
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Super Admin Quick Tools</h5>
                <span class="badge bg-purple-subtle text-purple px-2 py-1" style="background: #f3e8ff; color: #7e22ce;">System Control</span>
            </div>
            <div class="p-3 d-flex flex-column gap-2">
                <a href="<?= url('admin/restaurants') ?>" class="admin-btn-secondary justify-content-between p-3" style="border-radius: 12px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-stat-icon orange" style="width: 38px; height: 38px; border-radius: 10px;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div class="text-start">
                            <div class="fw-bold text-dark">Tenant Restaurants Directory</div>
                            <small class="text-muted">Create, edit, or configure restaurant branches</small>
                        </div>
                    </div>
                    <span class="text-muted fw-bold">→</span>
                </a>

                <a href="<?= url('admin/users') ?>" class="admin-btn-secondary justify-content-between p-3" style="border-radius: 12px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-stat-icon blue" style="width: 38px; height: 38px; border-radius: 10px;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div class="text-start">
                            <div class="fw-bold text-dark">Global User Accounts Directory</div>
                            <small class="text-muted">Provision staff accounts, roles, and branch assignments</small>
                        </div>
                    </div>
                    <span class="text-muted fw-bold">→</span>
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Outlet Details for Manager / Cashier -->
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Restaurant Outlet Details</h5>
                <span class="badge bg-light text-muted border px-2.5 py-1">🔒 Assigned Branch</span>
            </div>

            <div class="p-4 d-flex flex-column gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-stat-icon orange" style="width: 40px; height: 40px; border-radius: 10px;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Restaurant Name</small>
                        <div class="fw-bold text-dark fs-6"><?= e($restaurant['name'] ?? 'Main Outlet') ?></div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="admin-stat-icon blue" style="width: 40px; height: 40px; border-radius: 10px;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Outlet Address</small>
                        <div class="fw-bold text-dark"><?= !empty($restaurant['address']) ? e($restaurant['address']) : 'Configured by Super Admin' ?></div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="admin-stat-icon emerald" style="width: 40px; height: 40px; border-radius: 10px;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Contact Phone</small>
                        <div class="fw-bold text-dark"><?= !empty($restaurant['phone']) ? e($restaurant['phone']) : 'Not configured' ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- 3. Sign Out Action Card -->
    <a href="<?= url('logout') ?>" class="admin-card text-decoration-none p-3 d-flex align-items-center justify-content-center gap-2 text-danger fw-bold border-danger-subtle bg-danger-subtle" style="border-radius: 14px; transition: all 0.15s ease;">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        <span>Sign Out Account</span>
    </a>
</div>
