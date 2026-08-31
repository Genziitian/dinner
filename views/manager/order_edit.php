<?php
/**
 * Manager Order Edit View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold m-0">Edit Order #<?= (int)$order['order_number'] ?></h4>
        <small class="text-muted">Modify items, quantities, or customer details</small>
    </div>
    <a href="<?= url('orders/view?id=' . $order['id']) ?>" class="btn btn-outline-secondary btn-sm">Cancel</a>
</div>

<form id="editOrderForm" method="POST" action="<?= url('orders/edit') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">

    <div class="row g-3">
        <!-- Customer & Payment Details -->
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 mb-3" style="border-radius: 1rem;">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold m-0">Order Details</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <label class="form-label">Customer Name</label>
                        <input type="text" name="customer_name" class="form-control" value="<?= e($order['customer_name'] ?? '') ?>" placeholder="Customer Name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Customer Phone</label>
                        <input type="tel" name="customer_phone" class="form-control" value="<?= e($order['customer_phone'] ?? '') ?>" placeholder="Phone Number">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="Cash" <?= $order['payment_method'] === 'Cash' ? 'selected' : '' ?>>💵 Cash</option>
                            <option value="Online / UPI" <?= $order['payment_method'] !== 'Cash' ? 'selected' : '' ?>>📱 Online / UPI</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Save Changes</button>
                </div>
            </div>
        </div>

        <!-- Items Table Editor -->
        <div class="col-12 col-md-8">
            <div class="card shadow-sm border-0 mb-3" style="border-radius: 1rem;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold m-0">Order Items</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addEditItemRow()">+ Add Item</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="editItemsTable">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Item & Variant</th>
                                    <th style="width: 30%; text-align: center;">Quantity</th>
                                    <th style="width: 20%; text-align: end;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="editItemsTbody">
                                <?php foreach ($order['items'] as $idx => $it): ?>
                                    <tr data-row="<?= $idx ?>">
                                        <td>
                                            <select name="items[<?= $idx ?>][variant_id]" class="form-select form-select-sm" onchange="onItemVariantChange(this, <?= $idx ?>)" required>
                                                <?php foreach ($items as $menuItem): ?>
                                                    <optgroup label="<?= e($menuItem['name']) ?>">
                                                        <?php foreach ($menuItem['variants'] as $v): ?>
                                                            <?php 
                                                                $selected = ($it['item_id'] == $menuItem['id'] && $it['variant_name_snapshot'] === $v['variant_name']) ? 'selected' : '';
                                                            ?>
                                                            <option value="<?= (int)$v['id'] ?>" data-item-id="<?= (int)$menuItem['id'] ?>" <?= $selected ?>>
                                                                <?= e($menuItem['name']) ?> - <?= e($v['variant_name']) ?> (<?= format_currency($v['price']) ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="hidden" name="items[<?= $idx ?>][item_id]" value="<?= (int)$it['item_id'] ?>">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" step="0.05" min="0.05" name="items[<?= $idx ?>][quantity]" class="form-control form-control-sm text-center mx-auto" style="max-width: 100px;" value="<?= (float)$it['quantity'] ?>" required>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">&times;</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let nextRowIdx = <?= count($order['items']) ?>;

function onItemVariantChange(selectEl, rowIdx) {
    const opt = selectEl.options[selectEl.selectedIndex];
    const itemId = opt.getAttribute('data-item-id');
    const hidden = selectEl.closest('td').querySelector('input[type="hidden"]');
    if (hidden && itemId) {
        hidden.value = itemId;
    }
}

function addEditItemRow() {
    const tbody = document.getElementById('editItemsTbody');
    const tr = document.createElement('tr');
    tr.setAttribute('data-row', nextRowIdx);

    let optionsHtml = '';
    <?php foreach ($items as $menuItem): ?>
        optionsHtml += `<optgroup label="<?= e($menuItem['name']) ?>">`;
        <?php foreach ($menuItem['variants'] as $v): ?>
            optionsHtml += `<option value="<?= (int)$v['id'] ?>" data-item-id="<?= (int)$menuItem['id'] ?>"><?= e($menuItem['name']) ?> - <?= e($v['variant_name']) ?> (<?= format_currency($v['price']) ?>)</option>`;
        <?php endforeach; ?>
        optionsHtml += `</optgroup>`;
    <?php endforeach; ?>

    tr.innerHTML = `
        <td>
            <select name="items[${nextRowIdx}][variant_id]" class="form-select form-select-sm" onchange="onItemVariantChange(this, ${nextRowIdx})" required>
                ${optionsHtml}
            </select>
            <input type="hidden" name="items[${nextRowIdx}][item_id]" value="<?= !empty($items[0]['id']) ? (int)$items[0]['id'] : 1 ?>">
        </td>
        <td class="text-center">
            <input type="number" step="0.05" min="0.05" name="items[${nextRowIdx}][quantity]" class="form-control form-control-sm text-center mx-auto" style="max-width: 100px;" value="1" required>
        </td>
        <td class="text-end">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">&times;</button>
        </td>
    `;

    tbody.appendChild(tr);
    const sel = tr.querySelector('select');
    onItemVariantChange(sel, nextRowIdx);
    nextRowIdx++;
}
</script>
