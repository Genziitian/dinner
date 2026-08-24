<?php
/**
 * Manager Order Detail View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold m-0">Order #<?= (int)$order['order_number'] ?> Details</h4>
        <small class="text-muted">Recorded on <?= format_date($order['order_date']) ?> at <?= format_time($order['order_time']) ?></small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('manager/orders') ?>" class="btn btn-outline-secondary btn-sm">← Back to Orders</a>
        <a href="<?= url('manager/orders/edit?id=' . $order['id']) ?>" class="btn btn-primary btn-sm">Edit Order</a>
        <button type="button" class="btn btn-light btn-sm border" onclick="window.print()">Print</button>
    </div>
</div>

<div class="row g-3">
    <!-- Left Column: Items Breakdown -->
    <div class="col-12 col-md-8">
        <div class="card shadow-sm border-0 mb-3" style="border-radius: 1rem;">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold m-0 text-dark">Ordered Items Snapshot</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Item & Portion</th>
                                <th class="text-center">Unit Price</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?= e($item['item_name_snapshot']) ?></div>
                                        <small class="text-muted"><?= e($item['variant_name_snapshot']) ?> (<?= e($item['unit']) ?>)</small>
                                    </td>
                                    <td class="text-center"><?= format_currency($item['unit_price']) ?></td>
                                    <td class="text-center fw-semibold">
                                        <?= ($item['quantity'] == (int)$item['quantity']) ? (int)$item['quantity'] : (float)$item['quantity'] ?>
                                    </td>
                                    <td class="text-end fw-bold text-dark"><?= format_currency($item['total_price']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="3" class="fw-bold text-end">Subtotal:</td>
                                <td class="fw-bold text-end"><?= format_currency($order['subtotal']) ?></td>
                            </tr>
                            <tr class="table-light">
                                <td colspan="3" class="fw-bold text-end fs-5">Grand Total:</td>
                                <td class="fw-bold text-end fs-5 text-primary"><?= format_currency($order['total']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Meta Details & Public QR Link -->
    <div class="col-12 col-md-4">
        <!-- Order Summary Card -->
        <div class="card shadow-sm border-0 mb-3" style="border-radius: 1rem;">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold m-0 text-dark">Order Information</h6>
            </div>
            <div class="card-body p-3">
                <div class="mb-2 pb-2 border-bottom">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-success"><?= strtoupper(e($order['status'])) ?></span>
                </div>
                <div class="mb-2 pb-2 border-bottom">
                    <small class="text-muted d-block">Payment Method</small>
                    <span class="fw-bold text-dark"><?= e($order['payment_method']) ?></span>
                </div>
                <div class="mb-2 pb-2 border-bottom">
                    <small class="text-muted d-block">Customer Details</small>
                    <div class="fw-semibold"><?= e($order['customer_name'] ?? 'Walk-in Customer') ?></div>
                    <small class="text-muted"><?= e($order['customer_phone'] ?? 'No phone provided') ?></small>
                </div>
                <div class="mb-2 pb-2 border-bottom">
                    <small class="text-muted d-block">Created By Staff</small>
                    <div class="fw-semibold"><?= e($order['created_by_username']) ?></div>
                    <small class="text-muted"><?= $order['created_at'] ?></small>
                </div>
                <div>
                    <small class="text-muted d-block">Last Updated</small>
                    <small><?= $order['updated_at'] ?></small>
                </div>
            </div>
        </div>

        <!-- Order Delete Option -->
        <div class="card shadow-sm border-0" style="border-radius: 1rem;">
            <div class="card-body p-3 text-center">
                <form action="<?= url('manager/orders/delete') ?>" method="POST" onsubmit="return confirm('Are you sure you want to soft delete Order #<?= (int)$order['order_number'] ?>?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        Soft Delete Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
