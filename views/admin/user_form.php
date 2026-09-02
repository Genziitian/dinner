<?php
/**
 * Super Admin User Form View Template
 * GI ORDER POS
 */
declare(strict_types=1);
$isEdit = !empty($isEdit);
?>

<div class="admin-container" style="max-width: 680px;">
    <!-- Breadcrumb Header -->
    <div class="d-flex align-items-center gap-2 mb-2.5">
        <a href="<?= url('admin/users') ?>" class="admin-btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Users</span>
        </a>
    </div>

    <div class="admin-header-box mb-3">
        <div>
            <h2 class="admin-page-title"><?= $isEdit ? 'Edit User' : 'New User' ?></h2>
            <p class="admin-page-subtitle">Configure credentials, system role & branch assignment</p>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h5 class="admin-card-title">Account Details</h5>
            <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 0.75rem;">
                <?= $isEdit ? 'User #' . (int)$userObj['id'] : 'New Account' ?>
            </span>
        </div>

        <div class="p-3 p-md-3.5">
            <form method="POST" action="<?= $isEdit ? url('admin/users/edit') : url('admin/users/create') ?>" autocomplete="off">
                <?= csrf_field() ?>
                <?php if ($isEdit): ?>
                    <input type="hidden" name="id" value="<?= (int)$userObj['id'] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">
                        Username <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted ps-2.5 pe-1.5">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        <input 
                            type="text" 
                            name="username" 
                            class="form-control ps-1.5" 
                            style="font-size: 0.88rem;"
                            maxlength="10" 
                            pattern="[a-zA-Z0-9_]{3,10}"
                            placeholder="e.g. manager1, cashier1" 
                            value="<?= e($userObj['username'] ?? '') ?>" 
                            <?= $isEdit ? 'readonly disabled' : 'required' ?>
                        >
                    </div>
                    <div class="text-muted small mt-1" style="font-size: 0.76rem;">3 to 10 characters (letters, numbers, underscores). <?= $isEdit ? 'Username cannot be modified.' : '' ?></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">
                        Password <?= $isEdit ? '<span class="text-muted fw-normal" style="font-size: 0.78rem;">(Leave blank to keep current)</span>' : '<span class="text-danger">*</span>' ?>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted ps-2.5 pe-1.5">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        <input 
                            type="password" 
                            name="password" 
                            id="adminUserPassword"
                            class="form-control ps-1.5" 
                            style="font-size: 0.88rem;"
                            minlength="8" 
                            maxlength="72" 
                            placeholder="<?= $isEdit ? '••••••••' : 'Min 8 characters' ?>" 
                            <?= $isEdit ? '' : 'required' ?>
                        >
                        <button class="btn btn-outline-secondary px-2.5" type="button" onclick="const p = document.getElementById('adminUserPassword'); p.type = p.type === 'password' ? 'text' : 'password';" title="Toggle Password">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="row g-2.5 mb-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">
                            Role <span class="text-danger">*</span>
                        </label>
                        <select name="role" id="roleSelect" class="form-select" style="font-size: 0.88rem;" onchange="onRoleChanged(this.value)" required>
                            <option value="cashier" <?= ($userObj['role'] ?? '') === 'cashier' ? 'selected' : '' ?>>Cashier (POS Billing)</option>
                            <option value="manager" <?= ($userObj['role'] ?? '') === 'manager' ? 'selected' : '' ?>>Manager (Restaurant Admin)</option>
                            <option value="superadmin" <?= ($userObj['role'] ?? '') === 'superadmin' ? 'selected' : '' ?>>Super Admin (Global System)</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">Status</label>
                        <select name="status" class="form-select" style="font-size: 0.88rem;">
                            <option value="active" <?= ($userObj['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($userObj['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive (Suspended)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3.5" id="restaurantSelectWrap" style="<?= ($userObj['role'] ?? '') === 'superadmin' ? 'display: none;' : '' ?>">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">
                        Assigned Restaurant Branch <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted ps-2.5 pe-1.5">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                        <select name="restaurant_id" class="form-select ps-1.5" style="font-size: 0.88rem;">
                            <option value="">-- Select Restaurant --</option>
                            <?php foreach ($restaurants as $r): ?>
                                <option value="<?= (int)$r['id'] ?>" <?= ($userObj['restaurant_id'] ?? 0) == $r['id'] ? 'selected' : '' ?>>
                                    <?= e($r['name']) ?> (ID #<?= (int)$r['id'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 pt-3 border-top">
                    <button type="submit" class="admin-btn-primary py-2 px-3.5 flex-grow-1 justify-content-center">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span><?= $isEdit ? 'Save Changes' : 'Create User' ?></span>
                    </button>
                    <a href="<?= url('admin/users') ?>" class="admin-btn-secondary py-2 px-3">
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
