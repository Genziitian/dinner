<?php
/**
 * Manager Restaurant Settings & Profile View Template (Mobile-Redesigned)
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
$currentUser = current_user();
?>

<!-- Mobile Settings & Profile View (< 768px) -->
<div class="d-md-none mb-5">
    <!-- Profile Card Header -->
    <div class="card shadow-sm border-0 mb-3" style="border-radius: 1rem; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="card-body p-4 text-center text-white">
            <div class="d-flex align-items-center justify-content-center mx-auto mb-3 rounded-circle bg-white text-dark fw-bold fs-3 shadow" style="width: 60px; height: 60px; border: 2px solid rgba(255,255,255,0.2);">
                <?= strtoupper(substr($currentUser['username'] ?? 'M', 0, 1)) ?>
            </div>
            <h5 class="fw-bold m-0"><?= e($currentUser['username']) ?></h5>
            <span class="badge mt-1" style="background: rgba(255, 255, 255, 0.15); color: #fbbf24; border: 1px solid rgba(251, 191, 36, 0.3); font-size: 0.7rem;">MANAGER</span>
            <div class="small text-white-50 mt-2">🏢 <?= e($restaurant['name']) ?></div>
        </div>
    </div>

    <!-- Android-style List Menu Group -->
    <div class="card shadow-sm border-0 mb-3" style="border-radius: 1rem; overflow: hidden; border: 1px solid #e2e8f0 !important;">
        <div class="list-group list-group-flush">
            <!-- Restaurant Info (Editable Section) -->
            <div class="list-group-item p-3">
                <div class="fw-bold text-dark mb-3 d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                    <span>🏢</span> Restaurant Details
                </div>
                <form method="POST" action="<?= url('manager/settings') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Restaurant Name</label>
                        <input type="text" name="name" class="form-control form-control-sm py-2" value="<?= e($restaurant['name'] ?? '') ?>" required style="border-radius: 0.5rem;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Phone Number</label>
                        <input type="tel" name="phone" class="form-control form-control-sm py-2" value="<?= e($restaurant['phone'] ?? '') ?>" placeholder="e.g. +91 98765 43210" style="border-radius: 0.5rem;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Receipt Address</label>
                        <textarea name="address" class="form-control form-control-sm" rows="2" placeholder="e.g. 102 Main Street, Central Plaza" style="border-radius: 0.5rem; font-size: 0.85rem;"><?= e($restaurant['address'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">System Timezone</label>
                        <select name="timezone" class="form-select form-select-sm py-2" required style="border-radius: 0.5rem;">
                            <option value="Asia/Kolkata" <?= ($restaurant['timezone'] ?? '') === 'Asia/Kolkata' ? 'selected' : '' ?>>Asia/Kolkata (IST - India)</option>
                            <option value="Asia/Dubai" <?= ($restaurant['timezone'] ?? '') === 'Asia/Dubai' ? 'selected' : '' ?>>Asia/Dubai (GST - UAE)</option>
                            <option value="Asia/Singapore" <?= ($restaurant['timezone'] ?? '') === 'Asia/Singapore' ? 'selected' : '' ?>>Asia/Singapore (SGT)</option>
                            <option value="Europe/London" <?= ($restaurant['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' ?>>Europe/London (GMT/BST)</option>
                            <option value="America/New_York" <?= ($restaurant['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>America/New_York (EST/EDT)</option>
                            <option value="UTC" <?= ($restaurant['timezone'] ?? '') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100 py-2 fw-bold" style="border-radius: 0.5rem;">
                        Save Details
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Management Quick Links -->
    <div class="card shadow-sm border-0 mb-3" style="border-radius: 1rem; overflow: hidden; border: 1px solid #e2e8f0 !important;">
        <div class="list-group list-group-flush">
            <a href="<?= url('manager/users') ?>" class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span style="font-size: 1.2rem;">👥</span>
                    <div>
                        <div class="fw-semibold text-dark" style="font-size: 0.9rem;">Staff Accounts</div>
                        <small class="text-muted" style="font-size: 0.75rem;">Manage cashiers and manager accounts</small>
                    </div>
                </div>
                <span class="text-muted">➔</span>
            </a>
            
            <a href="<?= url('manager/reports') ?>" class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span style="font-size: 1.2rem;">📊</span>
                    <div>
                        <div class="fw-semibold text-dark" style="font-size: 0.9rem;">Financial Reports</div>
                        <small class="text-muted" style="font-size: 0.75rem;">Analyze sales statistics & payment methods</small>
                    </div>
                </div>
                <span class="text-muted">➔</span>
            </a>

            <a href="<?= url('manager/exports') ?>" class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span style="font-size: 1.2rem;">📥</span>
                    <div>
                        <div class="fw-semibold text-dark" style="font-size: 0.9rem;">Export CSV Reports</div>
                        <small class="text-muted" style="font-size: 0.75rem;">Download raw order reports in Excel format</small>
                    </div>
                </div>
                <span class="text-muted">➔</span>
            </a>

            <a href="<?= url('manager/audit') ?>" class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span style="font-size: 1.2rem;">🔍</span>
                    <div>
                        <div class="fw-semibold text-dark" style="font-size: 0.9rem;">System Audit Trail</div>
                        <small class="text-muted" style="font-size: 0.75rem;">Immutable history of all system activities</small>
                    </div>
                </div>
                <span class="text-muted">➔</span>
            </a>
        </div>
    </div>

    <!-- Logout Option -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 1rem; overflow: hidden; border: 1px solid #e2e8f0 !important;">
        <div class="list-group list-group-flush">
            <a href="<?= url('logout') ?>" class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center bg-light text-danger">
                <div class="d-flex align-items-center gap-3">
                    <span style="font-size: 1.2rem;">🚪</span>
                    <span class="fw-bold" style="font-size: 0.9rem;">Logout Account</span>
                </div>
                <span class="fw-bold">➔</span>
            </a>
        </div>
    </div>
</div>

<!-- Desktop Restaurant Settings View (>= 768px) -->
<div class="d-none d-md-block">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold m-0">Restaurant Settings</h4>
            <small class="text-muted">Configure restaurant details, timezone, and receipt header info</small>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius: 1rem; border: 1px solid #e2e8f0 !important;">
                <div class="card-body p-4">
                    <form method="POST" action="<?= url('manager/settings') ?>">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Restaurant Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?= e($restaurant['name'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Contact Phone Number</label>
                            <input type="tel" name="phone" class="form-control" value="<?= e($restaurant['phone'] ?? '') ?>" placeholder="e.g. +91 98765 43210">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Physical Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="e.g. 102 Main Street, Central Plaza"><?= e($restaurant['address'] ?? '') ?></textarea>
                            <div class="text-muted small">This address will appear at the top of customer print receipts.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Timezone <span class="text-danger">*</span></label>
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
</div>
