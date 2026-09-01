<?php
/**
 * Modern Super Admin User Form View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
$isEdit = !empty($isEdit);
?>

<div class="admin-container" style="max-width: 760px;">
    <!-- Breadcrumb Header -->
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="<?= url('admin/users') ?>" class="admin-btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.82rem;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Users</span>
        </a>
    </div>

    <div class="admin-header-box mb-4">
        <div>
            <h2 class="admin-page-title"><?= $isEdit ? 'Edit User Account' : 'Provision New User' ?></h2>
            <p class="admin-page-subtitle">Assign system roles, set access passwords, and map users to specific restaurant branches</p>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h5 class="admin-card-title">User Account Credentials & Access</h5>
            <span class="badge bg-light text-muted border px-2.5 py-1">User ID: <?= $isEdit ? '#' . (int)$userObj['id'] : 'New' ?></span>
        </div>

        <div class="p-4">
            <form method="POST" action="<?= $isEdit ? url('admin/users/edit') : url('admin/users/create') ?>" autocomplete="off">
                <?= csrf_field() ?>
                <?php if ($isEdit): ?>
                    <input type="hidden" name="id" value="<?= (int)$userObj['id'] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">
                        Username <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        <input 
                            type="text" 
                            name="username" 
                            class="form-control" 
                            maxlength="10" 
                            pattern="[a-zA-Z0-9_]{3,10}"
                            placeholder="e.g. manager02, cashier1" 
                            value="<?= e($userObj['username'] ?? '') ?>" 
                            <?= $isEdit ? 'readonly disabled' : 'required' ?>
                        >
                    </div>
                    <div class="text-muted small mt-1">3 to 10 alphanumeric or underscore characters. <?= $isEdit ? 'Username cannot be modified after creation.' : '' ?></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">
                        Password <?= $isEdit ? '<span class="text-muted small fw-normal">(Leave empty to keep unchanged)</span>' : '<span class="text-danger">*</span>' ?>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        <input 
                            type="password" 
                            name="password" 
                            id="adminUserPassword"
                            class="form-control" 
                            minlength="8" 
                            maxlength="72" 
                            placeholder="<?= $isEdit ? '••••••••' : 'Enter password (min 8 chars)' ?>" 
                            <?= $isEdit ? '' : 'required' ?>
                        >
                        <button class="btn btn-outline-secondary" type="button" onclick="const p = document.getElementById('adminUserPassword'); p.type = p.type === 'password' ? 'text' : 'password';" title="Toggle Password Visibility">
                            👁️
                        </button>
                    </div>
                    <div class="text-muted small mt-1">Minimum 8 characters. Uses secure Bcrypt hashing.</div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">
                            Role Designation <span class="text-danger">*</span>
                        </label>
                        <select name="role" id="roleSelect" class="form-select" onchange="onRoleChanged(this.value)" required>
                            <option value="cashier" <?= ($userObj['role'] ?? '') === 'cashier' ? 'selected' : '' ?>>🛒 Cashier (Point of Sale Billing Only)</option>
                            <option value="manager" <?= ($userObj['role'] ?? '') === 'manager' ? 'selected' : '' ?>>👔 Manager (Full Restaurant Admin & Reports)</option>
                            <option value="superadmin" <?= ($userObj['role'] ?? '') === 'superadmin' ? 'selected' : '' ?>>👑 Super Admin (Global System Owner)</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">Account Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= ($userObj['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>🟢 Active</option>
                            <option value="inactive" <?= ($userObj['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>🔴 Inactive (Suspended)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4" id="restaurantSelectWrap" style="<?= ($userObj['role'] ?? '') === 'superadmin' ? 'display: none;' : '' ?>">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">
                        Assigned Restaurant Branch <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                        <select name="restaurant_id" class="form-select">
                            <option value="">-- Select Restaurant Branch --</option>
                            <?php foreach ($restaurants as $r): ?>
                                <option value="<?= (int)$r['id'] ?>" <?= ($userObj['restaurant_id'] ?? 0) == $r['id'] ? 'selected' : '' ?>>
                                    <?= e($r['name']) ?> (ID: #<?= (int)$r['id'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="text-muted small mt-1">Staff will only have access to data belonging to their assigned restaurant.</div>
                </div>

                <div class="d-flex gap-2 pt-3 border-top">
                    <button type="submit" class="admin-btn-primary py-2.5 px-4 flex-grow-1 justify-content-center">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span><?= $isEdit ? 'Save User Changes' : 'Create User Account' ?></span>
                    </button>
                    <a href="<?= url('admin/users') ?>" class="admin-btn-secondary py-2.5 px-4">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function onRoleChanged(role) {
    const wrap = document.getElementById('restaurantSelectWrap');
    if (role === 'superadmin') {
        wrap.style.display = 'none';
        wrap.querySelector('select').removeAttribute('required');
    } else {
        wrap.style.display = 'block';
        wrap.querySelector('select').setAttribute('required', 'required');
    }
}
</script>
