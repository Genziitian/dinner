<?php
/**
 * Manager Create / Edit Menu Item Form View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);

$itemType = $item['item_type'] ?? Item::TYPE_PIECE;
$variants = $item['variants'] ?? [];
$isEdit = !empty($isEdit);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold m-0"><?= $isEdit ? 'Edit Menu Item' : 'Add New Menu Item' ?></h4>
        <small class="text-muted">Define pricing models, portions, piece rates, or weight units</small>
    </div>
    <a href="<?= url('manager/items') ?>" class="btn btn-outline-secondary btn-sm">Cancel</a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
        <div class="card shadow-sm border-0" style="border-radius: 1rem;">
            <div class="card-body p-4">
                <form method="POST" action="<?= $isEdit ? url('manager/items/edit') : url('manager/items/create') ?>">
                    <?= csrf_field() ?>
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                    <?php endif; ?>

                    <!-- Item Name -->
                    <div class="mb-3">
                        <label class="form-label">Item Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Chicken Curry, Boiled Egg, Rice" value="<?= e($item['name'] ?? '') ?>" required>
                    </div>

                    <!-- Pricing / Unit Model Selector -->
                    <div class="mb-3">
                        <label class="form-label">Pricing / Unit Model <span class="text-danger">*</span></label>
                        <select name="item_type" id="itemTypeSelect" class="form-select" onchange="onItemTypeChanged(this.value)">
                            <option value="piece" <?= $itemType === 'piece' ? 'selected' : '' ?>>Per Piece (e.g. Egg, Boiled Egg, Roti)</option>
                            <option value="portion" <?= $itemType === 'portion' ? 'selected' : '' ?>>Portion Based (e.g. Full, Half, Quarter)</option>
                            <option value="weight" <?= $itemType === 'weight' ? 'selected' : '' ?>>Weight / Volume (e.g. kg, g, Liter, ml)</option>
                        </select>
                    </div>

                    <!-- Base Unit (For Weight/Volume) -->
                    <div class="mb-3" id="baseUnitSection" style="<?= $itemType !== 'weight' ? 'display: none;' : '' ?>">
                        <label class="form-label">Unit of Measure</label>
                        <select name="base_unit" id="baseUnitSelect" class="form-select">
                            <option value="kg" <?= ($item['base_unit'] ?? '') === 'kg' ? 'selected' : '' ?>>Kilogram (kg)</option>
                            <option value="g" <?= ($item['base_unit'] ?? '') === 'g' ? 'selected' : '' ?>>Gram (g)</option>
                            <option value="l" <?= ($item['base_unit'] ?? '') === 'l' ? 'selected' : '' ?>>Liter (L)</option>
                            <option value="ml" <?= ($item['base_unit'] ?? '') === 'ml' ? 'selected' : '' ?>>Milliliter (ml)</option>
                        </select>
                    </div>

                    <!-- Single Price Input (Used for Piece & Weight) -->
                    <div class="mb-3" id="singlePriceSection" style="<?= $itemType === 'portion' ? 'display: none;' : '' ?>">
                        <label class="form-label" id="singlePriceLabel">Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="single_price" class="form-control" placeholder="e.g. 30.00" value="<?= !empty($variants[0]['price']) ? (float)$variants[0]['price'] : '' ?>">
                    </div>

                    <!-- Portion Variants Section (Used for Portion) -->
                    <div class="mb-4" id="portionSection" style="<?= $itemType !== 'portion' ? 'display: none;' : '' ?>">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label m-0">Portions & Prices <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPortionRow()">+ Add Portion</button>
                        </div>

                        <div id="portionsContainer" class="d-flex flex-column gap-2">
                            <?php if (!empty($variants) && $itemType === 'portion'): ?>
                                <?php foreach ($variants as $idx => $v): ?>
                                    <div class="d-flex gap-2 align-items-center portion-row">
                                        <input type="text" name="variant_names[]" class="form-control" placeholder="Portion Name (e.g. Full)" value="<?= e($v['variant_name']) ?>" required>
                                        <input type="number" step="0.01" min="0" name="variant_prices[]" class="form-control" placeholder="Price (₹)" value="<?= (float)$v['price'] ?>" required>
                                        <input type="hidden" name="variant_values[]" value="<?= (float)$v['quantity_value'] ?>">
                                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.portion-row').remove()">&times;</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Default 3 portions for quick entry -->
                                <div class="d-flex gap-2 align-items-center portion-row">
                                    <input type="text" name="variant_names[]" class="form-control" placeholder="Portion Name" value="Full" required>
                                    <input type="number" step="0.01" min="0" name="variant_prices[]" class="form-control" placeholder="Price (₹)" value="120" required>
                                    <input type="hidden" name="variant_values[]" value="1.0">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.portion-row').remove()">&times;</button>
                                </div>
                                <div class="d-flex gap-2 align-items-center portion-row">
                                    <input type="text" name="variant_names[]" class="form-control" placeholder="Portion Name" value="Half" required>
                                    <input type="number" step="0.01" min="0" name="variant_prices[]" class="form-control" placeholder="Price (₹)" value="60" required>
                                    <input type="hidden" name="variant_values[]" value="0.5">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.portion-row').remove()">&times;</button>
                                </div>
                                <div class="d-flex gap-2 align-items-center portion-row">
                                    <input type="text" name="variant_names[]" class="form-control" placeholder="Portion Name" value="Quarter" required>
                                    <input type="number" step="0.01" min="0" name="variant_prices[]" class="form-control" placeholder="Price (₹)" value="30" required>
                                    <input type="hidden" name="variant_values[]" value="0.25">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.portion-row').remove()">&times;</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Active Toggle -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="active" value="1" id="itemActiveCheck" <?= (!isset($item['active']) || $item['active'] == 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="itemActiveCheck">
                            Item is Active (Visible on Cashier Billing Screen)
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
                            <?= $isEdit ? 'Save Changes' : 'Create Item' ?>
                        </button>
                        <a href="<?= url('manager/items') ?>" class="btn btn-light px-4 py-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function onItemTypeChanged(type) {
    const baseUnitSec = document.getElementById('baseUnitSection');
    const singlePriceSec = document.getElementById('singlePriceSection');
    const portionSec = document.getElementById('portionSection');
    const singlePriceLabel = document.getElementById('singlePriceLabel');

    if (type === 'portion') {
        baseUnitSec.style.display = 'none';
        singlePriceSec.style.display = 'none';
        portionSec.style.display = 'block';
    } else if (type === 'weight') {
        baseUnitSec.style.display = 'block';
        singlePriceSec.style.display = 'block';
        portionSec.style.display = 'none';
        singlePriceLabel.innerHTML = 'Price per Unit (₹) <span class="text-danger">*</span>';
    } else {
        baseUnitSec.style.display = 'none';
        singlePriceSec.style.display = 'block';
        portionSec.style.display = 'none';
        singlePriceLabel.innerHTML = 'Price per Piece (₹) <span class="text-danger">*</span>';
    }
}

function addPortionRow() {
    const container = document.getElementById('portionsContainer');
    const row = document.createElement('div');
    row.className = 'd-flex gap-2 align-items-center portion-row';
    row.innerHTML = `
        <input type="text" name="variant_names[]" class="form-control" placeholder="Portion Name" required>
        <input type="number" step="0.01" min="0" name="variant_prices[]" class="form-control" placeholder="Price (₹)" required>
        <input type="hidden" name="variant_values[]" value="1.0">
        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.portion-row').remove()">&times;</button>
    `;
    container.appendChild(row);
}
</script>
