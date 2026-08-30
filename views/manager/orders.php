<?php
/**
 * Modern Vibrant Manager Order History View Template (Mobile-Redesigned)
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<!-- Desktop Header (>= 768px) -->
<div class="d-none d-md-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
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

<!-- Mobile Header (< 768px, Compact & Clean) -->
<div class="d-md-none d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold m-0" style="color: #0f172a; letter-spacing: -0.5px; font-size: 1.25rem;">Order History</h4>
        <small class="text-muted" style="font-size: 0.78rem;">Manage and review past orders</small>
    </div>
    <a href="<?= url('manager/exports') ?>" class="btn btn-sm btn-light border px-2 py-1" style="border-radius: 0.6rem;" title="Export CSV">
        <span style="font-size: 1rem;">📥</span>
    </a>
</div>

<!-- Mobile Compact Search + Filter Bar (< 768px) -->
<form method="GET" action="<?= url('manager/orders') ?>" class="mb-3 d-md-none">
    <div class="d-flex gap-2">
        <!-- Search input with embedded icon -->
        <div class="position-relative flex-grow-1">
            <svg style="position: absolute; left: 12px; top: 10px; color: #94a3b8; pointer-events: none;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" class="form-control form-control-sm" style="padding-left: 2.3rem; border-radius: 0.6rem; height: 36px; font-size: 0.88rem; border: 1.5px solid #cbd5e1;" placeholder="Search Order #, Customer..." value="<?= e($search) ?>">
        </div>
        <!-- Toggle Filter Settings Button -->
        <button type="button" class="btn btn-light border d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 0.6rem; border-color: #cbd5e1 !important;" onclick="toggleMobileFilters()">
            ⚙️
        </button>
        <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold" style="border-radius: 0.6rem; height: 36px;">Go</button>
    </div>
    
    <!-- Expandable Filter Settings -->
    <div id="mobileFiltersPanel" class="mt-2 p-3 bg-white border rounded-3 shadow-sm" style="display: none; border-radius: 0.8rem; border-color: #e2e8f0 !important;">
        <div class="row g-2">
            <div class="col-6">
                <label class="form-label small fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">TIMEFRAME</label>
                <select name="filter" class="form-select form-select-sm" onchange="toggleCustomDatesMobile(this.value);" style="border-radius: 0.5rem;">
                    <option value="today" <?= $currentFilter === 'today' ? 'selected' : '' ?>>Today</option>
                    <option value="yesterday" <?= $currentFilter === 'yesterday' ? 'selected' : '' ?>>Yesterday</option>
                    <option value="7days" <?= $currentFilter === '7days' ? 'selected' : '' ?>>Last 7 Days</option>
                    <option value="this_month" <?= $currentFilter === 'this_month' ? 'selected' : '' ?>>This Month</option>
                    <option value="last_month" <?= $currentFilter === 'last_month' ? 'selected' : '' ?>>Last Month</option>
                    <option value="custom" <?= $currentFilter === 'custom' ? 'selected' : '' ?>>Custom Range</option>
                    <option value="all" <?= $currentFilter === 'all' ? 'selected' : '' ?>>All Time</option>
                </select>
            </div>
            <div class="col-6">
                <label class="form-label small fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">PAYMENT</label>
                <select name="payment_method" class="form-select form-select-sm" style="border-radius: 0.5rem;">
                    <option value="">All Methods</option>
                    <option value="Cash" <?= $paymentMethod === 'Cash' ? 'selected' : '' ?>>💵 Cash</option>
                    <option value="Online / UPI" <?= ($paymentMethod === 'Online / UPI' || $paymentMethod === 'Online' || $paymentMethod === 'QR / UPI') ? 'selected' : '' ?>>📱 Online</option>
                </select>
            </div>
            
            <!-- Custom dates (Mobile) -->
            <div class="col-6 mt-2" id="customStartWrapMobile" style="<?= $currentFilter !== 'custom' ? 'display: none;' : '' ?>">
                <label class="form-label small text-muted" style="font-size: 0.7rem;">Start Date</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= e($customStart) ?>" style="border-radius: 0.5rem;">
            </div>
            <div class="col-6 mt-2" id="customEndWrapMobile" style="<?= $currentFilter !== 'custom' ? 'display: none;' : '' ?>">
                <label class="form-label small text-muted" style="font-size: 0.7rem;">End Date</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= e($customEnd) ?>" style="border-radius: 0.5rem;">
            </div>
        </div>
    </div>
</form>

<!-- Desktop Filter Bar (>= 768px) -->
<div class="card shadow-sm border-0 mb-4 d-none d-md-block" style="border-radius: 1.15rem; border: 1px solid #e2e8f0 !important;">
    <div class="card-body p-3">
        <form method="GET" action="<?= url('manager/orders') ?>" class="row g-2 align-items-end">
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

            <div class="col-6 col-md-2" id="customStartWrap" style="<?= $currentFilter !== 'custom' ? 'display: none;' : '' ?>">
                <label class="form-label small">Start Date</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= e($customStart) ?>">
            </div>
            <div class="col-6 col-md-2" id="customEndWrap" style="<?= $currentFilter !== 'custom' ? 'display: none;' : '' ?>">
                <label class="form-label small">End Date</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= e($customEnd) ?>">
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">Payment</label>
                <select name="payment_method" class="form-select form-select-sm">
                    <option value="">All Methods</option>
                    <option value="Cash" <?= $paymentMethod === 'Cash' ? 'selected' : '' ?>>💵 Cash</option>
                    <option value="Online / UPI" <?= ($paymentMethod === 'Online / UPI' || $paymentMethod === 'Online' || $paymentMethod === 'QR / UPI') ? 'selected' : '' ?>>📱 Online / UPI</option>
                </select>
            </div>

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

<!-- Orders List Container -->
<?php if (empty($orders)): ?>
    <div class="text-center py-5 text-muted bg-white border rounded-3 mb-5">
        <div class="fs-2 mb-2">📜</div>
        <div>No orders found matching the filter criteria.</div>
    </div>
<?php else: ?>
    <!-- Desktop Table View (>= 768px) -->
    <div class="card shadow-sm border-0 mb-4 d-none d-md-block" style="border-radius: 1.15rem; overflow: hidden; border: 1px solid #e2e8f0 !important;">
        <div class="card-body p-0">
            <div class="table-responsive">
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
        </div>
    </div>

    <!-- Mobile Native-style List (< 768px, 100% Mobile Optimized) -->
    <div class="d-md-none d-flex flex-column gap-2 mb-5">
        <?php foreach ($orders as $ord): ?>
            <div class="bg-white border rounded-3 p-3 shadow-sm" style="border-color: #e2e8f0 !important; transition: border-color 0.15s ease;">
                <!-- Top Row: Circle Order # + Styled Time | Big Price -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-flex align-items-center justify-content-center rounded-circle bg-dark text-white fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">
                            #<?= (int)$ord['order_number'] ?>
                        </span>
                        <div class="small fw-semibold text-secondary">
                            <?= format_time($ord['order_time']) ?>
                        </div>
                    </div>
                    <div class="fs-5 fw-bold" style="color: #0f172a;"><?= format_currency($ord['total']) ?></div>
                </div>

                <!-- Customer Details & Payment Mode -->
                <div class="d-flex justify-content-between align-items-center py-2 border-top border-bottom" style="border-color: #f1f5f9 !important;">
                    <div style="min-width: 0; flex: 1;">
                        <div class="fw-bold text-dark small text-truncate"><?= e($ord['customer_name'] ?? 'Walk-in Customer') ?></div>
                        <?php if (!empty($ord['customer_phone'])): ?>
                            <div class="text-muted" style="font-size: 0.72rem;"><?= e($ord['customer_phone']) ?></div>
                        <?php endif; ?>
                    </div>
                    <span class="badge" style="background-color: <?= $ord['payment_method'] === 'Cash' ? '#10b981' : '#ea580c' ?>12; color: <?= $ord['payment_method'] === 'Cash' ? '#059669' : '#d97706' ?>; border: 1px solid <?= $ord['payment_method'] === 'Cash' ? '#10b981' : '#ea580c' ?>25; font-size: 0.65rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 0.4rem;">
                        <?= e($ord['payment_method'] === 'Cash' ? 'CASH' : 'ONLINE') ?>
                    </span>
                </div>

                <!-- Items Row -->
                <div class="text-muted py-2" style="font-size: 0.78rem; line-height: 1.4;">
                    <?php 
                        $itemLabels = [];
                        foreach ($ord['ord_items'] ?? $ord['items'] as $it) {
                            $itemLabels[] = "{$it['item_name_snapshot']} ({$it['variant_name_snapshot']}) × " . ((float)$it['quantity'] == (int)$it['quantity'] ? (int)$it['quantity'] : $it['quantity']);
                        }
                        echo e(implode(', ', $itemLabels));
                    ?>
                </div>

                <!-- Footer Staff & Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-1 pt-2 border-top" style="border-color: #f1f5f9 !important;">
                    <small class="text-muted">By: <strong class="text-dark"><?= e($ord['created_by_username']) ?></strong></small>
                    <div class="d-flex gap-1">
                        <a href="<?= url('orders/view?id=' . $ord['id']) ?>" class="btn btn-sm btn-light border fw-semibold py-1 px-2.5" style="border-radius: 0.45rem; font-size: 0.75rem;">View</a>
                        <a href="<?= url('manager/orders/edit?id=' . $ord['id']) ?>" class="btn btn-sm btn-outline-secondary fw-semibold py-1 px-2.5" style="border-radius: 0.45rem; font-size: 0.75rem;">Edit</a>
                        <form action="<?= url('manager/orders/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete Order #<?= (int)$ord['order_number'] ?>?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="order_id" value="<?= (int)$ord['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold py-1 px-2.5" style="border-radius: 0.45rem; font-size: 0.75rem;">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
function toggleMobileFilters() {
    const panel = document.getElementById('mobileFiltersPanel');
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
    } else {
        panel.style.display = 'none';
    }
}

function toggleCustomDatesMobile(val) {
    const s = document.getElementById('customStartWrapMobile');
    const e = document.getElementById('customEndWrapMobile');
    if (val === 'custom') {
        s.style.display = 'block';
        e.style.display = 'block';
    } else {
        s.style.display = 'none';
        e.style.display = 'none';
    }
}

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
