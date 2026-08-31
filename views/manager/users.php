<?php
/**
 * Manager Staff Accounts View Template
 * Matches Android Native App UI 1:1 (Dynamic Data)
 */
declare(strict_types=1);
?>

<div class="app-container" style="padding-bottom: calc(var(--bottom-nav-height) + 5rem);">
    <!-- Top App Bar Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= url('manager/settings') ?>" class="top-bar-back-btn" title="Back to Profile">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h4 class="top-bar-title m-0">Staff Management</h4>
        </div>
        <div class="top-bar-actions">
            <a href="<?= url('manager/users') ?>" class="top-bar-icon-btn" title="Refresh">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </a>
        </div>
    </div>

    <!-- Staff List Cards -->
    <?php if (empty($users)): ?>
        <div class="card border-0 p-5 text-center my-3" style="border-radius: 16px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">👥</div>
            <div class="fw-bold text-dark fs-6 mb-1">No staff members found</div>
            <div class="text-secondary small">Tap the '+' button below to add your first staff member.</div>
        </div>
    <?php else: ?>
        <div class="catalog-list-wrap">
            <?php foreach ($users as $u): ?>
                <?php 
                    $isManager = $u['role'] === 'manager';
                    $isActive = $u['status'] === 'active';
                ?>
                <div class="catalog-item-card">
                    <div class="catalog-item-left">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 44px; height: 44px; font-size: 1.3rem; background: <?= $isManager ? 'rgba(234, 88, 12, 0.12)' : 'rgba(16, 185, 129, 0.12)' ?>;">
                            <?= $isManager ? '👔' : '🛒' ?>
                        </div>
                        <div>
                            <div class="catalog-item-name"><?= e($u['username']) ?></div>
                            <div class="catalog-item-meta">
                                <span class="badge" style="background: <?= $isManager ? '#eff6ff; color: #2563eb' : '#ecfdf5; color: #059669' ?>; border-radius: 6px; font-size: 0.72rem; font-weight: 700;">
                                    <?= $isManager ? 'MANAGER' : 'CASHIER' ?>
                                </span>
                                <span class="badge" style="background: <?= $isActive ? '#d1fae5; color: #10b981' : '#fee2e2; color: #ef4444' ?>; border-radius: 6px; font-size: 0.72rem; font-weight: 700;">
                                    <?= strtoupper(e($u['status'])) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <a href="<?= url('manager/users/edit?id=' . (int)$u['id']) ?>" class="btn btn-sm btn-light border fw-bold px-3 py-1.5" style="border-radius: 10px;">
                        Edit
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Floating Action Button (FAB) Bottom Right '+' -->
    <a href="<?= url('manager/users/create') ?>" class="fab-add-btn" title="Add Staff Member">
        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
    </a>
</div>
