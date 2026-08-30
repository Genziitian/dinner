<?php
/**
 * Modern Vibrant Cashier Today's Summary View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold m-0" style="color: #0f172a; letter-spacing: -0.5px;">Collection Summary</h4>
                <small class="text-muted"><?= format_date($todayDate) ?> · <?= e($restaurant['name']) ?></small>
            </div>
            <a href="<?= url('cashier/order') ?>" class="btn btn-save-order btn-sm px-3">+ New Order</a>
        </div>

        <!-- Total Revenue Card -->
        <div class="card shadow-sm mb-3 border-0 metric-card-primary" style="border-radius: 1.25rem;">
            <div class="card-body p-4 text-center">
                <div class="text-white-50 text-uppercase fw-bold" style="font-size: 0.78rem; letter-spacing: 1px;">TOTAL SALES TODAY</div>
                <div class="metric-value text-white my-2"><?= format_currency($stats['total_sales']) ?></div>
                <div class="badge bg-warning text-dark px-3 py-2 fw-bold fs-6 rounded-pill">
                    <?= (int)$stats['total_orders'] ?> Orders Completed
                </div>
            </div>
        </div>

        <!-- Payment Breakdown Cards -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 1.25rem; overflow: hidden;">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold m-0" style="color: #0f172a;">Payment Methods Breakdown</h6>
            </div>
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <span style="font-size: 1.5rem;">💵</span>
                        <div>
                            <div class="fw-bold text-dark fs-6">Cash Collection</div>
                            <small class="text-muted">Recorded physical cash</small>
                        </div>
                    </div>
                    <span class="fw-bold fs-5 text-success"><?= format_currency($stats['cash_sales']) ?></span>
                </div>

                <div class="d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-3">
                        <span style="font-size: 1.5rem;">📱</span>
                        <div>
                            <div class="fw-bold text-dark fs-6">Online / UPI Collection</div>
                            <small class="text-muted">QR code scanner, UPI apps, cards</small>
                        </div>
                    </div>
                    <span class="fw-bold fs-5 text-warning"><?= format_currency($stats['online_sales']) ?></span>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2">
            <a href="<?= url('cashier/order') ?>" class="btn btn-save-order py-3 fs-6">
                <span>Start Next Order</span>
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>
</div>
