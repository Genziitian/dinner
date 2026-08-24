<?php
/**
 * Modern Vibrant Manager Financial & Sales Reports View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold m-0" style="color: #0f172a; letter-spacing: -0.5px;">Financial & Sales Reports</h4>
        <small class="text-muted">Server-side aggregated revenue and payment method breakdown</small>
    </div>
    <a href="<?= url('manager/exports') ?>" class="btn btn-outline-secondary btn-sm">Export to CSV</a>
</div>

<!-- Report Period Tabs / Filters -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 1.15rem;">
    <div class="card-body p-3">
        <form method="GET" action="<?= url('manager/reports') ?>" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold">Reporting Period</label>
                <select name="period" class="form-select form-select-sm" onchange="toggleCustomDates(this.value); if(this.value!=='custom') this.form.submit();">
                    <option value="today" <?= $currentPeriod === 'today' ? 'selected' : '' ?>>Today</option>
                    <option value="yesterday" <?= $currentPeriod === 'yesterday' ? 'selected' : '' ?>>Yesterday</option>
                    <option value="7days" <?= $currentPeriod === '7days' ? 'selected' : '' ?>>Last 7 Days</option>
                    <option value="this_month" <?= $currentPeriod === 'this_month' ? 'selected' : '' ?>>This Month</option>
                    <option value="last_month" <?= $currentPeriod === 'last_month' ? 'selected' : '' ?>>Last Month</option>
                    <option value="3months" <?= $currentPeriod === '3months' ? 'selected' : '' ?>>Last 3 Months</option>
                    <option value="6months" <?= $currentPeriod === '6months' ? 'selected' : '' ?>>Last 6 Months</option>
                    <option value="year" <?= $currentPeriod === 'year' ? 'selected' : '' ?>>This Year</option>
                    <option value="custom" <?= $currentPeriod === 'custom' ? 'selected' : '' ?>>Custom Date Range</option>
                </select>
            </div>

            <div class="col-6 col-md-3" id="customStartWrap" style="<?= $currentPeriod !== 'custom' ? 'display: none;' : '' ?>">
                <label class="form-label small">Start Date</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= e($customStart ?? '') ?>">
            </div>
            <div class="col-6 col-md-3" id="customEndWrap" style="<?= $currentPeriod !== 'custom' ? 'display: none;' : '' ?>">
                <label class="form-label small">End Date</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= e($customEnd ?? '') ?>">
            </div>

            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-save-order btn-sm w-100">Update Report</button>
            </div>
        </form>
    </div>
</div>

<!-- Active Period 3 Balanced Metrics -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card metric-card-primary h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-white-50 text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">TOTAL REVENUE</div>
                        <div class="fs-1 fw-bold text-white my-1"><?= format_currency($stats['total_sales']) ?></div>
                    </div>
                    <span style="font-size: 2rem;">💰</span>
                </div>
                <small class="text-white-50"><?= (int)$stats['total_orders'] ?> orders in selected period</small>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4">
        <div class="card metric-card-cash h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-success text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">CASH SALES</div>
                        <div class="fs-1 fw-bold text-success my-1"><?= format_currency($stats['cash_sales']) ?></div>
                    </div>
                    <span style="font-size: 2rem;">💵</span>
                </div>
                <small class="text-muted"><?= $stats['total_sales'] > 0 ? round(($stats['cash_sales'] / $stats['total_sales']) * 100, 1) : 0 ?>% of total revenue</small>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4">
        <div class="card metric-card-upi h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-warning text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">ONLINE / UPI SALES</div>
                        <div class="fs-1 fw-bold text-warning my-1"><?= format_currency($stats['online_sales']) ?></div>
                    </div>
                    <span style="font-size: 2rem;">📱</span>
                </div>
                <small class="text-muted"><?= $stats['total_sales'] > 0 ? round(($stats['online_sales'] / $stats['total_sales']) * 100, 1) : 0 ?>% of total revenue</small>
            </div>
        </div>
    </div>
</div>

<!-- Comparison Summary Table & Mobile Cards -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 1.15rem; overflow: hidden;">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="fw-bold m-0" style="color: #0f172a;">Periodic Comparison Overview</h5>
    </div>
    <div class="card-body p-0">
        <!-- Desktop Table (>= 768px) -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-3">TIMEFRAME</th>
                        <th class="py-3 text-center">TOTAL ORDERS</th>
                        <th class="py-3 text-end">CASH</th>
                        <th class="py-3 text-end">ONLINE / UPI</th>
                        <th class="py-3 text-end px-3">TOTAL REVENUE</th>
                    </tr>
                </thead>
                <tbody>
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
                    <tr>
                        <td class="px-3"><strong><?= $p['label'] ?></strong></td>
                        <td class="text-center"><span class="badge bg-light text-dark border"><?= $row['total_orders'] ?></span></td>
                        <td class="text-end text-success fw-semibold"><?= format_currency($row['cash_sales']) ?></td>
                        <td class="text-end text-warning fw-semibold"><?= format_currency($row['online_sales']) ?></td>
                        <td class="text-end px-3 fw-bold fs-6" style="color: #0f172a;"><?= format_currency($row['total_sales']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Comparison Cards (< 768px, 100% Mobile Optimized) -->
        <div class="d-md-none p-3">
            <?php foreach ($periodsList as $p): 
                $row = $summary[$p['key']] ?? ['total_orders' => 0, 'cash_sales' => 0, 'online_sales' => 0, 'total_sales' => 0];
            ?>
                <div class="mobile-card-row">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark fs-6"><?= $p['label'] ?></span>
                        <span class="badge bg-light text-dark border"><?= (int)$row['total_orders'] ?> Orders</span>
                    </div>
                    <div class="fs-4 fw-bold mb-2" style="color: #0f172a;"><?= format_currency($row['total_sales']) ?></div>
                    <div class="d-flex justify-content-between pt-2 border-top small" style="border-color: #f1f5f9 !important;">
                        <div>
                            <span class="text-muted">Cash:</span>
                            <strong class="text-success"><?= format_currency($row['cash_sales']) ?></strong>
                        </div>
                        <div>
                            <span class="text-muted">Online/UPI:</span>
                            <strong class="text-warning"><?= format_currency($row['online_sales']) ?></strong>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function toggleCustomDates(val) {
    const s = document.getElementById('customStartWrap');
    const e = document.getElementById('customEndWrap');
    if (val === 'custom') {
        s.style.display = 'block';
        e.style.display = 'block';
    } else {
        s.style.display = 'none';
        e.style.display = 'none';
    }
}
</script>
