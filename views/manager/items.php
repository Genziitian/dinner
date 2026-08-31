<?php
/**
 * Menu Items Catalog View Template
 * Matches Android Native App UI 1:1 (Dynamic Data)
 */
declare(strict_types=1);
?>

<div class="app-container" style="padding-bottom: calc(var(--bottom-nav-height) + 5rem);">
    <!-- Top App Bar Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= url('manager/dashboard') ?>" class="top-bar-back-btn" title="Back to Dashboard">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h4 class="top-bar-title m-0">Menu Items Catalog</h4>
        </div>
        <div class="top-bar-actions">
            <a href="<?= url('manager/items') ?>" class="top-bar-icon-btn" title="Refresh">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </a>
        </div>
    </div>

    <!-- Items Catalog List (Screenshot 4) -->
    <?php if (empty($items)): ?>
        <div class="card border-0 p-5 text-center my-3" style="border-radius: 16px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🍗</div>
            <div class="fw-bold text-dark fs-6 mb-1">No items found</div>
            <div class="text-secondary small">Tap the orange '+' button below to create your first item.</div>
        </div>
    <?php else: ?>
        <div class="catalog-list-wrap">
            <?php foreach ($items as $item): ?>
                <?php
                    $itemType = $item['item_type'] ?? 'piece';
                    $emoji = match($itemType) {
                        'portion' => '🍗',
                        'piece' => '🥚',
                        'weight' => ($item['base_unit'] === 'l' || $item['base_unit'] === 'ml') ? '🥤' : '🌾',
                        default => '🍴'
                    };
                    $displayPrice = $item['display_price'] ?? '';
                    if (empty($displayPrice) && !empty($item['variants'])) {
                        $minPrice = min(array_column($item['variants'], 'price'));
                        $displayPrice = $itemType === 'portion' ? 'From ₹' . (float)$minPrice : '₹' . (float)$minPrice . ' / ' . $item['base_unit'];
                    }
                    $isActive = !empty($item['active']);
                ?>
                <div class="catalog-item-card">
                    <a href="<?= url('manager/items/edit?id=' . (int)$item['id']) ?>" class="catalog-item-left text-decoration-none" title="Edit Item & Rates">
                        <span class="catalog-item-emoji"><?= $emoji ?></span>
                        <div>
                            <div class="catalog-item-name"><?= e($item['name']) ?></div>
                            <div class="catalog-item-meta">
                                <?= strtoupper(e($itemType)) ?> · <?= e($displayPrice) ?>
                            </div>
                        </div>
                    </a>

                    <!-- Material 3 Active Toggle Switch -->
                    <label class="m3-switch" title="Toggle Active/Inactive">
                        <input 
                            type="checkbox" 
                            <?= $isActive ? 'checked' : '' ?> 
                            onchange="toggleItemStatus(<?= (int)$item['id'] ?>, this)"
                        >
                        <span class="m3-switch-slider"></span>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Floating Action Button (FAB) Bottom Right '+' -->
    <a href="<?= url('manager/items/create') ?>" class="fab-add-btn" title="Add Menu Item">
        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
    </a>
</div>

<script>
async function toggleItemStatus(itemId, checkbox) {
    const originalChecked = !checkbox.checked;
    try {
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const token = csrfMeta ? csrfMeta.getAttribute('content') : '';

        const formData = new FormData();
        formData.append('id', itemId);
        formData.append('csrf_token', token);

        const response = await fetch('<?= url('manager/items/toggle') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            checkbox.checked = originalChecked;
            alert('Failed to update status. Please try again.');
        }
    } catch (e) {
        console.error('Toggle error:', e);
        checkbox.checked = originalChecked;
        alert('Network error while updating item status.');
    }
}
</script>
