<?php
/**
 * Super Admin Dashboard View Template
 * GI ORDER POS
 */
declare(strict_types=1);
?>

<div class="admin-container">
    <!-- Header Section -->
    <div class="admin-header-box">
        <div>
            <h2 class="admin-page-title">Super Admin</h2>
            <p class="admin-page-subtitle">System & Multi-Tenant Management</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= url('admin/restaurants/create') ?>" class="admin-btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                <span>New Restaurant</span>
            </a>
            <a href="<?= url('admin/users/create') ?>" class="admin-btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>New User</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid (2x2 on mobile, 4-col on desktop) -->
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <div class="col-6 col-lg-3">
            <a href="<?= url('admin/restaurants') ?>" class="text-decoration-none">
                <div class="admin-stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="admin-stat-label">Outlets</span>
                        <div class="admin-stat-icon orange">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                    </div>
                    <div class="admin-stat-val"><?= (int)($activeRestaurants ?? $totalRestaurants) ?></div>
                    <div class="text-muted" style="font-size: 0.76rem;">
                        <span class="text-success fw-bold"><?= (int)($activeRestaurants ?? $totalRestaurants) ?> Active</span> of <?= (int)$totalRestaurants ?>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-3">
            <a href="<?= url('admin/users') ?>" class="text-decoration-none">
                <div class="admin-stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="admin-stat-label">Total Users</span>
                        <div class="admin-stat-icon blue">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                    </div>
                    <div class="admin-stat-val"><?= (int)$totalUsers ?></div>
                    <div class="text-muted" style="font-size: 0.76rem;">
                        <span class="text-primary fw-bold"><?= (int)($superAdminCount ?? 1) ?></span> Super Admin
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-3">
            <a href="<?= url('admin/users') ?>" class="text-decoration-none">
                <div class="admin-stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="admin-stat-label">Managers</span>
                        <div class="admin-stat-icon purple">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <div class="admin-stat-val"><?= (int)($managerCount ?? 0) ?></div>
                    <div class="text-muted" style="font-size: 0.76rem;">
                        Outlet managers
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-3">
            <a href="<?= url('admin/users') ?>" class="text-decoration-none">
                <div class="admin-stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="admin-stat-label">Cashiers</span>
                        <div class="admin-stat-icon emerald">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>
                    <div class="admin-stat-val"><?= (int)($cashierCount ?? 0) ?></div>
                    <div class="text-muted" style="font-size: 0.76rem;">
                        Billing staff
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Tenant Restaurants Grid -->
    <div class="admin-card mb-3 mb-md-4">
        <div class="admin-card-header">
            <div class="d-flex align-items-center gap-2">
                <h5 class="admin-card-title">Tenant Restaurants</h5>
                <span class="badge bg-secondary-subtle text-secondary rounded-pill fw-bold" style="font-size: 0.78rem;">
                    <?= count($restaurants) ?>
                </span>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= url('admin/restaurants') ?>" class="admin-btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                    View All
                </a>
                <a href="<?= url('admin/restaurants/create') ?>" class="admin-btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                    + Add Branch
                </a>
            </div>
        </div>

        <div class="p-3 p-md-3">
            <?php if (empty($restaurants)): ?>
                <div class="text-center py-4">
                    <div class="mb-2">
                        <div class="brand-icon-box mx-auto" style="width: 48px; height: 48px; border-radius: 12px;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">No restaurants registered</h6>
                    <p class="text-muted small mb-3">Create your first tenant restaurant to start provisioning staff.</p>
                    <a href="<?= url('admin/restaurants/create') ?>" class="admin-btn-primary">+ Create Restaurant</a>
                </div>
            <?php else: ?>
                <div class="row g-2 g-md-3">
                    <?php foreach ($restaurants as $r): ?>
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="restaurant-grid-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="restaurant-avatar">
                                            <?= strtoupper(substr($r['name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark m-0" style="font-size: 0.95rem;"><?= e($r['name']) ?></h6>
                                            <small class="text-muted" style="font-size: 0.76rem;">ID #<?= (int)$r['id'] ?></small>
                                        </div>
                                    </div>
                                    <span class="admin-pill-status <?= $r['status'] === 'active' ? 'active' : 'inactive' ?>">
                                        <span class="dot"></span>
                                        <?= ucfirst(e($r['status'])) ?>
                                    </span>
                                </div>

                                <div class="d-flex flex-column gap-1.5 my-2 flex-grow-1" style="font-size: 0.82rem; color: #475569;">
                                    <?php if (!empty($r['phone'])): ?>
                                        <div class="d-flex align-items-center gap-1.5">
                                            <svg width="14" height="14" fill="none" stroke="#64748b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            <span><?= e($r['phone']) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($r['address'])): ?>
                                        <div class="d-flex align-items-start gap-1.5">
                                            <svg width="14" height="14" fill="none" stroke="#64748b" viewBox="0 0 24 24" class="mt-0.5 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span class="text-truncate-2"><?= e($r['address']) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex align-items-center gap-1.5 mt-1">
                                        <svg width="14" height="14" fill="none" stroke="#64748b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 0.72rem; font-family: monospace;">
                                            <?= e($r['timezone']) ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="pt-2 border-top mt-auto">
                                    <a href="<?= url('admin/restaurants/edit?id=' . $r['id']) ?>" class="admin-btn-secondary w-100 justify-content-center" style="padding: 0.4rem; font-size: 0.8rem;">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Global Users Section -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="d-flex align-items-center gap-2">
                <h5 class="admin-card-title">Recent Users</h5>
                <span class="badge bg-secondary-subtle text-secondary rounded-pill fw-bold" style="font-size: 0.78rem;">
                    <?= count($users) ?>
                </span>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= url('admin/users') ?>" class="admin-btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                    View All
                </a>
                <a href="<?= url('admin/users/create') ?>" class="admin-btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                    + New User
                </a>
            </div>
        </div>

        <!-- Desktop Table View (>= 768px) -->
        <div class="table-responsive d-none d-md-block">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="ps-3">User</th>
                        <th>Role</th>
                        <th>Assigned Branch</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No users found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach (array_slice($users, 0, 6) as $u): ?>
                            <?php
                                $roleGradient = match($u['role']) {
                                    'superadmin' => 'linear-gradient(135deg, #8b5cf6, #6d28d9)',
                                    'manager' => 'linear-gradient(135deg, #3b82f6, #1d4ed8)',
                                    'cashier' => 'linear-gradient(135deg, #10b981, #047857)',
                                    default => 'linear-gradient(135deg, #64748b, #475569)',
                                };
                            ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="nav-user-avatar" style="width: 30px; height: 30px; font-size: 0.75rem; background: <?= $roleGradient ?>;">
                                            <?= strtoupper(substr($u['username'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.88rem;"><?= e($u['username']) ?></div>
                                            <small class="text-muted" style="font-size: 0.74rem;">ID #<?= (int)$u['id'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-role badge-role-<?= e($u['role']) ?>">
                                        <?= strtoupper(e($u['role'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($u['restaurant_name'])): ?>
                                        <span class="d-inline-flex align-items-center gap-1 badge bg-light text-dark border px-2 py-1 fw-semibold" style="font-size: 0.78rem;">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            <?= e($u['restaurant_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="d-inline-flex align-items-center gap-1 badge bg-secondary-subtle text-secondary px-2 py-1" style="font-size: 0.76rem;">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Global (All Outlets)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="admin-pill-status <?= $u['status'] === 'active' ? 'active' : 'inactive' ?>">
                                        <span class="dot"></span>
                                        <?= ucfirst(e($u['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted" style="font-size: 0.78rem;"><?= format_date($u['created_at']) ?></small>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="<?= url('admin/users/edit?id=' . $u['id']) ?>" class="admin-btn-secondary" style="padding: 0.3rem 0.65rem; font-size: 0.76rem;">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile List View (< 768px) -->
        <div class="d-md-none p-2.5">
            <?php if (empty($users)): ?>
                <div class="text-center py-4 text-muted small">No users found.</div>
            <?php else: ?>
                <?php foreach (array_slice($users, 0, 6) as $u): ?>
                    <?php
                        $roleGradient = match($u['role']) {
                            'superadmin' => 'linear-gradient(135deg, #8b5cf6, #6d28d9)',
                            'manager' => 'linear-gradient(135deg, #3b82f6, #1d4ed8)',
                            'cashier' => 'linear-gradient(135deg, #10b981, #047857)',
                            default => 'linear-gradient(135deg, #64748b, #475569)',
                        };
                    ?>
                    <div class="admin-user-card-mobile">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="nav-user-avatar" style="width: 32px; height: 32px; font-size: 0.76rem; background: <?= $roleGradient ?>;">
                                    <?= strtoupper(substr($u['username'], 0, 2)) ?>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?= e($u['username']) ?></div>
                                    <small class="text-muted" style="font-size: 0.72rem;">ID #<?= (int)$u['id'] ?></small>
                                </div>
                            </div>
                            <span class="admin-pill-status <?= $u['status'] === 'active' ? 'active' : 'inactive' ?>">
                                <span class="dot"></span>
                                <?= ucfirst(e($u['status'])) ?>
                            </span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1.5 pt-1.5 border-top">
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <span class="badge-role badge-role-<?= e($u['role']) ?>" style="font-size: 0.72rem; padding: 2px 7px;">
                                    <?= strtoupper(e($u['role'])) ?>
                                </span>
                                <?php if (!empty($u['restaurant_name'])): ?>
                                    <span class="badge bg-light text-dark border px-1.5 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                                        <?= e($u['restaurant_name']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary px-1.5 py-0.5" style="font-size: 0.72rem;">
                                        Global
                                    </span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= url('admin/users/edit?id=' . $u['id']) ?>" class="admin-btn-secondary" style="padding: 0.25rem 0.65rem; font-size: 0.74rem;">
                                Edit
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
