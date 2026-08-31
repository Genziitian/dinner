<?php
/**
 * Manager Order Detail View Template
 * Matches Android Native App UI 1:1 (Dynamic Data)
 */
declare(strict_types=1);
?>

<div class="app-container">
    <!-- Top App Bar Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= url('manager/orders') ?>" class="top-bar-back-btn" title="Back to Orders">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h4 class="top-bar-title m-0">Order #<?= (int)$order['order_number'] ?></h4>
        </div>
        <div class="top-bar-actions">
            <a href="<?= url('receipt/view?id=' . (int)$order['id']) ?>" class="top-bar-icon-btn orange" title="View Printable QR Receipt">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            </a>
            <button type="button" class="top-bar-icon-btn" onclick="window.print()" title="Print">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            </button>
        </div>
    </div>

    <!-- Order Detail Card -->
    <div class="card border-0 shadow-sm p-3 mb-3" style="background: #ffffff; border-radius: 18px; border: 1px solid #e2e8f0 !important;">
        <div class="d-flex justify-content-between align-items-center pb-3 border-bottom">
            <div>
                <div class="fw-bold fs-5" style="color: #0f172a;">Order #<?= (int)$order['order_number'] ?></div>
                <div class="text-secondary small"><?= format_date($order['order_date']) ?> at <?= format_time($order['order_time']) ?></div>
            </div>
            <span class="payment-pill <?= strtolower($order['payment_method']) === 'cash' ? 'cash' : 'upi' ?>" style="font-size: 0.82rem; padding: 0.35rem 0.75rem;">
                <?= strtoupper(e($order['payment_method'])) ?>
            </span>
        </div>

        <div class="py-2.5 border-bottom">
            <div class="text-secondary small">Customer</div>
            <div class="fw-bold text-dark"><?= e(!empty($order['customer_name']) ? $order['customer_name'] : 'Walk-in Customer') ?> <?= !empty($order['customer_phone']) ? '· 📞 ' . e($order['customer_phone']) : '' ?></div>
            <div class="text-secondary small mt-1">Billed by: <strong class="text-dark"><?= e($order['created_by_username']) ?></strong></div>
        </div>

        <!-- Items Table -->
        <div class="py-3">
            <div class="text-secondary small fw-bold mb-2">ORDER ITEMS</div>
            <div class="d-flex flex-column gap-2">
                <?php foreach ($order['items'] as $item): ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;"><?= e($item['item_name_snapshot']) ?></div>
                            <div class="text-secondary small">
                                <?= e($item['variant_name_snapshot']) ?> × <?= ($item['quantity'] == (int)$item['quantity']) ? (int)$item['quantity'] : (float)$item['quantity'] ?> <?= e($item['unit'] !== 'piece' ? $item['unit'] : '') ?> @ <?= format_currency($item['unit_price']) ?>
                            </div>
                        </div>
                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                            <?= format_currency($item['total_price']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="pt-3 border-top d-flex justify-content-between align-items-center">
            <span class="fw-bold text-dark fs-6">TOTAL (<?= strtoupper(e($order['payment_method'])) ?>)</span>
            <span class="fs-4 fw-bold" style="color: var(--brand-orange);"><?= format_currency($order['total']) ?></span>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex gap-2">
        <a href="<?= url('receipt/view?id=' . (int)$order['id']) ?>" class="btn flex-fill py-2.5 fw-bold text-white shadow-sm" style="background: #0f172a; border-radius: 12px;">
            View QR Receipt
        </a>
        <a href="<?= url('manager/orders/edit?id=' . (int)$order['id']) ?>" class="btn btn-light border py-2.5 px-3 fw-bold" style="border-radius: 12px;">
            Edit Order
        </a>
    </div>
</div>
