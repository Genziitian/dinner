<?php
/**
 * Modern Super Admin Restaurant Form View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
$isEdit = !empty($isEdit);
?>

<div class="admin-container" style="max-width: 760px;">
    <!-- Breadcrumb Header -->
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="<?= url('admin/restaurants') ?>" class="admin-btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.82rem;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Restaurants</span>
        </a>
    </div>

    <div class="admin-header-box mb-4">
        <div>
            <h2 class="admin-page-title"><?= $isEdit ? 'Edit Restaurant Branch' : 'Create New Restaurant' ?></h2>
            <p class="admin-page-subtitle">Configure restaurant branding, outlet contact details, address, and timezone</p>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h5 class="admin-card-title">Outlet Profile & Settings</h5>
            <span class="badge bg-light text-muted border px-2.5 py-1">Branch ID: <?= $isEdit ? '#' . (int)$restaurant['id'] : 'New' ?></span>
        </div>

        <div class="p-4">
            <form method="POST" action="<?= $isEdit ? url('admin/restaurants/edit') : url('admin/restaurants/create') ?>" autocomplete="off">
                <?= csrf_field() ?>
                <?php if ($isEdit): ?>
                    <input type="hidden" name="id" value="<?= (int)$restaurant['id'] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">
                        Restaurant / Branch Name <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                        <input 
                            type="text" 
                            name="name" 
                            class="form-control" 
                            value="<?= e($restaurant['name'] ?? '') ?>" 
                            placeholder="e.g. Spice Garden Bistro" 
                            required
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">Contact Phone</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </span>
                        <input 
                            type="tel" 
                            name="phone" 
                            class="form-control" 
                            value="<?= e($restaurant['phone'] ?? '') ?>" 
                            placeholder="e.g. +91 98765 43210"
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">Physical Address</label>
                    <textarea 
                        name="address" 
                        class="form-control" 
                        rows="2" 
                        placeholder="e.g. 102 Flavor Street, Connaught Place, New Delhi"
                    ><?= e($restaurant['address'] ?? '') ?></textarea>
                    <div class="text-muted small mt-1">This address appears on customer thermal printouts and online receipts.</div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">
                            Timezone <span class="text-danger">*</span>
                        </label>
                        <select name="timezone" class="form-select" required>
                            <option value="Asia/Kolkata" <?= ($restaurant['timezone'] ?? 'Asia/Kolkata') === 'Asia/Kolkata' ? 'selected' : '' ?>>Asia/Kolkata (IST - India)</option>
                            <option value="Asia/Dubai" <?= ($restaurant['timezone'] ?? '') === 'Asia/Dubai' ? 'selected' : '' ?>>Asia/Dubai (GST - UAE)</option>
                            <option value="Asia/Singapore" <?= ($restaurant['timezone'] ?? '') === 'Asia/Singapore' ? 'selected' : '' ?>>Asia/Singapore (SGT)</option>
                            <option value="Europe/London" <?= ($restaurant['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' ?>>Europe/London (GMT/BST)</option>
                            <option value="America/New_York" <?= ($restaurant['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>America/New_York (EST/EDT)</option>
                            <option value="UTC" <?= ($restaurant['timezone'] ?? '') === 'UTC' ? 'selected' : '' ?>>UTC (Coordinated Universal Time)</option>
                        </select>
                        <div class="text-muted small mt-1">Daily order counter resets midnight in this timezone.</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.9rem;">Operational Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= ($restaurant['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>🟢 Active (Operational)</option>
                            <option value="inactive" <?= ($restaurant['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>🔴 Inactive (Deactivated)</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 pt-3 border-top">
                    <button type="submit" class="admin-btn-primary py-2.5 px-4 flex-grow-1 justify-content-center">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span><?= $isEdit ? 'Save Changes' : 'Create Restaurant Branch' ?></span>
                    </button>
                    <a href="<?= url('admin/restaurants') ?>" class="admin-btn-secondary py-2.5 px-4">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
