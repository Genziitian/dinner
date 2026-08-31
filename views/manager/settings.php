<?php
/**
 * My Profile & Outlet Details View Template
 * Matches Android Native App UI 1:1 (Dynamic Data)
 */
declare(strict_types=1);
$currentUser = current_user();
$role = strtolower($currentUser['role'] ?? 'manager');

$roleDetails = match($role) {
    'superadmin' => ['title' => 'Master Super Administrator', 'icon' => '👑', 'badgeColor' => '#f59e0b'],
    'manager' => ['title' => 'Restaurant General Manager', 'icon' => '👔', 'badgeColor' => '#2563eb'],
    'cashier' => ['title' => 'POS Billing Operator & Cashier', 'icon' => '🛒', 'badgeColor' => '#10b981'],
    default => ['title' => 'Staff Member', 'icon' => '👤', 'badgeColor' => '#64748b']
};
?>

<div class="app-container">
    <!-- Top App Bar Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= url($role === 'cashier' ? 'cashier/order' : 'manager/dashboard') ?>" class="top-bar-back-btn" title="Back">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h4 class="top-bar-title m-0">My Profile</h4>
        </div>
        <div class="top-bar-actions">
            <a href="<?= url('logout') ?>" class="top-bar-icon-btn red" title="Logout">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </a>
        </div>
    </div>

    <!-- 1. User Header & Role Designation Card (Screenshot 5) -->
    <div class="profile-hero-card">
        <div class="profile-avatar-circle">
            <?= $roleDetails['icon'] ?>
        </div>
        <div class="profile-username"><?= e($currentUser['username']) ?></div>
        
        <div class="profile-role-pill">
            <span><?= $roleDetails['icon'] ?></span>
            <span><?= e($roleDetails['title']) ?></span>
        </div>

        <!-- 3-Column Stats Row -->
        <div class="profile-stats-row">
            <div class="profile-stat-col">
                <div class="profile-stat-label">User ID</div>
                <div class="profile-stat-val">#<?= (int)$currentUser['id'] ?></div>
            </div>
            <div class="profile-stat-col">
                <div class="profile-stat-label">Account Status</div>
                <div class="profile-stat-val active-green">🟢 Active</div>
            </div>
            <div class="profile-stat-col">
                <div class="profile-stat-label">Security</div>
                <div class="profile-stat-val">🔒 Encrypted</div>
            </div>
        </div>

        <!-- Authority Notice -->
        <div class="profile-lock-notice">
            <span style="font-size: 1rem;">🔒</span>
            <span>
                <?= $role === 'manager' 
                    ? 'Account & credentials managed by Super Administrator.' 
                    : ($role === 'cashier' ? 'Account & credentials managed by Restaurant Manager.' : 'Platform-wide master access privileges.') ?>
            </span>
        </div>
    </div>

    <!-- 2. Restaurant Outlet Details Card (Screenshot 5) -->
    <div class="outlet-details-card">
        <div class="outlet-card-header">
            <h6 class="outlet-card-title">
                <span>🏢</span>
                <span>Restaurant Outlet Details</span>
            </h6>
            <span class="read-only-pill">🔒 Read-Only</span>
        </div>

        <div class="outlet-info-list">
            <div class="outlet-info-item">
                <span class="outlet-info-icon">🏪</span>
                <div>
                    <div class="outlet-info-label">Restaurant Name</div>
                    <div class="outlet-info-value"><?= e($restaurant['name'] ?? 'Main Outlet') ?></div>
                </div>
            </div>

            <div class="outlet-info-item">
                <span class="outlet-info-icon">📍</span>
                <div>
                    <div class="outlet-info-label">Outlet Address</div>
                    <div class="outlet-info-value"><?= e(!empty($restaurant['address']) ? $restaurant['address'] : 'Address configured by Admin') ?></div>
                </div>
            </div>

            <div class="outlet-info-item">
                <span class="outlet-info-icon">📞</span>
                <div>
                    <div class="outlet-info-label">Contact Phone</div>
                    <div class="outlet-info-value"><?= e(!empty($restaurant['phone']) ? $restaurant['phone'] : 'Not provided') ?></div>
                </div>
            </div>
        </div>

        <div class="outlet-footer-note">
            💡 Branch details & locations can only be modified by Super Administrator.
        </div>
    </div>

    <!-- 3. Staff & Export Management Tools -->
    <?php if ($role === 'manager'): ?>
        <div class="mb-3">
            <div class="section-header">
                <h6 class="section-title">👔 STAFF & EXPORT MANAGEMENT</h6>
            </div>

            <a href="<?= url('manager/users') ?>" class="profile-action-card">
                <div class="profile-action-left">
                    <span class="profile-action-icon">👥</span>
                    <div>
                        <div class="profile-action-title">Staff Management (Cashiers)</div>
                        <div class="profile-action-sub">Create & manage cashier operator staff</div>
                    </div>
                </div>
                <span class="text-secondary fw-bold">›</span>
            </a>

            <a href="<?= url('manager/exports') ?>" class="profile-action-card">
                <div class="profile-action-left">
                    <span class="profile-action-icon">📥</span>
                    <div>
                        <div class="profile-action-title">Export Sales Reports (CSV)</div>
                        <div class="profile-action-sub">Download Excel/CSV reports for any date range</div>
                    </div>
                </div>
                <span class="text-secondary fw-bold">›</span>
            </a>
        </div>
    <?php endif; ?>

    <!-- 4. Sign Out Button -->
    <a href="<?= url('logout') ?>" class="sign-out-btn-card">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        <span>Sign Out Account</span>
    </a>
</div>
