<?php
/**
 * Mill Customers Directory View
 * Atta & Oil Mill Management System
 * Strictly NO emojis used
 */
declare(strict_types=1);
?>

<div class="admin-container" style="max-width: 1100px;">
    <!-- Page Header & Action -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h2 class="admin-page-title mb-0.5"><?= __t('customer_title') ?></h2>
            <p class="admin-page-subtitle mb-0"><?= count($customers) ?> <?= __t('nav_customers') ?></p>
        </div>

        <a href="<?= url('mill/orders/new') ?>" class="admin-btn-primary py-1.5 px-3 d-none d-md-inline-flex" style="font-size: 0.85rem;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span><?= __t('nav_new_order') ?></span>
        </a>
    </div>

    <!-- Search Box -->
    <div class="admin-card p-3 mb-3">
        <form method="GET" action="<?= url('mill/customers') ?>" class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-white text-muted">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="<?= __t('search_customer') ?>"
                    value="<?= e($search ?? '') ?>"
                    style="font-size: 0.88rem;"
                >
                <?php if (!empty($search)): ?>
                    <a href="<?= url('mill/customers') ?>" class="btn btn-outline-secondary btn-sm d-flex align-items-center" title="Clear">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                <?php endif; ?>
            </div>
            <button type="submit" class="admin-btn-secondary px-3 py-1.5" style="font-size: 0.85rem;">
                <?= __t('customer_title') ?>
            </button>
        </form>
    </div>

    <!-- Customers Grid -->
    <?php if (empty($customers)): ?>
        <div class="admin-card p-4 text-center text-muted">
            <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="mb-2 text-secondary"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <h6 class="fw-bold text-dark mb-1"><?= __t('no_customers') ?></h6>
            <p class="small text-muted mb-3"><?= __t('today_summary') ?></p>
        </div>
    <?php else: ?>
        <div class="row g-2 g-md-3">
            <?php foreach ($customers as $c): ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="admin-card p-3 h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2 pb-2 border-bottom">
                            <div>
                                <h6 class="fw-bold text-dark mb-0.5" style="font-size: 0.95rem;"><?= e($c['name']) ?></h6>
                                <span class="text-muted small"><?= e($c['phone']) ?></span>
                            </div>
                            <?php if ((float)$c['unpaid_amount'] > 0): ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-0.5" style="font-size: 0.74rem;">
                                    <?= __t('unpaid') ?>: <?= format_currency($c['unpaid_amount']) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success-subtle text-success border border-success px-2 py-0.5" style="font-size: 0.74rem;">
                                    <?= __t('paid') ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="row g-2 my-1 flex-grow-1" style="font-size: 0.84rem;">
                            <div class="col-6">
                                <span class="text-muted small d-block"><?= __t('total_visits') ?></span>
                                <span class="fw-bold text-dark"><?= (int)$c['total_orders'] ?></span>
                            </div>
                            <div class="col-6 text-end">
                                <span class="text-muted small d-block"><?= __t('total_spent') ?></span>
                                <span class="fw-bold text-dark"><?= format_currency($c['lifetime_amount']) ?></span>
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-2 border-top mt-2">
                            <a href="<?= url('mill/customers/view?phone=' . urlencode($c['phone'])) ?>" class="admin-btn-secondary py-1 px-2.5 flex-grow-1 justify-content-center" style="font-size: 0.8rem;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span><?= __t('history') ?></span>
                            </a>
                            <a href="<?= url('mill/orders/new?phone=' . urlencode($c['phone'])) ?>" class="admin-btn-primary py-1 px-2.5 flex-grow-1 justify-content-center" style="font-size: 0.8rem;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span><?= __t('nav_new_order') ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
