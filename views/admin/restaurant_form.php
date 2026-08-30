<?php
/**
 * Super Admin Restaurant Create / Edit View Template (Mobile-Optimized)
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
$isEdit = !empty($isEdit);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold m-0" style="letter-spacing: -0.5px;"><?= $isEdit ? 'Edit Restaurant' : 'Create Restaurant' ?></h4>
        <small class="text-muted">Configure restaurant brand, contact, timezone, and operational status</small>
    </div>
    <a href="<?= url('admin/restaurants') ?>" class="btn btn-outline-secondary btn-sm">Cancel</a>
</div>

<div class="row justify-content-center mb-5">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm border-0" style="border-radius: 1rem; border: 1px solid #e2e8f0 !important;">
            <div class="card-body p-3 p-md-4">
                <form method="POST" action="<?= $isEdit ? url('admin/restaurants/edit') : url('admin/restaurants/create') ?>">
                    <?= csrf_field() ?>
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= (int)$restaurant['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Restaurant Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control py-2" value="<?= e($restaurant['name'] ?? '') ?>" placeholder="e.g. Spice Garden Bistro" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Phone Number</label>
                        <input type="tel" name="phone" class="form-control py-2" value="<?= e($restaurant['phone'] ?? '') ?>" placeholder="e.g. +91 98765 43210">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="e.g. 102 Connaught Place, New Delhi"><?= e($restaurant['address'] ?? '') ?></textarea>
                        <div class="text-muted small mt-1">This address appears on customer thermal receipts.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Timezone <span class="text-danger">*</span></label>
                        <select name="timezone" class="form-select py-2" required>
                            <option value="Asia/Kolkata" <?= ($restaurant['timezone'] ?? 'Asia/Kolkata') === 'Asia/Kolkata' ? 'selected' : '' ?>>Asia/Kolkata (IST - India)</option>
                            <option value="Asia/Dubai" <?= ($restaurant['timezone'] ?? '') === 'Asia/Dubai' ? 'selected' : '' ?>>Asia/Dubai (GST - UAE)</option>
                            <option value="Asia/Singapore" <?= ($restaurant['timezone'] ?? '') === 'Asia/Singapore' ? 'selected' : '' ?>>Asia/Singapore (SGT)</option>
                            <option value="Europe/London" <?= ($restaurant['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' ?>>Europe/London (GMT/BST)</option>
                            <option value="America/New_York" <?= ($restaurant['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>America/New_York (EST/EDT)</option>
                            <option value="UTC" <?= ($restaurant['timezone'] ?? '') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Status</label>
                        <select name="status" class="form-select py-2">
                            <option value="active" <?= ($restaurant['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($restaurant['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive (Deactivated)</option>
                        </select>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 pt-2">
                        <button type="submit" class="btn btn-primary py-2.5 px-4 fw-bold flex-grow-1">
                            <?= $isEdit ? 'Save Changes' : 'Create Restaurant' ?>
                        </button>
                        <a href="<?= url('admin/restaurants') ?>" class="btn btn-light border py-2.5 px-4 flex-grow-1 text-center">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
