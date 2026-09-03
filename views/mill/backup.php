<?php
/**
 * Mill Data Backup & Restore View
 * Atta & Oil Mill Management System
 * Strictly NO emojis used
 */
declare(strict_types=1);
?>

<div class="admin-container" style="max-width: 800px;">
    <!-- Page Header -->
    <div class="d-flex align-items-center gap-2 mb-2.5">
        <a href="<?= url('mill/dashboard') ?>" class="admin-btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span><?= __t('nav_dashboard') ?></span>
        </a>
    </div>

    <div class="admin-header-box mb-3">
        <div>
            <h2 class="admin-page-title mb-0.5"><?= __t('backup_title') ?></h2>
            <p class="admin-page-subtitle mb-0"><?= e($user['restaurant_name'] ?: 'Atta Mill') ?></p>
        </div>
    </div>

    <div class="row g-3">
        <!-- 1. Export Backup Card -->
        <div class="col-12 col-md-6">
            <div class="admin-card h-100 p-3.5 d-flex flex-column">
                <div class="mb-2.5">
                    <div class="text-primary bg-light p-2 rounded-circle d-inline-flex mb-2">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 1rem;"><?= __t('export_title') ?></h5>
                    <p class="text-muted small mb-0" style="font-size: 0.82rem; line-height: 1.4;">
                        <?= __t('export_desc') ?>
                    </p>
                </div>

                <div class="mt-auto pt-3 border-top">
                    <a href="<?= url('mill/backup/export') ?>" class="admin-btn-primary w-100 justify-content-center py-2" style="font-size: 0.88rem;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span><?= __t('download_backup') ?></span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Restore Backup Card -->
        <div class="col-12 col-md-6">
            <div class="admin-card h-100 p-3.5 d-flex flex-column">
                <div class="mb-2.5">
                    <div class="text-warning bg-warning-subtle p-2 rounded-circle d-inline-flex mb-2">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/></svg>
                    </div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 1rem;"><?= __t('restore_title') ?></h5>
                    <p class="text-muted small mb-0" style="font-size: 0.82rem; line-height: 1.4;">
                        <?= __t('restore_desc') ?>
                    </p>
                </div>

                <form method="POST" action="<?= url('mill/backup/restore') ?>" enctype="multipart/form-data" class="mt-auto pt-3 border-top">
                    <?= csrf_field() ?>
                    <div class="mb-2.5">
                        <label class="form-label small fw-semibold text-secondary mb-1"><?= __t('choose_file') ?></label>
                        <input type="file" name="backup_file" class="form-control form-control-sm" accept=".json,application/json" required>
                    </div>
                    <button type="submit" class="admin-btn-secondary w-100 justify-content-center py-2 text-dark fw-semibold" style="font-size: 0.88rem;" onclick="return confirm('Restore data from this file?')">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/></svg>
                        <span><?= __t('upload_restore_btn') ?></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
