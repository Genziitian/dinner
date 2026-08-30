<?php
/**
 * Modern Vibrant Manager Menu Items List View Template (Mobile-Redesigned)
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold m-0" style="color: #0f172a; letter-spacing: -0.5px;">Menu & Pricing Management</h4>
        <small class="text-muted d-none d-sm-inline">Configure portions (Full/Half/Quarter), per-piece, and weight rates</small>
    </div>
    <a href="<?= url('manager/items/create') ?>" class="btn btn-primary btn-sm px-3 fw-bold" style="border-radius: 0.6rem;">
        <span>+ Add New Item</span>
    </a>
</div>

<div class="card shadow-sm border-0 mb-5" style="border-radius: 1.15rem; overflow: hidden; border: 1px solid #e2e8f0 !important;">
    <div class="card-body p-0">
        <?php if (empty($items)): ?>
            <div class="text-center py-5 text-muted">
                <div class="fs-2 mb-2">🍽️</div>
                <p class="mb-0">No menu items found. Click "+ Add New Item" to create your first dish.</p>
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

            <!-- Mobile App-style List View (< 768px) -->
            <div class="d-md-none">
                <div class="d-flex flex-column divide-y">
                    <?php foreach ($items as $it): ?>
                        <?php
                            $emoji = match($it['item_type']) {
                                'portion' => '🍗',
                                'piece' => '🥚',
                                'weight' => ($it['base_unit'] === 'l' || $it['base_unit'] === 'ml') ? '🥤' : '🌾',
                                default => '🍴'
                            };
                            $badgeColor = match($it['item_type']) {
                                'portion' => '#ea580c',
                                'piece' => '#eab308',
                                'weight' => '#10b981',
                                default => '#64748b'
                            };
                        ?>
                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-white" style="transition: background-color 0.15s ease;">
                            <div class="d-flex align-items-center gap-3" style="min-width: 0; flex: 1;">
                                <!-- Circular Avatar/Emoji Box -->
                                <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 44px; height: 44px; background-color: #f1f5f9; font-size: 1.3rem; flex-shrink: 0; border: 1px solid #e2e8f0;">
                                    <?= $emoji ?>
                                </div>
                                <div style="min-width: 0; flex: 1;">
                                    <!-- Name + Type Badge -->
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="fw-bold text-dark fs-6" style="letter-spacing: -0.3px;"><?= e($it['name']) ?></span>
                                        <span class="badge" style="background-color: <?= $badgeColor ?>18; color: <?= $badgeColor ?>; font-size: 0.65rem; font-weight: 700; border: 1px solid <?= $badgeColor ?>30;">
                                            <?= strtoupper(e($it['item_type'])) ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Mini Variant Rates List -->
                                    <div class="d-flex flex-wrap align-items-center gap-1.5 mt-1 text-muted" style="font-size: 0.75rem;">
                                        <?php $first = true; foreach ($it['variants'] as $v): ?>
                                            <?php if (!$first): ?><span class="text-muted-50">•</span><?php endif; ?>
                                            <span>
                                                <span class="fw-semibold text-secondary"><?= e($v['variant_name']) ?></span>: 
                                                <strong class="text-dark"><?= format_currency($v['price']) ?></strong>
                                            </span>
                                            <?php $first = false; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sleek App-style Action Buttons -->
                            <div class="d-flex align-items-center gap-1 ms-2" style="flex-shrink: 0;">
                                <a href="<?= url('manager/items/edit?id=' . $it['id']) ?>" class="btn btn-light border btn-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 0.5rem; padding: 0;" title="Edit Item">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #475569;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <form action="<?= url('manager/items/toggle') ?>" method="POST" class="m-0">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                                    <button type="submit" class="btn btn-light border btn-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 0.5rem; padding: 0;" title="<?= $it['active'] ? 'Deactivate' : 'Activate' ?>">
                                        <?php if ($it['active']): ?>
                                            <!-- Eye/Active (Green) -> Click to hide -->
                                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #10b981;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <?php else: ?>
                                            <!-- Eye-Slash/Inactive (Red) -> Click to show -->
                                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #ef4444;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                        <?php endif; ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
