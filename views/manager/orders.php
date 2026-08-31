<?php
/**
 * Manager & Cashier Order History View Template
 * Matches Android Native App UI 1:1 (Dynamic Data)
 */
declare(strict_types=1);
$currentUser = current_user();
$currentFilter = $currentFilter ?? 'today';
$search = $search ?? '';
$backUrl = ($currentUser && $currentUser['role'] === User::ROLE_CASHIER) ? url('cashier/order') : url('manager/dashboard');
?>

<div class="app-container">
    <!-- Top App Bar Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= $backUrl ?>" class="top-bar-back-btn" title="Back">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h4 class="top-bar-title m-0">Order History</h4>
        </div>
        <div class="top-bar-actions">
            <a href="<?= url('manager/orders') ?>" class="top-bar-icon-btn" title="Refresh">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </a>
        </div>
    </div>

    <!-- Search Input Bar -->
    <form method="GET" action="<?= url('manager/orders') ?>" class="search-input-wrap">
        <input type="hidden" name="filter" value="<?= e($currentFilter) ?>">
        <?php if (!empty($paymentMethod)): ?>
            <input type="hidden" name="payment_method" value="<?= e($paymentMethod) ?>">
        <?php endif; ?>
        <svg class="search-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input 
            type="text" 
            name="search" 
            class="search-input-custom" 
            placeholder="Search by customer, phone, order #..." 
            value="<?= e($search) ?>"
            onchange="this.form.submit()"
        >
    </form>

    <!-- Timeframe Filter Chips Row -->
    <?php
    $filters = [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        '7days' => 'Last 7 Days',
        'this_month' => 'This Month',
        'last_month' => 'Last Month',
        'all' => 'All History'
    ];
    ?>
    <div class="filter-chips-scroll">
        <?php foreach ($filters as $key => $label): ?>
            <a 
                href="<?= url('manager/orders?filter=' . $key . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" 
                class="filter-chip-btn <?= $currentFilter === $key ? 'active' : '' ?>"
            >
                <?= e($label) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Orders Cards List -->
    <?php if (empty($orders)): ?>
        <div class="card border-0 p-5 text-center my-3" style="border-radius: 16px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📜</div>
            <div class="fw-bold text-dark fs-6 mb-1">
                <?= !empty($search) ? 'No matching orders found' : 'No orders recorded for this period' ?>
            </div>
            <div class="text-secondary small">
                <?= !empty($search) ? 'Try searching with another customer name, phone, or order #' : 'New orders will appear here automatically.' ?>
            </div>
        </div>
    <?php else: ?>
        <div class="orders-list-wrap">
            <?php foreach ($orders as $order): ?>
                <?php 
                    $isCash = strtolower($order['payment_method'] ?? 'cash') === 'cash';
                    $payPillClass = $isCash ? 'cash' : 'upi';
                    $payText = $isCash ? 'Cash' : 'UPI';
                ?>
                <a href="<?= url('orders/view?id=' . (int)$order['id']) ?>" class="order-card-android">
                    <!-- Row 1: Order# + Payment Badge | Total + Chevron -->
                    <div class="order-card-row-top">
                        <div class="order-card-left-group">
                            <span class="order-number-pill">Order #<?= (int)$order['order_number'] ?></span>
                            <span class="payment-pill <?= $payPillClass ?>"><?= $payText ?></span>
                        </div>
                        <div class="order-card-amount">
                            <span><?= format_currency((float)$order['total']) ?></span>
                            <span class="chevron">›</span>
                        </div>
                    </div>

                    <!-- Row 2: Customer Name -->
                    <div class="order-customer-name">
                        <?= e(!empty($order['customer_name']) ? $order['customer_name'] : 'Walk-in Customer') ?>
                    </div>

                    <!-- Row 3: Date & Time -->
                    <div class="order-card-meta">
                        <?= date('Y-m-d', strtotime($order['created_at'])) ?> · <?= date('H:i:s', strtotime($order['created_at'])) ?>
                        <?php if (!empty($order['customer_phone'])): ?>
                            · 📞 <?= e($order['customer_phone']) ?>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
