<?php
/**
 * POS Billing / Menu Catalog View Template
 * Matches Android Native App UI 1:1 (Dynamic Data)
 */
declare(strict_types=1);
$currentUser = current_user();
$isManager = $currentUser && $currentUser['role'] === User::ROLE_MANAGER;
$backUrl = $isManager ? url('manager/dashboard') : url('cashier/summary');
?>

<div class="app-container" style="padding-bottom: calc(var(--bottom-nav-height) + 4.5rem);">
    <!-- Top App Bar Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= $backUrl ?>" class="top-bar-back-btn" title="Back">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="brand-pill-tag mb-1">
                    <span class="dot"></span>
                    <span>GI ORDER POS</span>
                </div>
                <h4 class="top-bar-title"><?= e($restaurant['name']) ?></h4>
                <div class="top-bar-subtitle" id="itemCountSub"><?= count($items) ?> items available</div>
            </div>
        </div>

        <div class="top-bar-actions">
            <div class="next-order-pill">
                <span class="amber-dot"></span>
                <span>Order #<?= (int)$previewOrderNumber ?></span>
            </div>
            <a href="<?= url('cashier/order') ?>" class="top-bar-icon-btn" title="Refresh Menu">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </a>
        </div>
    </div>

    <!-- Search Input Bar -->
    <div class="search-input-wrap">
        <svg class="search-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input 
            type="text" 
            id="itemSearch" 
            class="search-input-custom" 
            placeholder="Search menu item (e.g. Curry, Egg, Rice)..." 
            oninput="searchItems(this.value)"
        >
    </div>

    <!-- Category Filter Chips Row -->
    <div class="filter-chips-scroll">
        <button type="button" class="filter-chip-btn active" onclick="filterItems('all', this)">
            <span>🍽️</span> All Items
        </button>
        <button type="button" class="filter-chip-btn" onclick="filterItems('portion', this)">
            <span>🍗</span> Portion Meals
        </button>
        <button type="button" class="filter-chip-btn" onclick="filterItems('piece', this)">
            <span>🥚</span> Per Piece
        </button>
        <button type="button" class="filter-chip-btn" onclick="filterItems('weight', this)">
            <span>🌾</span> By Weight
        </button>
    </div>

    <!-- Menu Items 2-Column Grid -->
    <div class="pos-items-grid" id="itemsGrid">
        <?php if (empty($items)): ?>
            <div class="card border-0 p-5 text-center my-3 w-100" style="grid-column: 1 / -1; border-radius: 16px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🍽️</div>
                <div class="fw-bold text-dark fs-6">No menu items found</div>
                <div class="text-secondary small">Add items from the Menu catalog to start billing.</div>
            </div>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <?php
                    $itemType = $item['item_type'] ?? 'piece';
                    $typeClass = match($itemType) {
                        'portion' => 'portion',
                        'piece' => 'piece',
                        'weight' => 'weight',
                        default => 'piece'
                    };
                    $emoji = match($itemType) {
                        'portion' => '🍗',
                        'piece' => '🥚',
                        'weight' => ($item['base_unit'] === 'l' || $item['base_unit'] === 'ml') ? '🥤' : '🌾',
                        default => '🍴'
                    };
                ?>
                <div 
                    class="pos-item-card item-card" 
                    data-type="<?= e($item['item_type']) ?>" 
                    data-name="<?= e(strtolower($item['name'])) ?>"
                    data-item='<?= json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'
                    onclick="DineBilling.handleCardClick(this)"
                    style="cursor: pointer;"
                >
                    <div class="pos-item-top-row">
                        <span class="pos-item-emoji"><?= $emoji ?></span>
                        <span class="pos-item-type-pill <?= $typeClass ?>"><?= strtoupper(e($item['item_type'])) ?></span>
                    </div>
                    <div class="pos-item-name"><?= e($item['name']) ?></div>
                    <div class="pos-item-bottom-row">
                        <div class="pos-item-price"><?= e($item['display_price']) ?></div>
                        <button type="button" class="pos-item-add-btn" aria-label="Add item">+</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Floating Bottom Cart Bar (Screenshot 3) -->
<div class="pos-floating-cart-bar">
    <div class="pos-cart-bar-surface">
        <div class="pos-cart-bar-info" onclick="DineBilling.openMobileCart()" style="cursor: pointer;">
            <span class="pos-cart-bar-icon">🛒</span>
            <div>
                <div class="pos-cart-bar-total">Total: <span id="cartTotalText">₹0.00</span></div>
                <div class="pos-cart-bar-sub" id="cartItemsSub">Cart is empty</div>
            </div>
        </div>
        <button type="button" class="pos-cart-view-btn disabled" id="viewCartActionBtn" onclick="DineBilling.openMobileCart()" disabled>
            View Cart
        </button>
    </div>
</div>

<!-- Portion & Variant Selection Modal -->
<div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-android">
            <div class="modal-header modal-header-android">
                <h5 class="modal-title modal-title-android" id="variantModalTitle">Select Option</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" id="variantModalBody">
                <!-- Dynamically generated by billing.js -->
            </div>
        </div>
    </div>
</div>

<!-- Mobile & Desktop Slide-up Cart Modal / Checkout Drawer -->
<div class="modal fade" id="mobileCartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content modal-content-android">
            <div class="modal-header modal-header-android">
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size: 1.3rem;">🛒</span>
                    <h5 class="modal-title modal-title-android m-0">Current Order Cart</h5>
                    <span class="order-number-pill">Order #<?= (int)$previewOrderNumber ?></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-0 d-flex flex-column" style="max-height: 65vh; overflow-y: auto;">
                <!-- Optional Customer Details -->
                <div class="p-3 bg-light border-bottom">
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="text" id="customerNameMobile" class="form-control form-control-sm py-2" placeholder="Customer (Optional)" style="border-radius: 10px;">
                        </div>
                        <div class="col-6">
                            <input type="tel" id="customerPhoneMobile" class="form-control form-control-sm py-2" placeholder="Phone (Optional)" style="border-radius: 10px;">
                        </div>
                    </div>
                </div>

                <!-- Cart Items List Container -->
                <div class="p-3 flex-grow-1" id="mobileCartList" style="min-height: 120px;">
                    <!-- Dynamically populated by billing.js -->
                </div>
            </div>

            <!-- Cart Drawer Footer -->
            <div class="p-3 border-top bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-secondary fw-bold" style="font-size: 0.88rem;">TOTAL AMOUNT:</span>
                    <span class="fs-4 fw-bold" style="color: #0f172a;" id="mobileCartTotalModal">₹0.00</span>
                </div>

                <!-- Payment Method Toggle Buttons (Cash vs UPI) -->
                <div class="d-flex gap-2 mb-3">
                    <button type="button" class="btn flex-fill py-2 fw-bold text-center border payment-method-choice active" data-method="Cash" style="border-radius: 10px; background: #0f172a; color: #ffffff;" onclick="setPaymentMethod('Cash', this)">
                        💵 Cash
                    </button>
                    <button type="button" class="btn flex-fill py-2 fw-bold text-center border payment-method-choice" data-method="Online / UPI" style="border-radius: 10px; background: #ffffff; color: #0f172a;" onclick="setPaymentMethod('Online / UPI', this)">
                        📱 Online / UPI
                    </button>
                </div>

                <!-- Checkout Actions -->
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light py-2 px-3 fw-bold" style="border-radius: 12px; border: 1px solid #e2e8f0;" onclick="DineBilling.clearCart()">
                        Clear
                    </button>
                    <button type="button" class="btn flex-grow-1 py-2 fw-bold text-white shadow-sm" style="background: var(--brand-orange); border-radius: 12px; font-size: 1rem;" id="saveOrderBtnMobile" onclick="DineBilling.saveOrder()" disabled>
                        Save & Bill Order ➔
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('js/billing.js?v=' . filemtime(ROOT_PATH . '/public/assets/js/billing.js')) ?>"></script>
<script>
function filterItems(type, btn) {
    document.querySelectorAll('.filter-chip-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const cards = document.querySelectorAll('.pos-item-card');
    let visible = 0;
    cards.forEach(card => {
        if (type === 'all' || card.getAttribute('data-type') === type) {
            card.style.display = 'flex';
            visible++;
        } else {
            card.style.display = 'none';
        }
    });
    document.getElementById('itemCountSub').textContent = visible + ' items available';
}

function searchItems(query) {
    const q = query.toLowerCase().trim();
    const cards = document.querySelectorAll('.pos-item-card');
    let visible = 0;
    cards.forEach(card => {
        const name = card.getAttribute('data-name') || '';
        if (!q || name.includes(q)) {
            card.style.display = 'flex';
            visible++;
        } else {
            card.style.display = 'none';
        }
    });
    document.getElementById('itemCountSub').textContent = visible + ' items available';
}

function setPaymentMethod(method, btn) {
    document.querySelectorAll('.payment-method-choice').forEach(b => {
        b.style.background = '#ffffff';
        b.style.color = '#0f172a';
    });
    btn.style.background = '#0f172a';
    btn.style.color = '#ffffff';
    if (window.DineBilling) {
        window.DineBilling.setPaymentMethod(method);
    }
}
</script>
