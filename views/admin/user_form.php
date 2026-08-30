<?php
/**
 * Super Admin User Create / Edit View Template (Mobile-Optimized)
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
$isEdit = !empty($isEdit);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold m-0" style="letter-spacing: -0.5px;"><?= $isEdit ? 'Edit User' : 'Create User' ?></h4>
        <small class="text-muted">Provision staff or superadmin account with restaurant assignment</small>
    </div>
    <a href="<?= url('admin/users') ?>" class="btn btn-outline-secondary btn-sm">Cancel</a>
</div>

<div class="row justify-content-center mb-5">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm border-0" style="border-radius: 1rem; border: 1px solid #e2e8f0 !important;">
            <div class="card-body p-3 p-md-4">
                <form method="POST" action="<?= $isEdit ? url('admin/users/edit') : url('admin/users/create') ?>">
                    <?= csrf_field() ?>
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= (int)$userObj['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Username <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            name="username" 
                            class="form-control py-2" 
                            maxlength="10" 
                            pattern="[a-zA-Z0-9_]{3,10}"
                            placeholder="e.g. manager02" 
                            value="<?= e($userObj['username'] ?? '') ?>" 
                            <?= $isEdit ? 'readonly disabled' : 'required' ?>
                        >
                        <div class="text-muted small mt-1">3 to 10 alphanumeric or underscore characters.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Password <?= $isEdit ? '<span class="text-muted small fw-normal">(Leave empty to keep current)</span>' : '<span class="text-danger">*</span>' ?></label>
                        <input 
                            type="password" 
                            name="password" 
                            class="form-control py-2" 
                            minlength="8" 
                            maxlength="72"
                            placeholder="••••••••" 
                            <?= $isEdit ? '' : 'required' ?>
                        >
                        <div class="text-muted small mt-1">Minimum 8 characters.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Role <span class="text-danger">*</span></label>
                        <select name="role" id="roleSelect" class="form-select py-2" onchange="onRoleChanged(this.value)" required>
                            <option value="cashier" <?= ($userObj['role'] ?? '') === 'cashier' ? 'selected' : '' ?>>Cashier (Point of Sale billing only)</option>
                            <option value="manager" <?= ($userObj['role'] ?? '') === 'manager' ? 'selected' : '' ?>>Manager (Restaurant admin & reports)</option>
                            <option value="superadmin" <?= ($userObj['role'] ?? '') === 'superadmin' ? 'selected' : '' ?>>Super Admin (Global System Owner)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="restaurantSelectWrap" style="<?= ($userObj['role'] ?? '') === 'superadmin' ? 'display: none;' : '' ?>">
                        <label class="form-label fw-semibold text-dark">Assigned Restaurant <span class="text-danger">*</span></label>
                        <select name="restaurant_id" class="form-select py-2">
                            <option value="">-- Select Restaurant --</option>
                            <?php foreach ($restaurants as $r): ?>
                                <option value="<?= (int)$r['id'] ?>" <?= ($userObj['restaurant_id'] ?? 0) == $r['id'] ? 'selected' : '' ?>>
                                    <?= e($r['name']) ?> (ID: #<?= (int)$r['id'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Account Status</label>
                        <select name="status" class="form-select py-2">
                            <option value="active" <?= ($userObj['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($userObj['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 pt-2">
                        <button type="submit" class="btn btn-primary py-2.5 px-4 fw-bold flex-grow-1">
                            <?= $isEdit ? 'Save Changes' : 'Create User' ?>
                        </button>
                        <a href="<?= url('admin/users') ?>" class="btn btn-light border py-2.5 px-4 flex-grow-1 text-center">Cancel</a>
                    </div>
                </form>
            </div>
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
