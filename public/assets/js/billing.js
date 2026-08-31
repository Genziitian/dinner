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

  // Cache DOM elements
  let cartTotalTextEl, cartItemsSubEl, viewCartActionBtnEl;
  let mobileCartListEl, mobileCartTotalModalEl, saveOrderBtnMobileEl;
  let customerNameMobileInput, customerPhoneMobileInput;
  let variantModal, variantModalTitle, variantModalBody;
  let mobileCartModal;

  document.addEventListener('DOMContentLoaded', () => {
    cartTotalTextEl = document.getElementById('cartTotalText');
    cartItemsSubEl = document.getElementById('cartItemsSub');
    viewCartActionBtnEl = document.getElementById('viewCartActionBtn');
    
    mobileCartListEl = document.getElementById('mobileCartList');
    mobileCartTotalModalEl = document.getElementById('mobileCartTotalModal');
    saveOrderBtnMobileEl = document.getElementById('saveOrderBtnMobile');
    
    customerNameMobileInput = document.getElementById('customerNameMobile');
    customerPhoneMobileInput = document.getElementById('customerPhoneMobile');

    const modalEl = document.getElementById('variantModal');
    if (modalEl) {
      variantModal = new bootstrap.Modal(modalEl);
      variantModalTitle = document.getElementById('variantModalTitle');
      variantModalBody = document.getElementById('variantModalBody');
    }

    const cartModalEl = document.getElementById('mobileCartModal');
    if (cartModalEl) {
      mobileCartModal = new bootstrap.Modal(cartModalEl);
    }

    initItemClickListeners();
    renderCart();
  });

  function initItemClickListeners() {
    document.querySelectorAll('.pos-item-card').forEach((card) => {
      card.addEventListener('click', (e) => {
        const itemData = JSON.parse(card.getAttribute('data-item') || '{}');
        handleItemClick(itemData);
      });
    });
  }

  function handleItemClick(item) {
    currentActiveItem = item;
    const variants = item.variants || [];

    if (item.item_type === 'portion') {
      showPortionModal(item, variants);
    } else if (item.item_type === 'piece') {
      showPieceModal(item, variants[0] || { id: 0, price: 0 });
    } else if (item.item_type === 'weight') {
      showWeightModal(item, variants[0] || { id: 0, price: 0, quantity_unit: item.base_unit });
    } else {
      if (variants.length > 0) {
        addToCart(item, variants[0], 1);
      }
    }
  }

  function showPortionModal(item, variants) {
    if (!variantModal) return;
    variantModalTitle.innerHTML = `<span style="color: #ea580c;">🍗</span> ${escapeHtml(item.name)} — Select Portion`;

    let html = `<div class="portion-grid-android mb-3">`;
    variants.forEach((v) => {
      const portionIcon = v.variant_name.toLowerCase().includes('full') ? '🍲' : (v.variant_name.toLowerCase().includes('half') ? '🥣' : '🍽️');
      html += `
        <div class="portion-card-btn" onclick="DineBilling.selectPortion(${v.id})">
          <div style="font-size: 1.5rem; margin-bottom: 0.25rem;">${portionIcon}</div>
          <div style="font-weight: 800; font-size: 1rem; color: #0f172a;">${escapeHtml(v.variant_name)}</div>
          <div style="color: #ea580c; font-weight: 900; font-size: 1.1rem; margin-top: 0.25rem;">₹${parseFloat(v.price).toFixed(2)}</div>
        </div>
      `;
    });
    html += `</div>`;

    variantModalBody.innerHTML = html;
    variantModal.show();
  }

  function showPieceModal(item, variant) {
    if (!variantModal) return;
    variantModalTitle.innerHTML = `<span style="color: #b45309;">🥚</span> ${escapeHtml(item.name)} — Quantity`;
    const price = parseFloat(variant.price || 0).toFixed(2);

    variantModalBody.innerHTML = `
      <div class="text-center py-2">
        <div class="fs-4 fw-bold mb-1" style="color: #0f172a;">₹${price} <span class="text-secondary fs-6">/ piece</span></div>
        <div class="text-secondary small mb-3">Select quantity to add</div>
        <div class="d-flex align-items-center justify-content-center gap-3 my-3">
          <button type="button" class="btn btn-light rounded-circle fw-bold shadow-sm" style="width: 46px; height: 46px; font-size: 1.3rem; border: 1.5px solid #e2e8f0;" onclick="DineBilling.adjustModalQty(-1)">−</button>
          <span id="modalPieceQty" class="fs-3 fw-bold px-2" style="min-width: 45px;">1</span>
          <button type="button" class="btn rounded-circle fw-bold text-white shadow-sm" style="width: 46px; height: 46px; font-size: 1.3rem; background: var(--brand-orange);" onclick="DineBilling.adjustModalQty(1)">+</button>
        </div>
        <button type="button" class="btn w-100 py-2.5 fw-bold text-white shadow-sm mt-3" style="background: var(--brand-orange); border-radius: 12px;" onclick="DineBilling.confirmPieceAdd(${variant.id})">Add to Order</button>
      </div>
    `;
    variantModal.show();
  }

  function showWeightModal(item, variant) {
    if (!variantModal) return;
    const unit = variant.quantity_unit || item.base_unit || 'kg';
    const price = parseFloat(variant.price || 0).toFixed(2);
    variantModalTitle.innerHTML = `<span style="color: #047857;">🌾</span> ${escapeHtml(item.name)} — By Weight`;

    variantModalBody.innerHTML = `
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
        <button type="button" class="btn w-100 py-2.5 fw-bold text-white shadow-sm" style="background: var(--brand-orange); border-radius: 12px;" onclick="DineBilling.confirmWeightAdd(${variant.id})">Add to Order</button>
      </div>
    `;
    variantModal.show();
  }

  function addToCart(item, variant, quantity) {
    quantity = parseFloat(quantity);
    if (quantity <= 0) return;

    const unitPrice = parseFloat(variant.price);
    const existingIndex = cart.findIndex(c => c.item_id === item.id && c.variant_id === variant.id);

    if (existingIndex > -1) {
      cart[existingIndex].quantity += quantity;
      cart[existingIndex].total_price = Math.round(cart[existingIndex].quantity * unitPrice * 100) / 100;
    } else {
      cart.push({
        item_id: item.id,
        variant_id: variant.id,
        item_name: item.name,
        variant_name: variant.variant_name,
        unit: variant.quantity_unit || item.base_unit || 'piece',
        unit_price: unitPrice,
        quantity: quantity,
        total_price: Math.round(quantity * unitPrice * 100) / 100
      });
    }

    renderCart();
  }

  function renderCart() {
    let subtotal = 0;
    let totalItems = 0;

    cart.forEach(item => {
      subtotal += item.total_price;
      totalItems += item.quantity;
    });

    const formattedTotal = '₹' + subtotal.toFixed(2);

    // Update bottom bar
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

    // Update Modal Cart
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
                <button type="button" class="btn btn-sm text-danger p-1" onclick="DineBilling.removeItem(${index})" title="Remove">✕</button>
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

    if (saveOrderBtnMobileEl) {
      saveOrderBtnMobileEl.disabled = true;
      saveOrderBtnMobileEl.innerHTML = 'Saving Order...';
    }

    const payload = {
      customer_name: customerNameMobileInput ? customerNameMobileInput.value.trim() : '',
      customer_phone: customerPhoneMobileInput ? customerPhoneMobileInput.value.trim() : '',
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
          'Accept': 'application/json',
          'X-CSRF-Token': getCSRFToken()
        },
        body: JSON.stringify(payload)
      });

      const data = await response.json();

      if (data.success && data.redirect_url) {
        cart = [];
        window.location.href = data.redirect_url;
      } else {
        alert(data.message || 'Failed to save order. Please try again.');
        if (saveOrderBtnMobileEl) {
          saveOrderBtnMobileEl.disabled = false;
          saveOrderBtnMobileEl.innerHTML = 'Save & Bill Order ➔';
        }
      }
    } catch (err) {
      console.error('Order save error:', err);
      alert('Connection error occurred while saving the order. Please check connection and try again.');
      if (saveOrderBtnMobileEl) {
        saveOrderBtnMobileEl.disabled = false;
        saveOrderBtnMobileEl.innerHTML = 'Save & Bill Order ➔';
      }
    }
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Public bridge for inline button event handlers
  window.DineBilling = {
    openMobileCart: function() {
      if (mobileCartModal) mobileCartModal.show();
    },

    setPaymentMethod: function(method) {
      selectedPayment = method;
    },

    clearCart: function() {
      if (cart.length === 0) return;
      if (confirm('Are you sure you want to clear the current cart?')) {
        cart = [];
        renderCart();
      }
    },

    saveOrder: saveOrder,

    selectPortion: function(variantId) {
      if (!currentActiveItem) return;
      const variant = (currentActiveItem.variants || []).find(v => v.id === variantId);
      if (variant) {
        addToCart(currentActiveItem, variant, 1);
      }
      if (variantModal) variantModal.hide();
    },

    adjustModalQty: function(delta) {
      const qtyEl = document.getElementById('modalPieceQty');
      if (!qtyEl) return;
      let val = parseInt(qtyEl.textContent, 10) + delta;
      if (val < 1) val = 1;
      qtyEl.textContent = String(val);
    },

    confirmPieceAdd: function(variantId) {
      const qtyEl = document.getElementById('modalPieceQty');
      const qty = qtyEl ? parseInt(qtyEl.textContent, 10) : 1;
      const variant = (currentActiveItem.variants || []).find(v => v.id === variantId) || (currentActiveItem.variants[0]);
      if (currentActiveItem && variant) {
        addToCart(currentActiveItem, variant, qty);
      }
      if (variantModal) variantModal.hide();
    },

    setWeightVal: function(val) {
      const input = document.getElementById('modalWeightInput');
      if (input) input.value = String(val);
    },

    confirmWeightAdd: function(variantId) {
      const input = document.getElementById('modalWeightInput');
      const qty = input ? parseFloat(input.value) : 1.0;
      if (isNaN(qty) || qty <= 0) {
        alert('Please enter a valid weight quantity.');
        return;
      }
      const variant = (currentActiveItem.variants || []).find(v => v.id === variantId) || (currentActiveItem.variants[0]);
      if (currentActiveItem && variant) {
        addToCart(currentActiveItem, variant, qty);
      }
      if (variantModal) variantModal.hide();
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
      if (!cart[index]) return;
      cart.splice(index, 1);
      renderCart();
    }
  };
})();
