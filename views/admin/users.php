<?php
/**
 * Modern Super Admin Global Users List View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);

$superCount = 0;
$managerCount = 0;
$cashierCount = 0;

foreach ($users as $u) {
    if ($u['role'] === 'superadmin') $superCount++;
    elseif ($u['role'] === 'manager') $managerCount++;
    elseif ($u['role'] === 'cashier') $cashierCount++;
}
?>

<div class="admin-container">
    <!-- Header Section -->
    <div class="admin-header-box">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h2 class="admin-page-title">Global Users Directory</h2>
                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.85rem;">
                    <?= count($users) ?>
                </span>
            </div>
            <p class="admin-page-subtitle">Provision and manage Super Admins, Restaurant Managers, and POS Cashiers</p>
        </div>
        <div>
            <a href="<?= url('admin/users/create') ?>" class="admin-btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Add New User</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="admin-card mb-4">
        <div class="p-3">
            <div class="row g-3 align-items-center">
                <!-- Search -->
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input 
                            type="text" 
                            id="userSearchInput" 
                            class="form-control border-start-0 ps-0" 
                            placeholder="Search by username or restaurant..."
                            onkeyup="filterUsers()"
                        >
                    </div>
                </div>

                <!-- Role Filter Tabs -->
                <div class="col-12 col-md-7 d-flex justify-content-md-end gap-1 flex-wrap">
                    <button type="button" class="btn btn-sm btn-dark rounded-pill px-3 py-1 fw-bold role-filter-btn active" data-role="all" onclick="setRoleFilter('all', this)">
                        All (<?= count($users) ?>)
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 fw-semibold role-filter-btn" data-role="superadmin" onclick="setRoleFilter('superadmin', this)">
                        Super Admin (<?= $superCount ?>)
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 fw-semibold role-filter-btn" data-role="manager" onclick="setRoleFilter('manager', this)">
                        Managers (<?= $managerCount ?>)
                    </button>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1 fw-semibold role-filter-btn" data-role="cashier" onclick="setRoleFilter('cashier', this)">
                        Cashiers (<?= $cashierCount ?>)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="admin-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="ps-4">User Account</th>
                        <th>Role Designation</th>
                        <th>Assigned Restaurant Branch</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <p class="mb-2">No users created yet.</p>
                                <a href="<?= url('admin/users/create') ?>" class="admin-btn-primary">+ Create First User</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <?php
                                $roleGradient = match($u['role']) {
                                    'superadmin' => 'linear-gradient(135deg, #8b5cf6, #6d28d9)',
                                    'manager' => 'linear-gradient(135deg, #3b82f6, #1d4ed8)',
                                    'cashier' => 'linear-gradient(135deg, #10b981, #047857)',
                                    default => 'linear-gradient(135deg, #64748b, #475569)',
                                };
                            ?>
                            <tr class="user-row" data-role="<?= e($u['role']) ?>" data-search="<?= strtolower(e($u['username'] . ' ' . ($u['restaurant_name'] ?? 'global'))) ?>">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="nav-user-avatar" style="width: 36px; height: 36px; font-size: 0.82rem; background: <?= $roleGradient ?>;">
                                            <?= strtoupper(substr($u['username'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6"><?= e($u['username']) ?></div>
                                            <small class="text-muted">User ID: #<?= (int)$u['id'] ?></small>
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
                                        <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-semibold" style="font-size: 0.82rem;">
                                            🏬 <?= e($u['restaurant_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1.5 fw-medium" style="font-size: 0.8rem;">
                                            🌐 Global Access (All Outlets)
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
                                    <a href="<?= url('admin/users/edit?id=' . $u['id']) ?>" class="admin-btn-secondary" style="padding: 0.4rem 0.85rem; font-size: 0.82rem;">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit Account
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

<script>
let currentRoleFilter = 'all';

function setRoleFilter(role, btn) {
    currentRoleFilter = role;
    document.querySelectorAll('.role-filter-btn').forEach(b => {
        b.classList.remove('btn-dark', 'active');
        b.classList.add('btn-light', 'border');
    });
    btn.classList.remove('btn-light', 'border');
    btn.classList.add('btn-dark', 'active');
    filterUsers();
}

function filterUsers() {
    const q = document.getElementById('userSearchInput').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.user-row');

    rows.forEach(row => {
        const text = row.getAttribute('data-search') || '';
        const role = row.getAttribute('data-role') || '';
        const matchesQuery = (q === '' || text.includes(q));
        const matchesRole = (currentRoleFilter === 'all' || role === currentRoleFilter);

        if (matchesQuery && matchesRole) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
