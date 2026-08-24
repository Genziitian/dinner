<?php
/**
 * Manager Create / Edit Staff Form View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
$isEdit = !empty($isEdit);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold m-0"><?= $isEdit ? 'Edit Staff Account' : 'Add New Staff Account' ?></h4>
        <small class="text-muted">Create or configure cashier/manager login credentials</small>
    </div>
    <a href="<?= url('manager/users') ?>" class="btn btn-outline-secondary btn-sm">Cancel</a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-6">
        <div class="card shadow-sm border-0" style="border-radius: 1rem;">
            <div class="card-body p-4">
                <form method="POST" action="<?= $isEdit ? url('manager/users/edit') : url('manager/users/create') ?>">
                    <?= csrf_field() ?>
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= (int)$userObj['id'] ?>">
                    <?php endif; ?>

                    <!-- Username -->
                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            name="username" 
                            class="form-control" 
                            maxlength="10" 
                            pattern="[a-zA-Z0-9_]{3,10}"
                            placeholder="e.g. cashier02" 
                            value="<?= e($userObj['username'] ?? '') ?>" 
                            <?= $isEdit ? 'readonly disabled' : 'required' ?>
                        >
                        <div class="text-muted small">3 to 10 alphanumeric or underscore characters only.</div>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label">Password <?= $isEdit ? '<span class="text-muted small">(Leave blank to keep unchanged)</span>' : '<span class="text-danger">*</span>' ?></label>
                        <input 
                            type="password" 
                            name="password" 
                            class="form-control" 
                            minlength="8" 
                            maxlength="72"
                            placeholder="••••••••" 
                            <?= $isEdit ? '' : 'required' ?>
                        >
                        <div class="text-muted small">Must be 8 to 72 characters.</div>
                    </div>

                    <!-- Role Selection -->
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="cashier" <?= ($userObj['role'] ?? '') === 'cashier' ? 'selected' : '' ?>>Cashier (Billing & Today's Summary only)</option>
                            <option value="manager" <?= ($userObj['role'] ?? '') === 'manager' ? 'selected' : '' ?>>Manager (Full restaurant access & reporting)</option>
                        </select>
                    </div>

                    <!-- Status Selection -->
                    <div class="mb-4">
                        <label class="form-label">Account Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= ($userObj['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($userObj['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive (Locked out)</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
                            <?= $isEdit ? 'Save Changes' : 'Create Staff User' ?>
                        </button>
                        <a href="<?= url('manager/users') ?>" class="btn btn-light px-4 py-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
