<?php
/**
 * Super Admin Users Directory View Template
 * GI ORDER POS
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
                <h2 class="admin-page-title">Users</h2>
                <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill fw-bold" style="font-size: 0.8rem;">
                    <?= count($users) ?>
                </span>
            </div>
            <p class="admin-page-subtitle">Manage system roles & staff accounts</p>
        </div>
        <div>
            <a href="<?= url('admin/users/create') ?>" class="admin-btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Add User</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="admin-card mb-3 mb-md-4">
        <div class="p-2.5 p-md-3">
            <div class="row g-2 align-items-center">
                <!-- Search Input -->
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5 pe-1.5">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input 
                            type="text" 
                            id="userSearchInput" 
                            class="form-control border-start-0 ps-1" 
                            style="font-size: 0.88rem;"
                            placeholder="Search by username or restaurant..."
                            onkeyup="filterUsers()"
                        >
                    </div>
                </div>

                <!-- Role Filter Tabs / Chips -->
                <div class="col-12 col-md-7 d-flex justify-content-md-end align-items-center">
                    <div class="admin-filters-scroll">
                        <button type="button" class="admin-filter-chip active" data-role="all" onclick="setRoleFilter('all', this)">
                            All (<?= count($users) ?>)
                        </button>
                        <button type="button" class="admin-filter-chip" data-role="superadmin" onclick="setRoleFilter('superadmin', this)">
                            Super Admin (<?= $superCount ?>)
                        </button>
                        <button type="button" class="admin-filter-chip" data-role="manager" onclick="setRoleFilter('manager', this)">
                            Managers (<?= $managerCount ?>)
                        </button>
                        <button type="button" class="admin-filter-chip" data-role="cashier" onclick="setRoleFilter('cashier', this)">
                            Cashiers (<?= $cashierCount ?>)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users List Container -->
    <div class="admin-card">
        <?php if (empty($users)): ?>
            <div class="p-4 text-center text-muted">
                <p class="mb-2">No users created yet.</p>
                <a href="<?= url('admin/users/create') ?>" class="admin-btn-primary">+ Create User</a>
            </div>
        <?php else: ?>
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
                        <?php foreach ($users as $u): ?>
                            <?php
                                $roleGradient = match($u['role']) {
                                    'superadmin' => 'linear-gradient(135deg, #8b5cf6, #6d28d9)',
                                    'manager' => 'linear-gradient(135deg, #3b82f6, #1d4ed8)',
                                    'cashier' => 'linear-gradient(135deg, #10b981, #047857)',
                                    default => 'linear-gradient(135deg, #64748b, #475569)',
                                };
                            ?>
                            <tr class="user-row-desktop" data-role="<?= e($u['role']) ?>" data-search="<?= strtolower(e($u['username'] . ' ' . ($u['restaurant_name'] ?? 'global'))) ?>">
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="nav-user-avatar" style="width: 32px; height: 32px; font-size: 0.78rem; background: <?= $roleGradient ?>;">
                                            <?= strtoupper(substr($u['username'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?= e($u['username']) ?></div>
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
                                        <span class="d-inline-flex align-items-center gap-1 badge bg-light text-dark border px-2 py-1 fw-semibold" style="font-size: 0.8rem;">
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
                                    <small class="text-muted" style="font-size: 0.8rem;"><?= format_date($u['created_at']) ?></small>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="<?= url('admin/users/edit?id=' . $u['id']) ?>" class="admin-btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.78rem;">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards View (< 768px) -->
            <div class="d-md-none p-2.5">
                <?php foreach ($users as $u): ?>
                    <?php
                        $roleGradient = match($u['role']) {
                            'superadmin' => 'linear-gradient(135deg, #8b5cf6, #6d28d9)',
                            'manager' => 'linear-gradient(135deg, #3b82f6, #1d4ed8)',
                            'cashier' => 'linear-gradient(135deg, #10b981, #047857)',
                            default => 'linear-gradient(135deg, #64748b, #475569)',
                        };
                    ?>
                    <div class="admin-user-card-mobile user-row-mobile" data-role="<?= e($u['role']) ?>" data-search="<?= strtolower(e($u['username'] . ' ' . ($u['restaurant_name'] ?? 'global'))) ?>">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="nav-user-avatar" style="width: 34px; height: 34px; font-size: 0.78rem; background: <?= $roleGradient ?>;">
                                    <?= strtoupper(substr($u['username'], 0, 2)) ?>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.92rem;"><?= e($u['username']) ?></div>
                                    <small class="text-muted" style="font-size: 0.74rem;">ID #<?= (int)$u['id'] ?></small>
                                </div>
                            </div>
                            <span class="admin-pill-status <?= $u['status'] === 'active' ? 'active' : 'inactive' ?>">
                                <span class="dot"></span>
                                <?= ucfirst(e($u['status'])) ?>
                            </span>
                        </div>

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1.5 pt-2 border-top">
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <span class="badge-role badge-role-<?= e($u['role']) ?>" style="font-size: 0.72rem; padding: 2px 7px;">
                                    <?= strtoupper(e($u['role'])) ?>
                                </span>
                                <?php if (!empty($u['restaurant_name'])): ?>
                                    <span class="d-inline-flex align-items-center gap-1 badge bg-light text-dark border px-1.5 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        <?= e($u['restaurant_name']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary px-1.5 py-0.5" style="font-size: 0.72rem;">
                                        Global
                                    </span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= url('admin/users/edit?id=' . $u['id']) ?>" class="admin-btn-secondary" style="padding: 0.25rem 0.7rem; font-size: 0.76rem;">
                                Edit
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="noUsersMatch" class="p-4 text-center text-muted small" style="display: none;">
                No users match your filter.
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
let currentRoleFilter = 'all';

function setRoleFilter(role, btn) {
    currentRoleFilter = role;
    document.querySelectorAll('.admin-filter-chip').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filterUsers();
}

function filterUsers() {
    const q = (document.getElementById('userSearchInput')?.value || '').toLowerCase().trim();
    const desktopRows = document.querySelectorAll('.user-row-desktop');
    const mobileCards = document.querySelectorAll('.user-row-mobile');
    let count = 0;

    const filterItem = (el) => {
        const text = el.getAttribute('data-search') || '';
        const role = el.getAttribute('data-role') || '';
        const matchesQuery = (q === '' || text.includes(q));
        const matchesRole = (currentRoleFilter === 'all' || role === currentRoleFilter);

        if (matchesQuery && matchesRole) {
            el.style.display = '';
            return 1;
        } else {
            el.style.display = 'none';
            return 0;
        }
    };

    desktopRows.forEach(row => {
        if (filterItem(row)) count++;
    });

    mobileCards.forEach(card => {
        filterItem(card);
    });

    const noMatch = document.getElementById('noUsersMatch');
    if (noMatch) {
        noMatch.style.display = (count === 0 && (desktopRows.length > 0 || mobileCards.length > 0)) ? '' : 'none';
    }
}
</script>
