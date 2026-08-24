<?php
/**
 * Modern Vibrant Cashier Billing Screen View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="row g-3">
    <!-- Left Column: Menu Items Catalog -->
    <div class="col-12 col-lg-8">
        <!-- Top Action Bar -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold m-0" style="letter-spacing: -0.5px; color: #0f172a;">Menu Catalog</h4>
                <small class="text-muted"><?= e($restaurant['name']) ?> · <?= count($items) ?> items available</small>
            </div>
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-dark text-white rounded-pill shadow-sm" style="font-size: 0.88rem; font-weight: 700; border: 1.5px solid #334155;">
                <span style="color: #f59e0b;">●</span>
                <span>Order #<?= (int)$previewOrderNumber ?></span>
            </div>
        </div>

        <!-- Search Bar with Embedded Icon -->
        <div style="position: relative; margin-bottom: 0.85rem;">
            <svg style="position: absolute; left: 14px; top: 12px; color: #94a3b8; pointer-events: none;" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="itemSearch" class="form-control" style="padding-left: 2.6rem; border-radius: 0.75rem; border: 1.5px solid #e2e8f0; font-size: 0.95rem;" placeholder="Search menu item (e.g. Curry, Egg, Rice)..." oninput="searchItems(this.value)">
        </div>

        <!-- Category / Type Filters -->
        <div class="category-filter">
            <button type="button" class="active" onclick="filterItems('all', this)">
                <span>🍽️</span> All Items
            </button>
            <button type="button" onclick="filterItems('portion', this)">
                <span>🍗</span> Portion Meals
            </button>
            <button type="button" onclick="filterItems('piece', this)">
                <span>🥚</span> Per Piece
            </button>
            <button type="button" onclick="filterItems('weight', this)">
                <span>🌾</span> By Weight / Volume
            </button>
        </div>

        <!-- Items Grid -->
        <div class="item-grid" id="itemsGrid">
            <?php if (empty($items)): ?>
                <div class="col-12 text-center py-5 text-muted">
                    <p>No active items found in the menu.</p>
                </div>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <?php
                        $badgeClass = match($item['item_type']) {
                            'portion' => 'badge-portion',
                            'piece' => 'badge-piece',
                            'weight' => 'badge-weight',
                            default => 'badge-piece'
                        };
                        $emoji = match($item['item_type']) {
                            'portion' => '🍗',
                            'piece' => '🥚',
                            'weight' => ($item['base_unit'] === 'l' || $item['base_unit'] === 'ml') ? '🥤' : '🌾',
                            default => '🍴'
                        };
                    ?>
                    <div 
                        class="item-card" 
                        data-type="<?= e($item['item_type']) ?>" 
                        data-name="<?= e(strtolower($item['name'])) ?>"
                        data-item='<?= json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'
                    >
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span style="font-size: 1.35rem;"><?= $emoji ?></span>
                                <span class="<?= $badgeClass ?>"><?= e($item['item_type']) ?></span>
                            </div>
                            <div class="item-name"><?= e($item['name']) ?></div>
                        </div>
                        <div class="item-meta">
                            <div class="item-price" style="color: #0f172a;"><?= e($item['display_price']) ?></div>
                            <span class="d-inline-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 26px; height: 26px; border-radius: 50%; background: #ea580c; color: #ffffff; font-size: 0.95rem;">+</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Current Order / Cart Panel -->
    <div class="col-12 col-lg-4" id="cartSection">
        <div class="cart-panel sticky-top" style="top: 80px;">
            <div class="cart-header">
                <div>
                    <h5 class="fw-bold m-0" style="color: #0f172a;">Current Order</h5>
                    <small class="text-muted">Order #<?= (int)$previewOrderNumber ?></small>
                </div>
                <span class="badge bg-dark rounded-pill px-3 py-1 fs-6" id="cartCountBadge">0</span>
            </div>

            <!-- Optional Customer Details -->
            <div class="p-3 bg-light border-bottom">
                <div class="row g-2">
                    <div class="col-6">
                        <input type="text" id="customerName" class="form-control form-control-sm" placeholder="Customer (Optional)">
                    </div>
                    <div class="col-6">
                        <input type="tel" id="customerPhone" class="form-control form-control-sm" placeholder="Phone (Optional)">
                    </div>
                </div>
            </div>

            <!-- Cart Items List -->
            <div class="cart-items-list" id="cartItemsList">
                <!-- Dynamically populated by billing.js -->
            </div>

            <!-- Cart Footer & Checkout -->
            <div class="p-3 border-top bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted fw-bold" style="font-size: 0.85rem;">TOTAL AMOUNT:</span>
                    <span class="fs-4 fw-bold" style="color: #0f172a;" id="cartTotal">₹0.00</span>
                </div>

                <!-- Payment Method Selector (Cash vs Online/UPI) -->
                <label class="form-label text-muted small fw-bold" style="letter-spacing: 0.5px;">SELECT PAYMENT METHOD</label>
                <div class="payment-methods-grid" style="grid-template-columns: repeat(2, 1fr);">
                    <button type="button" class="payment-btn active" data-payment="Cash" style="padding: 0.75rem 0.5rem; font-size: 0.95rem;">💵 Cash</button>
                    <button type="button" class="payment-btn" data-payment="Online / UPI" style="padding: 0.75rem 0.5rem; font-size: 0.95rem;">📱 Online / UPI</button>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light" id="clearCartBtn" title="Clear Cart" style="border-radius: 0.75rem;">Clear</button>
                    <button type="button" class="btn btn-save-order flex-grow-1" id="saveOrderBtn" disabled>
                        <span>Save & Bill Order</span>
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Reusable Variant / Quantity Selection Modal -->
<div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0" style="border-radius: 1.25rem;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="variantModalTitle">Select Option</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="variantModalBody">
                <!-- Injected dynamically by billing.js -->
            </div>
        </div>
    </div>
</div>

<script>
function filterItems(type, btn) {
    document.querySelectorAll('.category-filter button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const cards = document.querySelectorAll('.item-card');
    cards.forEach(card => {
        if (type === 'all' || card.getAttribute('data-type') === type) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

function searchItems(query) {
    query = query.toLowerCase().trim();
    const cards = document.querySelectorAll('.item-card');
    cards.forEach(card => {
        const name = card.getAttribute('data-name') || '';
        if (!query || name.includes(query)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

function scrollToCart() {
    const el = document.getElementById('cartSection');
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>

<script src="<?= asset('js/billing.js?v=' . filemtime(ROOT_PATH . '/public/assets/js/billing.js')) ?>"></script>
