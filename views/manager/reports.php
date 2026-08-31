<?php
/**
 * Financial Reports View Template
 * Matches Android Native App UI 1:1 (Dynamic Data)
 */
declare(strict_types=1);
$currentPeriod = $currentPeriod ?? 'today';
?>

<div class="app-container">
    <!-- Top App Bar Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= url('manager/dashboard') ?>" class="top-bar-back-btn" title="Back to Dashboard">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h4 class="top-bar-title m-0">Financial Reports</h4>
        </div>
        <div class="top-bar-actions">
            <a href="<?= url('manager/exports') ?>" class="top-bar-icon-btn" title="CSV Exports">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </a>
            <a href="<?= url('manager/reports') ?>" class="top-bar-icon-btn" title="Refresh">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </a>
        </div>
    </div>

    <!-- Period Filter Chips -->
    <?php
    $periods = [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        '7days' => 'Last 7 Days',
        'this_month' => 'This Month',
        'last_month' => 'Last Month',
        '6months' => '6 Months',
        'year' => 'Yearly'
    ];
    ?>
    <div class="filter-chips-scroll mb-3">
        <?php foreach ($periods as $key => $label): ?>
            <a 
                href="<?= url('manager/reports?period=' . $key) ?>" 
                class="filter-chip-btn <?= $currentPeriod === $key ? 'active' : '' ?>"
            >
                <?= e($label) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- 1. Dark Revenue Summary Card -->
    <div class="dark-revenue-card">
        <div class="card-heading">TOTAL SALES (<?= strtoupper(e($currentPeriod)) ?>)</div>
        <div class="revenue-amount"><?= format_currency($stats['total_sales'] ?? 0.0) ?></div>
        <div class="small text-white-50 mb-2">
            <span style="color: var(--brand-amber); font-weight: 700;"><?= (int)($stats['total_orders'] ?? 0) ?> orders</span> completed in this period
        </div>
        <div class="revenue-breakdown">
            <span class="cash-text">Cash: <?= format_currency($stats['cash_sales'] ?? 0.0) ?></span>
            <span class="upi-text">UPI: <?= format_currency($stats['online_sales'] ?? 0.0) ?></span>
        </div>
    </div>

    <!-- 2. Periodic Overview Comparison Cards -->
    <div class="section-header">
        <h6 class="section-title">PERIODIC OVERVIEW</h6>
    </div>

    <div class="orders-list-wrap">
        <?php 
        $periodsList = [
            ['label' => 'Today', 'key' => 'today'],
            ['label' => 'Yesterday', 'key' => 'yesterday'],
            ['label' => 'This Month', 'key' => 'this_month'],
            ['label' => 'Last Month', 'key' => 'last_month'],
            ['label' => 'Past 6 Months', 'key' => 'six_months'],
            ['label' => 'This Year', 'key' => 'year'],
        ];
        foreach ($periodsList as $p):
            $row = $summary[$p['key']] ?? ['total_orders' => 0, 'cash_sales' => 0, 'online_sales' => 0, 'total_sales' => 0];
        ?>
            <div class="order-card-android">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-dark fs-6"><?= $p['label'] ?></span>
                        <span class="badge bg-light text-dark border"><?= (int)$row['total_orders'] ?> Orders</span>
                    </div>
                    <div class="fw-bold fs-5" style="color: #0f172a;">
                        <?= format_currency((float)$row['total_sales']) ?>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top small" style="border-color: #f1f5f9 !important;">
                    <span class="text-success fw-bold">💵 Cash: <?= format_currency((float)$row['cash_sales']) ?></span>
                    <span class="text-warning fw-bold">📱 UPI: <?= format_currency((float)$row['online_sales']) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
