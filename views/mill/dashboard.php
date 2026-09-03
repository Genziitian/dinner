<?php
/**
 * Mill Dashboard View
 * Atta & Oil Mill Management System
 * Strictly NO emojis used
 */
declare(strict_types=1);
?>

<div class="admin-container" style="max-width: 1100px;">
    <!-- Top Action Banner -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h2 class="admin-page-title mb-0.5"><?= __t('dashboard_title') ?></h2>
            <p class="admin-page-subtitle mb-0">
                <?= !empty($user['restaurant_name']) ? e($user['restaurant_name']) : 'Atta & Oil Mill' ?> &bull; <?= format_date($today) ?>
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="<?= url('mill/orders?status=unpaid') ?>" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1.5 px-3 py-1.5 fw-semibold" style="border-radius: 8px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><?= __t('view_unpaid') ?></span>
                <?php if ((float)$summary['unpaid_amount'] > 0): ?>
                    <span class="badge bg-danger text-white ms-1"><?= format_currency($summary['unpaid_amount']) ?></span>
                <?php endif; ?>
            </a>

            <a href="<?= url('mill/orders/new') ?>" class="admin-btn-primary py-1.5 px-3" style="font-size: 0.85rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span><?= __t('quick_new_order') ?></span>
            </a>
        </div>
    </div>

    <!-- Daily Metrics Grid -->
    <div class="row g-2 g-md-3 mb-3.5">
        <!-- Today's Total Orders -->
        <div class="col-6 col-md-3">
            <div class="admin-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="text-muted small fw-medium" style="font-size: 0.8rem;"><?= __t('todays_orders') ?></span>
                    <div class="text-primary bg-light p-1.5 rounded" style="line-height: 0;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                </div>
                <div class="h3 fw-bold text-dark mb-0"><?= (int)$summary['total_orders'] ?></div>
                <small class="text-muted" style="font-size: 0.72rem;"><?= (float)$summary['total_weight_kg'] ?> KG <?= __t('weight') ?></small>
            </div>
        </div>

        <!-- In Process Orders -->
        <div class="col-6 col-md-3">
            <div class="admin-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="text-muted small fw-medium" style="font-size: 0.8rem;"><?= __t('pending_orders') ?></span>
                    <div class="text-warning bg-warning-subtle p-1.5 rounded" style="line-height: 0;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="h3 fw-bold text-warning mb-0"><?= (int)$summary['pending_orders'] ?></div>
                <small class="text-muted" style="font-size: 0.72rem;"><?= __t('status_processing') ?></small>
            </div>
        </div>

        <!-- Ready for Pickup -->
        <div class="col-6 col-md-3">
            <div class="admin-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="text-muted small fw-medium" style="font-size: 0.8rem;"><?= __t('ready_orders') ?></span>
                    <div class="text-success bg-success-subtle p-1.5 rounded" style="line-height: 0;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
                <div class="h3 fw-bold text-success mb-0"><?= (int)$summary['ready_orders'] ?></div>
                <small class="text-muted" style="font-size: 0.72rem;"><?= __t('status_ready') ?></small>
            </div>
        </div>

        <!-- Delivered Orders -->
        <div class="col-6 col-md-3">
            <div class="admin-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="text-muted small fw-medium" style="font-size: 0.8rem;"><?= __t('delivered_orders') ?></span>
                    <div class="text-secondary bg-light p-1.5 rounded" style="line-height: 0;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="h3 fw-bold text-secondary mb-0"><?= (int)$summary['delivered_orders'] ?></div>
                <small class="text-muted" style="font-size: 0.72rem;"><?= __t('status_delivered') ?></small>
            </div>
        </div>
    </div>

    <!-- Revenue & Outstanding Dues Strip -->
    <div class="admin-card p-3 p-md-3.5 mb-3.5">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-md-4 border-end-md">
                <span class="text-muted small d-block mb-1"><?= __t('total_sales') ?></span>
                <span class="h2 fw-bold text-dark mb-0"><?= format_currency($summary['total_amount']) ?></span>
            </div>
            <div class="col-6 col-md-4 border-end-md">
                <span class="text-muted small d-block mb-1"><?= __t('paid_amount') ?></span>
                <span class="h4 fw-bold text-success mb-0"><?= format_currency($summary['paid_amount']) ?></span>
            </div>
            <div class="col-6 col-md-4">
                <span class="text-muted small d-block mb-1"><?= __t('unpaid_amount') ?></span>
                <span class="h4 fw-bold text-danger mb-0"><?= format_currency($summary['unpaid_amount']) ?></span>
            </div>
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="admin-card">
        <div class="admin-card-header d-flex justify-content-between align-items-center">
            <h5 class="admin-card-title mb-0"><?= __t('recent_orders') ?></h5>
            <a href="<?= url('mill/orders') ?>" class="admin-btn-secondary py-1 px-2.5" style="font-size: 0.78rem;">
                <span><?= __t('status_all') ?></span>
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="table-responsive">
            <?php if (empty($recentOrders)): ?>
                <div class="p-4 text-center text-muted">
                    <p class="mb-2"><?= __t('no_orders') ?></p>
                    <a href="<?= url('mill/orders/new') ?>" class="btn btn-sm btn-primary"><?= __t('quick_new_order') ?></a>
                </div>
            <?php else: ?>
                <table class="table table-hover align-middle mb-0" style="font-size: 0.86rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3"><?= __t('order_no') ?></th>
                            <th><?= __t('customer_name') ?></th>
                            <th><?= __t('service') ?></th>
                            <th><?= __t('weight_kg') ?></th>
                            <th><?= __t('total_price') ?></th>
                            <th><?= __t('payment') ?></th>
                            <th><?= __t('status') ?></th>
                            <th class="text-end pe-3"><?= __t('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $o): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-dark">#<?= (int)$o['order_number'] ?></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= e($o['customer_name']) ?></div>
                                    <small class="text-muted"><?= e($o['customer_phone']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= e($o['service_name']) ?></span>
                                </td>
                                <td><?= (float)$o['weight_kg'] ?> KG</td>
                                <td class="fw-bold text-dark"><?= format_currency($o['total_amount']) ?></td>
                                <td>
                                    <span class="badge <?= $o['payment_status'] === 'paid' ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger' ?>">
                                        <?= $o['payment_status'] === 'paid' ? __t('paid') : __t('unpaid') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $statusClass = match ($o['status']) {
                                        'received'   => 'bg-warning-subtle text-warning border-warning',
                                        'processing' => 'bg-info-subtle text-info border-info',
                                        'ready'      => 'bg-success-subtle text-success border-success',
                                        'delivered'  => 'bg-light text-secondary border',
                                        'cancelled'  => 'bg-danger-subtle text-danger border-danger',
                                        default      => 'bg-light text-dark',
                                    };
                                    ?>
                                    <span class="badge border <?= $statusClass ?>">
                                        <?= __t('status_' . $o['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="<?= url('mill/orders?search=' . urlencode($o['customer_phone'])) ?>" class="btn btn-sm btn-light border px-2 py-0.5" title="View details">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
