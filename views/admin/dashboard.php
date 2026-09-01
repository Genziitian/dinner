<?php
/**
 * Modern Super Admin Dashboard View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="admin-container">
    <!-- Header Section -->
    <div class="admin-header-box">
        <div>
            <h2 class="admin-page-title">Super Admin Dashboard</h2>
            <p class="admin-page-subtitle">Overview of all tenant restaurants, staff provisioning, and system status</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= url('admin/restaurants/create') ?>" class="admin-btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                <span>New Restaurant</span>
            </a>
            <a href="<?= url('admin/users/create') ?>" class="admin-btn-secondary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>New User</span>
            </a>
        </div>
    </div>

    <!-- 4 Stats Cards Grid -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="<?= url('admin/restaurants') ?>" class="text-decoration-none">
                <div class="admin-stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="admin-stat-label">Restaurants</span>
                        <div class="admin-stat-icon orange">
                            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                    </div>
                    <div class="admin-stat-val"><?= (int)$totalRestaurants ?></div>
                    <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.8rem;">
                        <span class="text-success fw-bold">Active Outlets</span> · Manage Directory →
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="<?= url('admin/users') ?>" class="text-decoration-none">
                <div class="admin-stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="admin-stat-label">Global Users</span>
                        <div class="admin-stat-icon blue">
                            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                    </div>
                    <div class="admin-stat-val"><?= (int)$totalUsers ?></div>
                    <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.8rem;">
                        <span class="text-primary fw-bold">Staff Accounts</span> · View All Users →
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="admin-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="admin-stat-label">System Health</span>
                    <div class="admin-stat-icon emerald">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="admin-stat-val text-success" style="font-size: 1.55rem; margin-top: 0.75rem;">Operational</div>
                <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.8rem;">
                    <span class="badge bg-success-subtle text-success p-1 px-2 rounded-pill fw-bold" style="font-size: 10px;">99.9% Uptime</span>
                    <span>Database Live</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="admin-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="admin-stat-label">Data Isolation</span>
                    <div class="admin-stat-icon purple">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                </div>
                <div class="admin-stat-val text-dark" style="font-size: 1.55rem; margin-top: 0.75rem;">Multi-Tenant</div>
                <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.8rem;">
                    <span class="badge bg-purple-subtle text-purple p-1 px-2 rounded-pill fw-bold" style="font-size: 10px; background: #f3e8ff; color: #7e22ce;">Strict Scoping</span>
                    <span>Row Security</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Restaurants Directory Grid -->
    <div class="admin-card mb-4">
        <div class="admin-card-header">
            <div>
                <h5 class="admin-card-title">Tenant Restaurants</h5>
                <small class="text-muted">Registered branches and billing configurations</small>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= url('admin/restaurants') ?>" class="admin-btn-secondary" style="padding: 0.4rem 0.85rem; font-size: 0.82rem;">
                    Manage All (<?= count($restaurants) ?>)
                </a>
                <a href="<?= url('admin/restaurants/create') ?>" class="admin-btn-primary" style="padding: 0.4rem 0.85rem; font-size: 0.82rem;">
                    + Add Branch
                </a>
            </div>
        </div>

        <div class="p-3 p-md-4">
            <?php if (empty($restaurants)): ?>
                <div class="text-center py-5">
                    <div class="mb-3">
                        <div class="brand-icon-box mx-auto" style="width: 56px; height: 56px; border-radius: 16px;">
                            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">No restaurants registered yet</h6>
                    <p class="text-muted small mb-3">Create your first tenant restaurant to start provisioning managers and cashiers.</p>
                    <a href="<?= url('admin/restaurants/create') ?>" class="admin-btn-primary">+ Create First Restaurant</a>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($restaurants as $r): ?>
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="restaurant-grid-card">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="restaurant-avatar">
                                            <?= strtoupper(substr($r['name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark m-0 fs-6"><?= e($r['name']) ?></h6>
                                            <small class="text-muted">Branch ID: #<?= (int)$r['id'] ?></small>
                                        </div>
                                    </div>
                                    <span class="admin-pill-status <?= $r['status'] === 'active' ? 'active' : 'inactive' ?>">
                                        <span class="dot"></span>
                                        <?= ucfirst(e($r['status'])) ?>
                                    </span>
                                </div>

                                <div class="d-flex flex-column gap-2 mb-3 flex-grow-1" style="font-size: 0.85rem; color: #475569;">
                                    <?php if (!empty($r['phone'])): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <svg width="15" height="15" fill="none" stroke="#64748b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            <span><?= e($r['phone']) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($r['address'])): ?>
                                        <div class="d-flex align-items-start gap-2">
                                            <svg width="15" height="15" fill="none" stroke="#64748b" viewBox="0 0 24 24" class="mt-0.5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span class="text-truncate-2"><?= e($r['address']) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex align-items-center gap-2">
                                        <svg width="15" height="15" fill="none" stroke="#64748b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <code><?= e($r['timezone']) ?></code>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 pt-2 border-top">
                                    <a href="<?= url('admin/restaurants/edit?id=' . $r['id']) ?>" class="admin-btn-secondary flex-grow-1 text-center justify-content-center" style="padding: 0.45rem 0.75rem; font-size: 0.82rem;">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
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

    <!-- Recent Global Users Table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <h5 class="admin-card-title">Recent Global Users</h5>
                <small class="text-muted">Master Super Admins, Restaurant Managers, and Cashiers</small>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= url('admin/users') ?>" class="admin-btn-secondary" style="padding: 0.4rem 0.85rem; font-size: 0.82rem;">
                    View All Users (<?= count($users) ?>)
                </a>
                <a href="<?= url('admin/users/create') ?>" class="admin-btn-primary" style="padding: 0.4rem 0.85rem; font-size: 0.82rem;">
                    + New User
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="ps-4">User</th>
                        <th>Role</th>
                        <th>Assigned Branch</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No users found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach (array_slice($users, 0, 8) as $u): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="nav-user-avatar" style="width: 32px; height: 32px; font-size: 0.75rem; background: <?= $u['role'] === 'superadmin' ? 'linear-gradient(135deg, #8b5cf6, #6d28d9)' : ($u['role'] === 'manager' ? 'linear-gradient(135deg, #3b82f6, #1d4ed8)' : 'linear-gradient(135deg, #10b981, #047857)') ?>;">
                                            <?= strtoupper(substr($u['username'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.92rem;"><?= e($u['username']) ?></div>
                                            <small class="text-muted">ID #<?= (int)$u['id'] ?></small>
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
                                        <span class="badge bg-light text-dark border px-2 py-1 fw-semibold" style="font-size: 0.8rem;">
                                            🏬 <?= e($u['restaurant_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="font-size: 0.78rem;">
                                            🌐 Global / All Outlets
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
                                    <small class="text-muted"><?= format_date($u['created_at']) ?></small>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= url('admin/users/edit?id=' . $u['id']) ?>" class="admin-btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
