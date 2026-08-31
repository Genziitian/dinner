<?php
/**
 * Internal Staff Receipt View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="container py-2">
    <!-- Action Bar (Hidden on Print) -->
    <div class="d-flex justify-content-between align-items-center mb-3 no-print max-w-sm mx-auto" style="max-width: 380px;">
        <a href="<?= url('cashier/order') ?>" class="btn btn-primary btn-sm">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Order
        </a>
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Receipt
        </button>
    </div>

    <!-- Thermal Print Printable Receipt -->
    <div class="receipt-wrapper">
        <div class="receipt-header">
            <div class="receipt-title"><?= e($order['restaurant_name']) ?></div>
            <?php if (!empty($order['restaurant_address'])): ?>
                <div style="font-size: 0.8rem; color: #475569;"><?= e($order['restaurant_address']) ?></div>
            <?php endif; ?>
            <?php if (!empty($order['restaurant_phone'])): ?>
                <div style="font-size: 0.8rem; color: #475569;">Tel: <?= e($order['restaurant_phone']) ?></div>
            <?php endif; ?>
            <div class="receipt-divider"></div>
            <div class="d-flex justify-content-between" style="font-size: 0.85rem;">
                <span><?= format_date($order['order_date']) ?></span>
                <span><?= format_time($order['order_time']) ?></span>
            </div>
            <div class="fw-bold fs-5 mt-1">ORDER #<?= (int)$order['order_number'] ?></div>

            <?php if (!empty($order['customer_name']) || !empty($order['customer_phone'])): ?>
                <div class="text-start mt-2" style="font-size: 0.85rem;">
                    <?php if (!empty($order['customer_name'])): ?>
                        <div><strong>Customer:</strong> <?= e($order['customer_name']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($order['customer_phone'])): ?>
                        <div><strong>Phone:</strong> <?= e($order['customer_phone']) ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Items Table -->
        <table class="receipt-table">
            <thead>
                <tr>
                    <th style="width: 55%;">Item</th>
                    <th style="width: 20%; text-align: center;">Qty</th>
                    <th style="width: 25%; text-align: right;">Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td>
                            <div><strong><?= e($item['item_name_snapshot']) ?></strong></div>
                            <?php if ($item['variant_name_snapshot'] !== 'Standard'): ?>
                                <small style="color: #64748b; font-size: 0.75rem;"><?= e($item['variant_name_snapshot']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?= ($item['quantity'] == (int)$item['quantity']) ? (int)$item['quantity'] : (float)$item['quantity'] ?>
                            <small><?= e($item['unit'] !== 'piece' ? $item['unit'] : '') ?></small>
                        </td>
                        <td style="text-align: right;"><?= format_currency($item['total_price']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="receipt-divider"></div>

        <!-- Totals & Payment -->
        <div class="receipt-total-row">
            <span>TOTAL</span>
            <span><?= format_currency($order['total']) ?></span>
        </div>

        <div class="d-flex justify-content-between" style="font-size: 0.9rem; font-weight: bold; margin-top: 0.4rem;">
            <span>PAYMENT:</span>
            <span><?= strtoupper(e($order['payment_method'])) ?></span>
        </div>

        <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem; margin-top: 0.25rem;">
            <span>Billed By:</span>
            <span><?= e($order['created_by_username']) ?></span>
        </div>

        <!-- Customer Secure QR Code -->
        <?php if (!empty($publicUrl)): ?>
            <div class="receipt-qr-box" style="background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 0.75rem; padding: 1rem; text-align: center; margin: 1rem 0;">
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.75rem; color: #475569; font-weight: 700;">
                    Scan to view e-Receipt
                </div>
                <!-- Solid White Quiet Zone Container -->
                <div style="background: #ffffff; padding: 20px; display: inline-block; border-radius: 0.75rem; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div id="receiptQr" style="width: 280px; height: 280px;"></div>
                </div>
                <div style="font-size: 0.7rem; color: #64748b; word-break: break-all; margin-top: 0.75rem; line-height: 1.3;">
                    <?= e($publicUrl) ?>
                </div>
            </div>

            <style>
            #receiptQr img, #receiptQr canvas {
                width: 280px !important;
                height: 280px !important;
                display: block;
                image-rendering: -moz-crisp-edges;
                image-rendering: -o-crisp-edges;
                image-rendering: -webkit-optimize-contrast;
                image-rendering: crisp-edges;
                image-rendering: pixelated;
                -ms-interpolation-mode: nearest-neighbor;
            }
            </style>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const payload = "<?= trim($publicUrl) ?>";
                new QRCode(document.getElementById('receiptQr'), {
                    text: payload,
                    width: 280,
                    height: 280,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            });
            </script>
        <?php endif; ?>

        <div class="text-center mt-3" style="font-size: 0.75rem; color: #64748b;">
            Thank you for dining with us!
        </div>
    </div>
</div>
