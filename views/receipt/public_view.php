<?php
/**
 * Public Customer Receipt View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= asset('icons/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= asset('icons/favicon-16x16.png') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= asset('icons/icon.svg') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= asset('icons/apple-touch-icon.png') ?>">
    <link rel="shortcut icon" href="<?= asset('icons/favicon.ico') ?>">
    <title>Receipt #<?= (int)$order['order_number'] ?> - <?= e($order['restaurant_name']) ?></title>
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="bg-light py-4 px-2">

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

    <!-- Items List -->
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

    <!-- Total -->
    <div class="receipt-total-row">
        <span>TOTAL</span>
        <span><?= format_currency($order['total']) ?></span>
    </div>

    <div class="d-flex justify-content-between" style="font-size: 0.9rem; font-weight: bold; margin-top: 0.4rem;">
        <span>PAYMENT RECORDED:</span>
        <span><?= strtoupper(e($order['payment_method'])) ?></span>
    </div>

    <div class="text-center mt-4 pt-3 border-top" style="font-size: 0.8rem; color: #64748b;">
        <p class="m-0">Official Digital Receipt</p>
        <small>Thank you for your visit!</small>
    </div>

    <div class="mt-4 no-print text-center">
        <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="window.print()">
            Print / Save Receipt
        </button>
    </div>
</div>

</body>
</html>
