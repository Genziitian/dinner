<?php
/**
 * Modern Vibrant Manager Menu Items List View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold m-0" style="color: #0f172a; letter-spacing: -0.5px;">Menu & Pricing Management</h4>
        <small class="text-muted">Configure portions (Full/Half/Quarter), per-piece, and weight rates</small>
    </div>
    <a href="<?= url('manager/items/create') ?>" class="btn-boxed-primary">
        <span style="font-size: 1.1rem; line-height: 1;">+</span>
        <span>Add New Item</span>
    </a>
</div>

<div class="card shadow-sm border-0 mb-4" style="border-radius: 1.15rem; overflow: hidden;">
    <div class="card-body p-0">
        <?php if (empty($items)): ?>
            <div class="text-center py-5 text-muted">
                <div class="fs-2 mb-2">🍽️</div>
                <p>No menu items found. Click "+ Add New Item" to create your first dish.</p>
            </div>
        <?php else: ?>
            <!-- Desktop Table View (>= 768px) -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-3">ITEM NAME</th>
                            <th class="py-3">PRICING MODEL</th>
                            <th class="py-3">VARIANTS & PRICING</th>
                            <th class="py-3">STATUS</th>
                            <th class="py-3 text-end px-3">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $it): ?>
                            <?php
                                $badgeClass = match($it['item_type']) {
                                    'portion' => 'badge-portion',
                                    'piece' => 'badge-piece',
                                    'weight' => 'badge-weight',
                                    default => 'badge-piece'
                                };
                                $emoji = match($it['item_type']) {
                                    'portion' => '🍗',
                                    'piece' => '🥚',
                                    'weight' => ($it['base_unit'] === 'l' || $it['base_unit'] === 'ml') ? '🥤' : '🌾',
                                    default => '🍴'
                                };
                            ?>
                            <tr>
                                <td class="px-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span style="font-size: 1.35rem;"><?= $emoji ?></span>
                                        <div>
                                            <div class="fw-bold text-dark fs-6"><?= e($it['name']) ?></div>
                                            <small class="text-muted">Base unit: <?= e($it['base_unit']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="<?= $badgeClass ?>"><?= ucfirst(e($it['item_type'])) ?> Based</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach ($it['variants'] as $v): ?>
                                    <span class="variant-chip">
                                        <strong><?= e($v['variant_name']) ?>:</strong> <?= format_currency($v['price']) ?>
                                    </span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $it['active'] ? 'success' : 'danger' ?>">
                                        <?= $it['active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="text-end px-3">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?= url('manager/items/edit?id=' . $it['id']) ?>" class="btn btn-sm btn-light border fw-semibold">Edit</a>
                                        <form action="<?= url('manager/items/toggle') ?>" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                                            <button type="submit" class="btn btn-sm <?= $it['active'] ? 'btn-outline-danger' : 'btn-outline-success' ?> fw-semibold">
                                                <?= $it['active'] ? 'Deactivate' : 'Activate' ?>
                                            </button>
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
                <?php foreach ($items as $it): ?>
                    <?php
                        $badgeClass = match($it['item_type']) {
                            'portion' => 'badge-portion',
                            'piece' => 'badge-piece',
                            'weight' => 'badge-weight',
                            default => 'badge-piece'
                        };
                        $emoji = match($it['item_type']) {
                            'portion' => '🍗',
                            'piece' => '🥚',
                            'weight' => ($it['base_unit'] === 'l' || $it['base_unit'] === 'ml') ? '🥤' : '🌾',
                            default => '🍴'
                        };
                    ?>
                    <div class="mobile-card-row">
                        <!-- Top Row: Icon + Name + Status -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size: 1.5rem;"><?= $emoji ?></span>
                                <div>
                                    <div class="fw-bold text-dark fs-6"><?= e($it['name']) ?></div>
                                    <span class="<?= $badgeClass ?>"><?= ucfirst(e($it['item_type'])) ?></span>
                                </div>
                            </div>
                            <span class="badge bg-<?= $it['active'] ? 'success' : 'danger' ?>">
                                <?= $it['active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </div>

                        <!-- Variants & Pricing Chips -->
                        <div class="p-2.5 bg-light rounded-3 my-2 border" style="border-color: #f1f5f9 !important;">
                            <div class="text-muted fw-bold mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">VARIANTS & RATES:</div>
                            <div class="d-flex flex-wrap gap-1.5">
                                <?php foreach ($it['variants'] as $v): ?>
                                    <span class="variant-chip">
                                        <strong><?= e($v['variant_name']) ?>:</strong> <?= format_currency($v['price']) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-2 pt-1">
                            <a href="<?= url('manager/items/edit?id=' . $it['id']) ?>" class="btn btn-sm btn-light border fw-bold px-3 py-1">Edit Item</a>
                            <form action="<?= url('manager/items/toggle') ?>" method="POST" class="d-inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                                <button type="submit" class="btn btn-sm <?= $it['active'] ? 'btn-outline-danger' : 'btn-outline-success' ?> fw-bold px-3 py-1">
                                    <?= $it['active'] ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
