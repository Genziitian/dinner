<?php
/**
 * Modern Vibrant Manager Order History View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold m-0" style="color: #0f172a; letter-spacing: -0.5px;">Order History</h4>
        <small class="text-muted">Manage, search, edit and review past orders</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('cashier/order') ?>" class="btn-boxed-primary">
            <span style="font-size: 1.1rem; line-height: 1;">+</span>
            <span>New Order</span>
        </a>
        <a href="<?= url('manager/exports') ?>" class="btn-boxed-outline">
            <span>📥</span>
            <span>Export CSV</span>
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 1.15rem;">
    <div class="card-body p-3">
        <form method="GET" action="<?= url('manager/orders') ?>" class="row g-2 align-items-end">
            <!-- Quick Period Filter -->
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold">Timeframe</label>
                <select name="filter" class="form-select form-select-sm" onchange="toggleCustomDates(this.value); this.form.submit();">
                    <option value="today" <?= $currentFilter === 'today' ? 'selected' : '' ?>>Today</option>
                    <option value="yesterday" <?= $currentFilter === 'yesterday' ? 'selected' : '' ?>>Yesterday</option>
                    <option value="7days" <?= $currentFilter === '7days' ? 'selected' : '' ?>>Last 7 Days</option>
                    <option value="this_month" <?= $currentFilter === 'this_month' ? 'selected' : '' ?>>This Month</option>
                    <option value="last_month" <?= $currentFilter === 'last_month' ? 'selected' : '' ?>>Last Month</option>
                    <option value="custom" <?= $currentFilter === 'custom' ? 'selected' : '' ?>>Custom Date Range</option>
                    <option value="all" <?= $currentFilter === 'all' ? 'selected' : '' ?>>All Time</option>
                </select>
            </div>

            <!-- Custom Dates (Visible if custom selected) -->
            <div class="col-6 col-md-2" id="customStartWrap" style="<?= $currentFilter !== 'custom' ? 'display: none;' : '' ?>">
                <label class="form-label small">Start Date</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= e($customStart) ?>">
            </div>
            <div class="col-6 col-md-2" id="customEndWrap" style="<?= $currentFilter !== 'custom' ? 'display: none;' : '' ?>">
                <label class="form-label small">End Date</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= e($customEnd) ?>">
            </div>

            <!-- Payment Method Filter -->
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">Payment</label>
                <select name="payment_method" class="form-select form-select-sm">
                    <option value="">All Methods</option>
                    <option value="Cash" <?= $paymentMethod === 'Cash' ? 'selected' : '' ?>>💵 Cash</option>
                    <option value="Online / UPI" <?= ($paymentMethod === 'Online / UPI' || $paymentMethod === 'Online' || $paymentMethod === 'QR / UPI') ? 'selected' : '' ?>>📱 Online / UPI</option>
                </select>
            </div>

            <!-- Search Field -->
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Order #, Customer..." value="<?= e($search) ?>">
            </div>

            <div class="col-12 col-md-auto">
                <button type="submit" class="btn btn-save-order btn-sm px-4">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 1.15rem; overflow: hidden;">
    <div class="card-body p-0">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5 text-muted">
                <div class="fs-2 mb-2">📜</div>
                <div>No orders found matching the filter criteria.</div>
            </div>
        <?php else: ?>
            <!-- Desktop Table View (>= 768px) -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-3">ORDER #</th>
                            <th class="py-3">DATE & TIME</th>
                            <th class="py-3">CUSTOMER</th>
                            <th class="py-3">ITEMS SUMMARY</th>
                            <th class="py-3">TOTAL</th>
                            <th class="py-3">PAYMENT</th>
                            <th class="py-3">STAFF</th>
                            <th class="py-3 text-end px-3">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $ord): ?>
                            <tr>
                                <td class="px-3">
                                    <span class="badge bg-dark rounded-pill px-3 py-2 fs-6">#<?= (int)$ord['order_number'] ?></span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= format_date($ord['order_date']) ?></div>
                                    <small class="text-muted"><?= format_time($ord['order_time']) ?></small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= e($ord['customer_name'] ?? 'Walk-in') ?></div>
                                    <?php if (!empty($ord['customer_phone'])): ?>
                                        <small class="text-muted"><?= e($ord['customer_phone']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="max-width: 250px; font-size: 0.85rem;" class="text-truncate" title="<?php 
                                        $str = [];
                                        foreach ($ord['items'] as $it) {
                                            $str[] = "{$it['item_name_snapshot']} ({$it['variant_name_snapshot']}) x {$it['quantity']}";
                                        }
                                        echo e(implode(', ', $str));
                                    ?>">
                                        <span class="badge bg-light text-dark border"><?= count($ord['items']) ?> items</span>: <?= e(implode(', ', $str)) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold fs-6" style="color: #0f172a;"><?= format_currency($ord['total']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $ord['payment_method'] === 'Cash' ? 'success' : 'warning text-dark' ?>">
                                        <?= e($ord['payment_method'] === 'Cash' ? 'Cash' : 'Online / UPI') ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted"><?= e($ord['created_by_username']) ?></small>
                                </td>
                                <td class="text-end px-3">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?= url('orders/view?id=' . $ord['id']) ?>" class="btn btn-sm btn-light border fw-semibold">View</a>
                                        <a href="<?= url('manager/orders/edit?id=' . $ord['id']) ?>" class="btn btn-sm btn-outline-secondary fw-semibold">Edit</a>
                                        <form action="<?= url('manager/orders/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete Order #<?= (int)$ord['order_number'] ?>?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="order_id" value="<?= (int)$ord['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Layout (< 768px, 100% Mobile Optimized) -->
            <div class="d-md-none p-3">
                <?php foreach ($orders as $ord): ?>
                    <div class="mobile-card-row">
                        <!-- Top Row: Order # + Date/Time + Big Price -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark rounded-pill px-2.5 py-1.5 fw-bold fs-6" style="font-size: 0.95rem !important;">#<?= (int)$ord['order_number'] ?></span>
                                <div class="small fw-semibold text-muted">
                                    <?= format_time($ord['order_time']) ?> · <?= format_date($ord['order_date']) ?>
                                </div>
                            </div>
                            <div class="fs-5 fw-bold" style="color: #0f172a;"><?= format_currency($ord['total']) ?></div>
                        </div>

                        <!-- Customer & Items Summary -->
                        <div class="p-2.5 bg-light rounded-3 my-2 border" style="border-color: #f1f5f9 !important;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark small"><?= e($ord['customer_name'] ?? 'Walk-in Customer') ?></span>
                                <span class="badge bg-<?= $ord['payment_method'] === 'Cash' ? 'success' : 'warning text-dark' ?>">
                                    <?= e($ord['payment_method'] === 'Cash' ? 'Cash' : 'Online / UPI') ?>
                                </span>
                            </div>
                            <div class="text-muted small" style="font-size: 0.8rem;">
                                <?php 
                                    $itemLabels = [];
                                    foreach ($ord['items'] as $it) {
                                        $itemLabels[] = "{$it['item_name_snapshot']} ({$it['variant_name_snapshot']}) × {$it['quantity']}";
                                    }
                                    echo e(implode(', ', $itemLabels));
                                ?>
                            </div>
                        </div>

                        <!-- Staff & Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-1">
                            <small class="text-muted">By: <strong><?= e($ord['created_by_username']) ?></strong></small>
                            <div class="d-flex gap-1.5">
                                <a href="<?= url('orders/view?id=' . $ord['id']) ?>" class="btn btn-sm btn-light border fw-bold px-2.5 py-1">View</a>
                                <a href="<?= url('manager/orders/edit?id=' . $ord['id']) ?>" class="btn btn-sm btn-outline-secondary fw-bold px-2.5 py-1">Edit</a>
                                <form action="<?= url('manager/orders/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete Order #<?= (int)$ord['order_number'] ?>?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="order_id" value="<?= (int)$ord['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger fw-bold px-2.5 py-1">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
