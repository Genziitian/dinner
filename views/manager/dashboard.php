<?php
/**
 * Modern Vibrant Manager Dashboard View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<!-- Mobile Dashboard (< 768px) -->
<div class="d-md-none">
    <!-- Compact Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold m-0" style="color: #0f172a; letter-spacing: -0.5px; font-size: 1.25rem;">Dashboard</h4>
            <small class="text-muted" style="font-size: 0.78rem;"><?= e($restaurant['name']) ?> · <?= format_date($stats['start_date']) ?></small>
        </div>
        <a href="<?= url('manager/exports') ?>" class="btn btn-sm btn-light border px-2 py-1" style="border-radius: 0.6rem;" title="Export CSV">
            <span style="font-size: 1rem;">📥</span>
        </a>
    </div>

    <!-- Compact Metrics Row: 3 cards in a horizontal strip -->
    <div class="d-flex gap-2 mb-3" style="overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none;">
        <!-- Total Sales -->
        <div style="flex: 1; min-width: 0; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 0.9rem; padding: 0.85rem 0.9rem; color: #fff;">
            <div style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.8px; opacity: 0.6; font-weight: 700;">Sales</div>
            <div class="fw-bold" style="font-size: 1.2rem; line-height: 1.3;"><?= format_currency($stats['total_sales']) ?></div>
            <div style="font-size: 0.65rem; opacity: 0.5; margin-top: 2px;"><?= (int)$stats['total_orders'] ?> orders</div>
        </div>
        <!-- Cash -->
        <div style="flex: 1; min-width: 0; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.9rem; padding: 0.85rem 0.9rem;">
            <div style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.8px; color: #16a34a; font-weight: 700;">Cash</div>
            <div class="fw-bold" style="font-size: 1.2rem; line-height: 1.3; color: #15803d;"><?= format_currency($stats['cash_sales']) ?></div>
        </div>
        <!-- Online -->
        <div style="flex: 1; min-width: 0; background: #fffbeb; border: 1px solid #fde68a; border-radius: 0.9rem; padding: 0.85rem 0.9rem;">
            <div style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.8px; color: #d97706; font-weight: 700;">Online</div>
            <div class="fw-bold" style="font-size: 1.2rem; line-height: 1.3; color: #b45309;"><?= format_currency($stats['online_sales']) ?></div>
        </div>
    </div>

    <!-- Quick Actions: icon-heavy, compact 2x2 grid -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; margin-bottom: 1rem;">
        <a href="<?= url('manager/orders') ?>" class="text-decoration-none" style="display: flex; flex-direction: column; align-items: center; gap: 0.3rem; padding: 0.7rem 0.25rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.85rem;">
            <span style="font-size: 1.35rem; line-height: 1;">📋</span>
            <span style="font-size: 0.7rem; font-weight: 600; color: #334155;">Orders</span>
        </a>
        <a href="<?= url('manager/items') ?>" class="text-decoration-none" style="display: flex; flex-direction: column; align-items: center; gap: 0.3rem; padding: 0.7rem 0.25rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.85rem;">
            <span style="font-size: 1.35rem; line-height: 1;">🍗</span>
            <span style="font-size: 0.7rem; font-weight: 600; color: #334155;">Menu</span>
        </a>
        <a href="<?= url('manager/reports') ?>" class="text-decoration-none" style="display: flex; flex-direction: column; align-items: center; gap: 0.3rem; padding: 0.7rem 0.25rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.85rem;">
            <span style="font-size: 1.35rem; line-height: 1;">📊</span>
            <span style="font-size: 0.7rem; font-weight: 600; color: #334155;">Reports</span>
        </a>
        <a href="<?= url('manager/users') ?>" class="text-decoration-none" style="display: flex; flex-direction: column; align-items: center; gap: 0.3rem; padding: 0.7rem 0.25rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.85rem;">
            <span style="font-size: 1.35rem; line-height: 1;">👥</span>
            <span style="font-size: 0.7rem; font-weight: 600; color: #334155;">Staff</span>
        </a>
    </div>

    <!-- Recent Orders -->
    <div style="margin-bottom: 5rem;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold m-0" style="font-size: 0.95rem; color: #0f172a;">Recent Orders</h6>
            <a href="<?= url('manager/orders') ?>" class="text-decoration-none" style="font-size: 0.78rem; font-weight: 600; color: var(--brand-orange);">View all →</a>
        </div>

        <?php if (empty($recentOrders)): ?>
            <div class="text-center py-4 px-2" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 0.85rem;">
                <div style="font-size: 1.5rem; margin-bottom: 0.35rem;">🍽️</div>
                <div class="text-muted" style="font-size: 0.85rem;">No orders today yet</div>
            </div>
        <?php else: ?>
            <div class="d-flex flex-column gap-2">
                <?php foreach ($recentOrders as $order): ?>
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.85rem; padding: 0.75rem 0.85rem;">
                        <!-- Row 1: Order# + Time | Amount -->
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="order-badge" style="font-size: 0.78rem !important; padding: 0.2rem 0.55rem !important;">#<?= (int)$order['order_number'] ?></span>
                                <span class="text-muted" style="font-size: 0.72rem; font-weight: 500;"><?= date('h:i A', strtotime($order['created_at'])) ?></span>
                            </div>
                            <span class="fw-bold" style="font-size: 1rem; color: #0f172a;"><?= format_currency((float)$order['total']) ?></span>
                        </div>
                        <!-- Row 2: Customer + badges + actions -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-1">
                                <span style="font-size: 0.78rem; color: #334155; font-weight: 500;"><?= e($order['customer_name'] ?? 'Walk-in') ?></span>
                                <span class="badge bg-<?= $order['payment_method'] === 'Cash' ? 'success' : 'warning text-dark' ?>" style="font-size: 0.6rem; padding: 0.15rem 0.4rem;"><?= e($order['payment_method'] === 'Cash' ? 'Cash' : 'UPI') ?></span>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="<?= url('orders/view?id=' . $order['id']) ?>" class="btn btn-sm btn-light border fw-semibold" style="font-size: 0.68rem; padding: 0.15rem 0.5rem; border-radius: 0.4rem;">View</a>
                                <a href="<?= url('manager/orders/edit?id=' . $order['id']) ?>" class="btn btn-sm btn-outline-secondary fw-semibold" style="font-size: 0.68rem; padding: 0.15rem 0.5rem; border-radius: 0.4rem;">Edit</a>
                                <button type="button" class="btn btn-sm btn-outline-danger fw-semibold" style="font-size: 0.68rem; padding: 0.15rem 0.5rem; border-radius: 0.4rem;" onclick="confirmDelete(<?= (int)$order['id'] ?>, <?= (int)$order['order_number'] ?>)">Del</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Desktop Dashboard (>= 768px) — unchanged -->
<div class="d-none d-md-block">
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
        <div class="col-12 col-md-4">
            <div class="card metric-card-primary h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-white-50 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.8px;">TOTAL SALES TODAY</div>
                            <div class="metric-value text-white my-1"><?= format_currency($stats['total_sales']) ?></div>
                        </div>
                        <span style="font-size: 1.8rem;">💰</span>
                    </div>
                    <div class="text-white-50 small mt-2">
                        <span class="badge bg-warning text-dark fw-bold rounded-pill px-2"><?= (int)$stats['total_orders'] ?> orders</span> completed today
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card metric-card-cash h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-success text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.8px;">CASH COLLECTION</div>
                            <div class="metric-value text-success my-1"><?= format_currency($stats['cash_sales']) ?></div>
                        </div>
                        <span style="font-size: 1.8rem;">💵</span>
                    </div>
                    <small class="text-muted d-block mt-2">Direct physical cash receipts</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card metric-card-upi h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-warning text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.8px;">ONLINE / UPI COLLECTION</div>
                            <div class="metric-value text-warning my-1"><?= format_currency($stats['online_sales']) ?></div>
                        </div>
                        <span style="font-size: 1.8rem;">📱</span>
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
                <div class="table-responsive">
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
            <?php endif; ?>
        </div>
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
