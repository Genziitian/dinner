<?php
/**
 * Modern Vibrant Manager Dashboard View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold m-0" style="color: #0f172a; letter-spacing: -0.5px;">Manager Dashboard</h3>
        <small class="text-muted"><?= e($restaurant['name']) ?> · Overview for Today (<?= format_date($stats['start_date']) ?>)</small>
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

<!-- 3 Balanced Metrics Cards Row -->
<div class="row g-3 mb-4">
    <!-- Total Revenue Card -->
    <div class="col-12 col-md-4">
        <div class="card metric-card-primary h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-white-50 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.8px;">TOTAL SALES TODAY</div>
                        <div class="fs-1 fw-bold text-white my-1"><?= format_currency($stats['total_sales']) ?></div>
                    </div>
                    <span style="font-size: 2rem;">💰</span>
                </div>
                <div class="text-white-50 small mt-2">
                    <span class="badge bg-warning text-dark fw-bold rounded-pill px-2"><?= (int)$stats['total_orders'] ?> orders</span> completed today
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Collection -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card metric-card-cash h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-success text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.8px;">CASH COLLECTION</div>
                        <div class="fs-1 fw-bold text-success my-1"><?= format_currency($stats['cash_sales']) ?></div>
                    </div>
                    <span style="font-size: 2rem;">💵</span>
                </div>
                <small class="text-muted d-block mt-2">Direct physical cash receipts</small>
            </div>
        </div>
    </div>

    <!-- Online / UPI Payments -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card metric-card-upi h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-warning text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.8px;">ONLINE / UPI COLLECTION</div>
                        <div class="fs-1 fw-bold text-warning my-1"><?= format_currency($stats['online_sales']) ?></div>
                    </div>
                    <span style="font-size: 2rem;">📱</span>
                </div>
                <small class="text-muted d-block mt-2">QR codes, UPI apps & digital transfers</small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Navigation Cards Grid -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="<?= url('manager/orders') ?>" class="card h-100 text-decoration-none border-0 shadow-sm p-3 text-center transition-card" style="border-radius: 1rem; background: #ffffff;">
            <div class="fs-2 mb-1">📜</div>
            <div class="fw-bold text-dark fs-6">Order History</div>
            <small class="text-muted">View all & edit orders</small>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="<?= url('manager/items') ?>" class="card h-100 text-decoration-none border-0 shadow-sm p-3 text-center transition-card" style="border-radius: 1rem; background: #ffffff;">
            <div class="fs-2 mb-1">🍗</div>
            <div class="fw-bold text-dark fs-6">Menu & Pricing</div>
            <small class="text-muted">Portions & items</small>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="<?= url('manager/reports') ?>" class="card h-100 text-decoration-none border-0 shadow-sm p-3 text-center transition-card" style="border-radius: 1rem; background: #ffffff;">
            <div class="fs-2 mb-1">📊</div>
            <div class="fw-bold text-dark fs-6">Financial Reports</div>
            <small class="text-muted">Periodic sales analytics</small>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="<?= url('manager/users') ?>" class="card h-100 text-decoration-none border-0 shadow-sm p-3 text-center transition-card" style="border-radius: 1rem; background: #ffffff;">
            <div class="fs-2 mb-1">👥</div>
            <div class="fw-bold text-dark fs-6">Staff Accounts</div>
            <small class="text-muted">Cashiers & Managers</small>
        </a>
    </div>
</div>

<!-- Recent Orders Section -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 1.15rem; overflow: hidden;">
    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0" style="color: #0f172a;">Recent Orders Today</h5>
        <a href="<?= url('manager/orders') ?>" class="btn btn-sm btn-outline-dark fw-bold px-3" style="border-radius: 0.5rem;">View All Orders</a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($recentOrders)): ?>
            <div class="text-center py-5 text-muted">
                <div class="fs-3 mb-2">🍽️</div>
                <div>No orders recorded yet today.</div>
            </div>
        <?php else: ?>
            <!-- Desktop Table View (>= 768px) -->
            <div class="table-responsive desktop-table-view d-none d-md-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-3">ORDER #</th>
                            <th class="py-3">TIME</th>
                            <th class="py-3">CUSTOMER</th>
                            <th class="py-3">ITEMS</th>
                            <th class="py-3">TOTAL</th>
                            <th class="py-3">PAYMENT</th>
                            <th class="py-3">BILLED BY</th>
                            <th class="py-3 text-end px-3">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td class="px-3">
                                    <span class="badge bg-dark rounded-pill px-3 py-2 fs-6">#<?= (int)$order['order_number'] ?></span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= date('h:i A', strtotime($order['created_at'])) ?></div>
                                    <small class="text-muted"><?= date('d M Y', strtotime($order['created_at'])) ?></small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= e($order['customer_name'] ?? 'Walk-in') ?></div>
                                    <?php if (!empty($order['customer_phone'])): ?>
                                        <small class="text-muted"><?= e($order['customer_phone']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?= (int)($order['items_count'] ?? 1) ?> item(s)</span>
                                </td>
                                <td>
                                    <span class="fw-bold fs-6" style="color: #0f172a;"><?= format_currency((float)$order['total']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $order['payment_method'] === 'Cash' ? 'success' : 'warning text-dark' ?>">
                                        <?= e($order['payment_method']) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted"><?= e($order['created_by_username'] ?? 'Staff') ?></small>
                                </td>
                                <td class="text-end px-3">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?= url('orders/view?id=' . $order['id']) ?>" class="btn btn-sm btn-light border fw-semibold">View</a>
                                        <a href="<?= url('manager/orders/edit?id=' . $order['id']) ?>" class="btn btn-sm btn-outline-secondary fw-semibold">Edit</a>
                                        <button type="button" class="btn btn-sm btn-outline-danger fw-semibold" onclick="confirmDelete(<?= (int)$order['id'] ?>, <?= (int)$order['order_number'] ?>)">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Layout (< 768px, 100% Mobile Optimized) -->
            <div class="mobile-cards-view d-md-none p-3">
                <?php foreach ($recentOrders as $order): ?>
                    <div class="mobile-card-row">
                        <!-- Top Row: Order # + Time + Big Price -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="order-badge">#<?= (int)$order['order_number'] ?></span>
                                <span class="text-muted small fw-semibold"><?= date('h:i A', strtotime($order['created_at'])) ?></span>
                            </div>
                            <div class="fs-5 fw-bold" style="color: #0f172a;"><?= format_currency((float)$order['total']) ?></div>
                        </div>

                        <!-- Customer & Payment Info -->
                        <div class="d-flex justify-content-between align-items-center py-2 border-top border-bottom my-2" style="border-color: #f1f5f9 !important;">
                            <div>
                                <div class="fw-bold text-dark small"><?= e($order['customer_name'] ?? 'Walk-in Customer') ?></div>
                                <?php if (!empty($order['customer_phone'])): ?>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?= e($order['customer_phone']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-<?= $order['payment_method'] === 'Cash' ? 'success' : 'warning text-dark' ?>">
                                    <?= e($order['payment_method']) ?>
                                </span>
                                <span class="badge bg-light text-dark border"><?= (int)($order['items_count'] ?? 1) ?> items</span>
                            </div>
                        </div>

                        <!-- Staff & Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted">By: <strong><?= e($order['created_by_username'] ?? 'Staff') ?></strong></small>
                            <div class="d-flex gap-1">
                                <a href="<?= url('orders/view?id=' . $order['id']) ?>" class="btn btn-sm btn-light border fw-bold px-2.5 py-1">View</a>
                                <a href="<?= url('manager/orders/edit?id=' . $order['id']) ?>" class="btn btn-sm btn-outline-secondary fw-bold px-2.5 py-1">Edit</a>
                                <button type="button" class="btn btn-sm btn-outline-danger fw-bold px-2.5 py-1" onclick="confirmDelete(<?= (int)$order['id'] ?>, <?= (int)$order['order_number'] ?>)">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Manager Soft-Delete Modal -->
<div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0" style="border-radius: 1.25rem;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">⚠️ Soft-Delete Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url('manager/orders/delete') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="deleteOrderId">
                <div class="modal-body">
                    <p>Are you sure you want to soft-delete <strong id="deleteOrderNum"></strong>?</p>
                    <p class="text-muted small mb-0">The order will be hidden from reports and cashier views, but an immutable audit trail entry will be permanently recorded.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4 fw-bold">Confirm Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, num) {
    document.getElementById('deleteOrderId').value = id;
    document.getElementById('deleteOrderNum').textContent = 'Order #' + num;
    new bootstrap.Modal(document.getElementById('deleteOrderModal')).show();
}
</script>
