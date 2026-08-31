<?php
/**
 * Manager Dashboard View Template
 * Matches Android Native App UI 1:1 (Dynamic Data)
 */
declare(strict_types=1);
$currentUser = current_user();
?>

<div class="app-container">
    <!-- Android Native Top App Bar Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="brand-pill-tag mb-1">
                <span class="dot"></span>
                <span>GI ORDER POS</span>
            </div>
            <h4 class="top-bar-title"><?= e($restaurant['name']) ?></h4>
            <div class="top-bar-subtitle">Manager Dashboard</div>
        </div>
        <div class="top-bar-actions">
            <button type="button" class="top-bar-icon-btn orange" onclick="openQrLookupModal()" title="Scan / Search QR Receipt">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            </button>
            <a href="<?= url('manager/dashboard') ?>" class="top-bar-icon-btn" title="Refresh">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </a>
            <a href="<?= url('logout') ?>" class="top-bar-icon-btn red" title="Logout">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </a>
        </div>
    </div>

    <!-- 1. Dark Revenue Summary Card -->
    <div class="dark-revenue-card">
        <div class="card-heading">TODAY'S REVENUE</div>
        <div class="revenue-amount"><?= format_currency($stats['total_sales'] ?? 0.0) ?></div>
        <div class="revenue-breakdown">
            <span class="cash-text">Cash: <?= format_currency($stats['cash_sales'] ?? 0.0) ?></span>
            <span class="upi-text">UPI: <?= format_currency($stats['online_sales'] ?? 0.0) ?></span>
        </div>
    </div>

    <!-- 2. QUICK ACTIONS (2x2 Grid) -->
    <div class="section-header">
        <h6 class="section-title">QUICK ACTIONS</h6>
    </div>

    <div class="quick-actions-grid">
        <!-- New Order -->
        <a href="<?= url('cashier/order') ?>" class="quick-action-card">
            <div class="action-icon-box orange">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <div class="card-title">New Order</div>
                <div class="card-subtitle">POS Billing Screen</div>
            </div>
        </a>

        <!-- Orders -->
        <a href="<?= url('manager/orders') ?>" class="quick-action-card">
            <div class="action-icon-box blue">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="card-title">Orders</div>
                <div class="card-subtitle">View History</div>
            </div>
        </a>

        <!-- Menu Items -->
        <a href="<?= url('manager/items') ?>" class="quick-action-card">
            <div class="action-icon-box green">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <div class="card-title">Menu Items</div>
                <div class="card-subtitle">Rates & Portions</div>
            </div>
        </a>

        <!-- Reports -->
        <a href="<?= url('manager/reports') ?>" class="quick-action-card">
            <div class="action-icon-box purple">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <div class="card-title">Reports</div>
                <div class="card-subtitle">Sales & Analytics</div>
            </div>
        </a>
    </div>

    <!-- 3. RECENT ORDERS -->
    <div class="section-header">
        <h6 class="section-title">RECENT ORDERS</h6>
        <a href="<?= url('manager/orders') ?>" class="section-link">View All</a>
    </div>

    <?php if (empty($recentOrders)): ?>
        <div class="card border-0 p-4 text-center" style="border-radius: 14px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
            <div style="font-size: 2rem; margin-bottom: 0.25rem;">🧾</div>
            <div class="text-secondary small fw-bold">No recent orders recorded today.</div>
        </div>
    <?php else: ?>
        <div class="orders-list-wrap">
            <?php foreach ($recentOrders as $order): ?>
                <?php 
                    $isCash = strtolower($order['payment_method']) === 'cash';
                    $payPillClass = $isCash ? 'cash' : 'upi';
                ?>
                <a href="<?= url('manager/orders/view?id=' . (int)$order['id']) ?>" class="order-card-android">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold fs-6" style="color: #0f172a;">Order #<?= (int)$order['order_number'] ?></span>
                                <span class="payment-pill <?= $payPillClass ?>"><?= strtoupper(e($order['payment_method'] === 'Cash' ? 'CASH' : 'UPI')) ?></span>
                            </div>
                            <div class="order-card-meta">
                                <?= date('H:i:s', strtotime($order['created_at'])) ?> · <?= e(!empty($order['customer_name']) ? $order['customer_name'] : 'Walk-in Customer') ?>
                            </div>
                        </div>
                        <div class="fs-5 fw-bold" style="color: #0f172a;">
                            <?= format_currency((float)$order['total']) ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
