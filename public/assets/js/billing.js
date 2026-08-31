/**
 * Cashier POS Billing & Cart Engine
 * Matches Android Native App interactions & reactivity
 */
(function() {
  'use strict';

  // In-memory state for unsaved cart
  let cart = [];
  let selectedPayment = 'Cash';
  let currentActiveItem = null;
  let modalSelectedQty = 1;

  // Modals
  let variantModal = null;
  let mobileCartModal = null;

  function getVariantModal() {
    const modalEl = document.getElementById('variantModal');
    if (!modalEl) return null;
    if (!variantModal && typeof bootstrap !== 'undefined') {
      variantModal = new bootstrap.Modal(modalEl);
    }
    return variantModal;
  }

  function getCartModal() {
    const modalEl = document.getElementById('mobileCartModal');
    if (!modalEl) return null;
    if (!mobileCartModal && typeof bootstrap !== 'undefined') {
      mobileCartModal = new bootstrap.Modal(modalEl);
    }
    return mobileCartModal;
  }

  function init() {
    initItemClickListeners();
    renderCart();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function initItemClickListeners() {
    document.querySelectorAll('.pos-item-card').forEach((card) => {
      // Remove old listener if any and add fresh
      card.onclick = (e) => {
        e.preventDefault();
        try {
          const raw = card.getAttribute('data-item');
          if (raw) {
            const itemData = JSON.parse(raw);
            handleItemClick(itemData);
          }
        } catch (err) {
          console.error('Error parsing item data:', err);
        }
      };
    });
  }

  function handleItemClick(item) {
    if (!item) return;
    currentActiveItem = item;
    const variants = item.variants || [];

    if (item.item_type === 'portion') {
      if (variants.length > 1) {
        showPortionModal(item, variants);
      } else if (variants.length === 1) {
        addToCart(item, variants[0], 1);
      } else {
        addToCart(item, { id: 0, variant_name: 'Portion', price: item.price_per_unit || 0 }, 1);
      }
    } else if (item.item_type === 'weight') {
      const v = variants[0] || { id: 0, variant_name: 'Weight', price: item.price_per_unit || 0, quantity_unit: item.base_unit || 'kg' };
      showWeightModal(item, v);
    } else {
      // Piece or default: Instant add 1 piece!
      const v = variants[0] || { id: 0, variant_name: 'Piece', price: item.price_per_unit || 0 };
      addToCart(item, v, 1);
    }
  }

  function showPortionModal(item, variants) {
    const modal = getVariantModal();
    const titleEl = document.getElementById('variantModalTitle');
    const bodyEl = document.getElementById('variantModalBody');
    if (!modal || !titleEl || !bodyEl) return;

    titleEl.innerHTML = `<span style="color: #ea580c;">🍗</span> ${escapeHtml(item.name)} — Select Portion`;

    let html = `<div class="portion-grid-android mb-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.75rem;">`;
    variants.forEach((v) => {
      const isFull = v.variant_name.toLowerCase().includes('full');
      const isHalf = v.variant_name.toLowerCase().includes('half');
      const portionIcon = isFull ? '🍲' : (isHalf ? '🥣' : '🍽️');
      const priceVal = parseFloat(v.price || 0).toFixed(2);
      html += `
        <button type="button" class="btn text-start p-3 border rounded-3 portion-choice-btn" style="background: #ffffff; border-color: #e2e8f0; border-radius: 14px !important;" onclick="DineBilling.selectPortion(${v.id})">
          <div style="font-size: 1.6rem; margin-bottom: 0.25rem;">${portionIcon}</div>
          <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">${escapeHtml(v.variant_name)}</div>
          <div style="color: #ea580c; font-weight: 900; font-size: 1.15rem; margin-top: 0.25rem;">₹${priceVal}</div>
        </button>
      `;
    });
    html += `</div>`;

    bodyEl.innerHTML = html;
    modal.show();
  }

  function showWeightModal(item, variant) {
    const modal = getVariantModal();
    const titleEl = document.getElementById('variantModalTitle');
    const bodyEl = document.getElementById('variantModalBody');
    if (!modal || !titleEl || !bodyEl) return;

    const unit = variant.quantity_unit || item.base_unit || 'kg';
    const price = parseFloat(variant.price || item.price_per_unit || 0).toFixed(2);
    titleEl.innerHTML = `<span style="color: #047857;">🌾</span> ${escapeHtml(item.name)} — By Weight`;

    bodyEl.innerHTML = `
      <div class="py-2">
        <div class="d-flex justify-content-between align-items-center p-3 mb-3 bg-light rounded-3">
          <span class="fw-semibold text-secondary">Unit Rate:</span>
          <span class="fs-5 fw-bold" style="color: #0f172a;">₹${price} / ${unit}</span>
        </div>
        <label class="form-label fw-bold small text-secondary">ENTER QUANTITY IN ${unit.toUpperCase()}:</label>
        <input type="number" id="modalWeightInput" class="form-control form-control-lg text-center fw-bold mb-2" value="1" step="0.05" min="0.05" style="border: 2px solid #10b981; font-size: 1.4rem; border-radius: 12px;">
        <div class="d-flex gap-2 justify-content-center mb-3">
          <button type="button" class="btn btn-sm btn-light border fw-bold" style="border-radius: 20px;" onclick="DineBilling.setWeightVal(0.25)">0.25 ${unit}</button>
          <button type="button" class="btn btn-sm btn-light border fw-bold" style="border-radius: 20px;" onclick="DineBilling.setWeightVal(0.5)">0.5 ${unit}</button>
          <button type="button" class="btn btn-sm btn-light border fw-bold" style="border-radius: 20px;" onclick="DineBilling.setWeightVal(1.0)">1.0 ${unit}</button>
          <button type="button" class="btn btn-sm btn-light border fw-bold" style="border-radius: 20px;" onclick="DineBilling.setWeightVal(2.0)">2.0 ${unit}</button>
        </div>
        <button type="button" class="btn w-100 py-2.5 fw-bold text-white shadow-sm" style="background: var(--brand-orange); border-radius: 12px;" onclick="DineBilling.confirmWeightAdd(${variant.id || 0})">Add to Order</button>
      </div>
    `;
    modal.show();
  }

  function addToCart(item, variant, quantity) {
    quantity = parseFloat(quantity);
    if (quantity <= 0 || isNaN(quantity)) return;

    const unitPrice = parseFloat(variant.price || item.price_per_unit || 0);
    const variantId = variant.id || 0;
    const variantName = variant.variant_name || (item.item_type === 'portion' ? 'Regular' : (variant.quantity_unit || 'Piece'));

    const existingIndex = cart.findIndex(c => c.item_id === item.id && c.variant_id === variantId);

    if (existingIndex > -1) {
      cart[existingIndex].quantity += quantity;
      cart[existingIndex].total_price = Math.round(cart[existingIndex].quantity * unitPrice * 100) / 100;
    } else {
      cart.push({
        item_id: item.id,
        variant_id: variantId,
        item_name: item.name,
        variant_name: variantName,
        unit: variant.quantity_unit || item.base_unit || 'piece',
        unit_price: unitPrice,
        quantity: quantity,
        total_price: Math.round(quantity * unitPrice * 100) / 100
      });
    }

    renderCart();

    // Haptic / Visual bounce on bottom floating cart
    if (navigator.vibrate) navigator.vibrate(40);
    const floatingBar = document.querySelector('.pos-floating-cart-bar');
    if (floatingBar) {
      floatingBar.style.transform = 'scale(1.03)';
      setTimeout(() => { floatingBar.style.transform = 'none'; }, 150);
    }
  }

  function renderCart() {
    let subtotal = 0;
    let totalItems = 0;

    cart.forEach(item => {
      subtotal += item.total_price;
      totalItems += item.quantity;
    });

    const formattedTotal = '₹' + subtotal.toFixed(2);

    // Update bottom floating bar elements
    const cartTotalTextEl = document.getElementById('cartTotalText');
    const cartItemsSubEl = document.getElementById('cartItemsSub');
    const viewCartActionBtnEl = document.getElementById('viewCartActionBtn');

    if (cartTotalTextEl) cartTotalTextEl.textContent = formattedTotal;
    if (cartItemsSubEl) {
      cartItemsSubEl.textContent = cart.length === 0 ? 'Cart is empty' : `${cart.length} item${cart.length > 1 ? 's' : ''} in cart`;
    }
    if (viewCartActionBtnEl) {
      viewCartActionBtnEl.disabled = cart.length === 0;
      if (cart.length === 0) {
        viewCartActionBtnEl.classList.add('disabled');
      } else {
        viewCartActionBtnEl.classList.remove('disabled');
      }
    }

    // Update Modal Cart elements
    const mobileCartTotalModalEl = document.getElementById('mobileCartTotalModal');
    const saveOrderBtnMobileEl = document.getElementById('saveOrderBtnMobile');
    const mobileCartListEl = document.getElementById('mobileCartList');

    if (mobileCartTotalModalEl) mobileCartTotalModalEl.textContent = formattedTotal;
    if (saveOrderBtnMobileEl) saveOrderBtnMobileEl.disabled = cart.length === 0;

    if (mobileCartListEl) {
      if (cart.length === 0) {
        mobileCartListEl.innerHTML = `
          <div class="text-center py-4 text-secondary">
            <div style="font-size: 2.25rem; margin-bottom: 0.25rem;">🛒</div>
            <div class="fw-bold">Your cart is empty</div>
            <small>Tap items on the menu to add them.</small>
          </div>
        `;
      } else {
        let html = '';
        cart.forEach((item, index) => {
          const formattedQty = (item.quantity % 1 === 0) ? item.quantity : item.quantity.toFixed(2);
          html += `
            <div class="d-flex justify-content-between align-items-center p-2.5 mb-2 rounded-3 border bg-white" style="border-radius: 12px; border-color: #e2e8f0 !important;">
              <div style="flex: 1; min-width: 0; padding-right: 0.5rem;">
                <div class="fw-bold text-dark text-truncate" style="font-size: 0.92rem;">${escapeHtml(item.item_name)}</div>
                <div class="text-secondary small">${escapeHtml(item.variant_name)} · ₹${item.unit_price.toFixed(2)}</div>
              </div>
              <div class="d-flex align-items-center gap-2">
                <div class="d-inline-flex align-items-center border rounded-pill px-2 py-1 bg-light">
                  <button type="button" class="btn btn-sm p-0 border-0 fw-bold text-dark" style="width: 20px; line-height: 1;" onclick="DineBilling.updateQty(${index}, -1)">−</button>
                  <span class="px-2 fw-bold small" style="min-width: 24px; text-align: center;">${formattedQty}</span>
                  <button type="button" class="btn btn-sm p-0 border-0 fw-bold text-dark" style="width: 20px; line-height: 1;" onclick="DineBilling.updateQty(${index}, 1)">+</button>
                </div>
                <div class="fw-bold text-dark ps-2" style="min-width: 65px; text-align: right;">₹${item.total_price.toFixed(2)}</div>
                <button type="button" class="btn btn-sm text-danger p-1 border-0 bg-transparent" onclick="DineBilling.removeItem(${index})" title="Remove">✕</button>
              </div>
            </div>
          `;
        });
        mobileCartListEl.innerHTML = html;
      }
    }
  }

  function getCSRFToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }

  async function saveOrder() {
    if (cart.length === 0) {
      alert('Cart is empty. Please add items to save order.');
      return;
    }

    const saveBtn = document.getElementById('saveOrderBtnMobile');
    if (saveBtn) {
      saveBtn.disabled = true;
      saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving Order...';
    }

    const custName = document.getElementById('customerNameMobile');
    const custPhone = document.getElementById('customerPhoneMobile');

    const payload = {
      customer_name: custName ? custName.value.trim() : '',
      customer_phone: custPhone ? custPhone.value.trim() : '',
      payment_method: selectedPayment,
      items: cart.map(c => ({
        item_id: c.item_id,
        variant_id: c.variant_id,
        quantity: c.quantity
      }))
    };

    try {
      const response = await fetch('/api/orders', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCSRFToken(),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
      });

      const res = await response.json();

      if (response.ok && res.success) {
        cart = [];
        renderCart();
        const cartModal = getCartModal();
        if (cartModal) cartModal.hide();
        window.location.href = res.redirect_url || '/receipt/view?id=' + res.order_id;
      } else {
        alert(res.message || 'Error saving order. Please check item availability.');
        if (saveBtn) {
          saveBtn.disabled = false;
          saveBtn.innerHTML = 'Save & Bill Order ➔';
        }
      }
    } catch (err) {
      console.error('Order save error:', err);
      alert('Could not complete order. Please check network connection.');
      if (saveBtn) {
        saveBtn.disabled = false;
        saveBtn.innerHTML = 'Save & Bill Order ➔';
      }
    }
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str || '';
    return div.innerHTML;
  }

  // Global window API object
  window.DineBilling = {
    handleCardClick: function(cardEl) {
      try {
        const raw = cardEl.getAttribute('data-item');
        if (raw) {
          handleItemClick(JSON.parse(raw));
        }
      } catch (e) {
        console.error(e);
      }
    },

    selectPortion: function(variantId) {
      if (!currentActiveItem) return;
      const v = (currentActiveItem.variants || []).find(x => x.id === variantId);
      if (v) {
        addToCart(currentActiveItem, v, 1);
        const modal = getVariantModal();
        if (modal) modal.hide();
      }
    },

    setWeightVal: function(val) {
      const input = document.getElementById('modalWeightInput');
      if (input) input.value = val;
    },

    confirmWeightAdd: function(variantId) {
      if (!currentActiveItem) return;
      const input = document.getElementById('modalWeightInput');
      const qty = input ? parseFloat(input.value) : 1;
      if (isNaN(qty) || qty <= 0) {
        alert('Please enter a valid weight quantity.');
        return;
      }
      const v = (currentActiveItem.variants || []).find(x => x.id === variantId) || { id: 0, price: currentActiveItem.price_per_unit || 0 };
      addToCart(currentActiveItem, v, qty);
      const modal = getVariantModal();
      if (modal) modal.hide();
    },

    updateQty: function(index, delta) {
      if (!cart[index]) return;
      cart[index].quantity += delta;
      if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
      } else {
        cart[index].total_price = Math.round(cart[index].quantity * cart[index].unit_price * 100) / 100;
      }
      renderCart();
    },

    removeItem: function(index) {
      if (cart[index]) {
        cart.splice(index, 1);
        renderCart();
      }
    },

    clearCart: function() {
      if (cart.length > 0 && confirm('Clear all items from this order cart?')) {
        cart = [];
        renderCart();
      }
    },

    openMobileCart: function() {
      const cartModal = getCartModal();
      if (cartModal) {
        cartModal.show();
      }
    },

    setPaymentMethod: function(method) {
      selectedPayment = method;
    },

    saveOrder: saveOrder
  };
})();
