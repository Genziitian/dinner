<?php
/**
 * Customer Profile & Order History View
 * Atta & Oil Mill Management System
 * Strictly NO emojis used
 */
declare(strict_types=1);

$cleanPhone = preg_replace('/[^0-9]/', '', (string)$customer['phone']);
$waPhone = strlen($cleanPhone) === 10 ? '91' . $cleanPhone : $cleanPhone;
?>

<div class="admin-container" style="max-width: 960px;">
    <!-- Breadcrumb Header -->
    <div class="d-flex align-items-center gap-2 mb-2.5">
        <a href="<?= url('mill/customers') ?>" class="admin-btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span><?= __t('nav_customers') ?></span>
        </a>
    </div>

    <!-- Customer Details Banner -->
    <div class="admin-card p-3 p-md-3.5 mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h3 class="fw-bold text-dark mb-1"><?= e($customer['name']) ?></h3>
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <span><?= e($customer['phone']) ?></span>
                    &bull;
                    <span><?= __t('customer_history') ?></span>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="https://wa.me/<?= e($waPhone) ?>" target="_blank" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1.5 px-3 py-1.5 fw-semibold">
                    <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.694.062-1.127-.082-.363-.122-.82-.294-1.411-.555-2.518-1.109-4.148-3.663-4.275-3.834-.125-.171-1.02-1.358-1.02-2.589 0-1.231.644-1.836.872-2.087.228-.251.498-.314.664-.314.166 0 .332.002.477.01.155.008.363-.058.568.435.21.503.716 1.748.778 1.874.062.125.104.271.021.438-.083.166-.124.271-.249.417-.124.145-.262.325-.374.436-.125.125-.255.26-.11.51.145.249.645 1.064 1.385 1.722.952.846 1.756 1.108 2.006 1.233.25.125.395.104.54-.062.146-.167.623-.728.789-.978.166-.249.332-.208.56-.125.228.084 1.453.686 1.702.81.249.125.415.187.477.291.063.104.063.603-.081 1.008z"/></svg>
                    <span><?= __t('whatsapp_btn') ?></span>
                </a>
                <a href="<?= url('mill/orders/new?phone=' . urlencode($customer['phone'])) ?>" class="admin-btn-primary py-1.5 px-3" style="font-size: 0.85rem;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span><?= __t('nav_new_order') ?></span>
                </a>
            </div>
        </div>

        <!-- Customer Quick Metrics -->
        <div class="row g-2 mt-2 pt-2 border-top">
            <div class="col-6 col-md-3">
                <span class="text-muted small d-block"><?= __t('total_visits') ?></span>
                <span class="h5 fw-bold text-dark mb-0"><?= (int)($stats['total_orders'] ?? 0) ?></span>
            </div>
            <div class="col-6 col-md-3">
                <span class="text-muted small d-block"><?= __t('total_weight') ?></span>
                <span class="h5 fw-bold text-dark mb-0"><?= (float)($stats['total_weight_kg'] ?? 0) ?> KG</span>
            </div>
            <div class="col-6 col-md-3">
                <span class="text-muted small d-block"><?= __t('total_spent') ?></span>
                <span class="h5 fw-bold text-success mb-0"><?= format_currency($stats['lifetime_spent'] ?? 0) ?></span>
            </div>
            <div class="col-6 col-md-3">
                <span class="text-muted small d-block"><?= __t('pending_due') ?></span>
                <span class="h5 fw-bold <?= (float)($stats['pending_due'] ?? 0) > 0 ? 'text-danger' : 'text-muted' ?> mb-0">
                    <?= format_currency($stats['pending_due'] ?? 0) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Complete Orders History Table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h5 class="admin-card-title mb-0"><?= __t('customer_history') ?> (<?= count($orders) ?>)</h5>
        </div>

        <div class="table-responsive">
            <?php if (empty($orders)): ?>
                <div class="p-4 text-center text-muted">
                    <p class="mb-0"><?= __t('no_orders') ?></p>
                </div>
            <?php else: ?>
                <table class="table table-hover align-middle mb-0" style="font-size: 0.86rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3"><?= __t('order_no') ?></th>
                            <th><?= __t('date') ?></th>
                            <th><?= __t('service') ?></th>
                            <th><?= __t('weight_kg') ?></th>
                            <th><?= __t('rate_per_kg') ?></th>
                            <th><?= __t('total_price') ?></th>
                            <th><?= __t('payment') ?></th>
                            <th class="text-end pe-3"><?= __t('status') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-dark">#<?= (int)$o['order_number'] ?></td>
                                <td>
                                    <div><?= format_date($o['order_date']) ?></div>
                                    <small class="text-muted"><?= format_time($o['order_time']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= e($o['service_name']) ?></span>
                                    <?php if (!empty($o['notes'])): ?>
                                        <div class="text-muted small" style="font-size: 0.74rem;">Note: <?= e($o['notes']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-semibold text-dark"><?= (float)$o['weight_kg'] ?> KG</td>
                                <td><?= format_currency($o['rate_per_kg']) ?></td>
                                <td class="fw-bold text-dark"><?= format_currency($o['total_amount']) ?></td>
                                <td>
                                    <span class="badge <?= $o['payment_status'] === 'paid' ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger' ?>">
                                        <?= $o['payment_status'] === 'paid' ? __t('paid') : __t('unpaid') ?>
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <?php 
                                    $statusBadgeClass = match ($o['status']) {
                                        'received'   => 'bg-warning-subtle text-warning border-warning',
                                        'processing' => 'bg-info-subtle text-info border-info',
                                        'ready'      => 'bg-success-subtle text-success border-success',
                                        'delivered'  => 'bg-light text-secondary border',
                                        'cancelled'  => 'bg-danger-subtle text-danger border-danger',
                                        default      => 'bg-light text-dark',
                                    };
                                    ?>
                                    <span class="badge border <?= $statusBadgeClass ?>">
                                        <?= __t('status_' . $o['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
