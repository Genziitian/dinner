<?php
/**
 * Cashier Shift Summary View Template
 * Matches Android Native App UI 1:1 (Dynamic Data)
 */
declare(strict_types=1);
?>

<div class="app-container">
    <!-- Top App Bar Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= url('cashier/order') ?>" class="top-bar-back-btn" title="Back to POS">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h4 class="top-bar-title m-0">Shift Summary</h4>
        </div>
        <div class="top-bar-actions">
            <a href="<?= url('cashier/summary') ?>" class="top-bar-icon-btn" title="Refresh">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </a>
        </div>
    </div>

    <!-- 1. Dark Revenue Summary Card -->
    <div class="dark-revenue-card">
        <div class="card-heading">TODAY'S SHIFT COLLECTION</div>
        <div class="revenue-amount"><?= format_currency($stats['total_sales'] ?? 0.0) ?></div>
        <div class="small text-white-50 mb-2">
            <span style="color: var(--brand-amber); font-weight: 700;"><?= (int)($stats['total_orders'] ?? 0) ?> orders</span> completed today
        </div>
        <div class="revenue-breakdown">
            <span class="cash-text">Cash: <?= format_currency($stats['cash_sales'] ?? 0.0) ?></span>
            <span class="upi-text">UPI: <?= format_currency($stats['online_sales'] ?? 0.0) ?></span>
        </div>
    </div>

    <!-- 2. Breakdown Card -->
    <div class="outlet-details-card">
        <div class="outlet-card-header">
            <h6 class="outlet-card-title">
                <span>💳</span>
                <span>Payment Methods Breakdown</span>
            </h6>
        </div>

        <div class="outlet-info-list">
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size: 1.35rem;">💵</span>
                    <div>
                        <div class="fw-bold text-dark">Cash Collection</div>
                        <div class="text-secondary small">Direct physical cash receipts</div>
                    </div>
                </div>
                <div class="fw-bold text-success fs-5"><?= format_currency($stats['cash_sales'] ?? 0.0) ?></div>
            </div>

            <div class="d-flex justify-content-between align-items-center py-2">
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size: 1.35rem;">📱</span>
                    <div>
                        <div class="fw-bold text-dark">Online / UPI Collection</div>
                        <div class="text-secondary small">QR codes & digital transfers</div>
                    </div>
                </div>
                <div class="fw-bold text-warning fs-5"><?= format_currency($stats['online_sales'] ?? 0.0) ?></div>
            </div>
        </div>
    </div>

    <!-- Start Next Order Button -->
    <a href="<?= url('cashier/order') ?>" class="btn w-100 py-3 fw-bold text-white shadow-sm" style="background: var(--brand-orange); border-radius: 14px; font-size: 1rem;">
        <span>Start Next Order</span>
        <span class="ms-1">➔</span>
    </a>
</div>
