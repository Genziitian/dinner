<?php
/**
 * Manager Restaurant Settings View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold m-0">Restaurant Settings</h4>
        <small class="text-muted">Configure restaurant details, timezone, and receipt header info</small>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0" style="border-radius: 1rem;">
            <div class="card-body p-4">
                <form method="POST" action="<?= url('manager/settings') ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Restaurant Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= e($restaurant['name'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contact Phone Number</label>
                        <input type="tel" name="phone" class="form-control" value="<?= e($restaurant['phone'] ?? '') ?>" placeholder="e.g. +91 98765 43210">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Physical Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="e.g. 102 Main Street, Central Plaza"><?= e($restaurant['address'] ?? '') ?></textarea>
                        <div class="text-muted small">This address will appear at the top of customer print receipts.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Timezone <span class="text-danger">*</span></label>
                        <select name="timezone" class="form-select" required>
                            <option value="Asia/Kolkata" <?= ($restaurant['timezone'] ?? '') === 'Asia/Kolkata' ? 'selected' : '' ?>>Asia/Kolkata (IST - Indian Standard Time)</option>
                            <option value="Asia/Dubai" <?= ($restaurant['timezone'] ?? '') === 'Asia/Dubai' ? 'selected' : '' ?>>Asia/Dubai (GST)</option>
                            <option value="Asia/Singapore" <?= ($restaurant['timezone'] ?? '') === 'Asia/Singapore' ? 'selected' : '' ?>>Asia/Singapore (SGT)</option>
                            <option value="Europe/London" <?= ($restaurant['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' ?>>Europe/London (GMT/BST)</option>
                            <option value="America/New_York" <?= ($restaurant['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>America/New_York (EST/EDT)</option>
                            <option value="UTC" <?= ($restaurant['timezone'] ?? '') === 'UTC' ? 'selected' : '' ?>>UTC (Coordinated Universal Time)</option>
                        </select>
                        <div class="text-muted small">Controls the daily order number midnight reset and report timeframes.</div>
                    </div>

                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
                        Save Restaurant Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
