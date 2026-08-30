/**
 * Cashier Billing & Cart Engine
 * Restaurant Billing & Order Management System
 */
(function() {
  'use strict';

  // In-memory state for unsaved cart
  let cart = [];
  let selectedPayment = 'Cash';
  let currentActiveItem = null;

  // Cache DOM elements
  let cartItemsListEl, cartTotalEl, cartCountBadgeEl, paymentBtns, saveOrderBtn, clearCartBtn;
  let customerNameInput, customerPhoneInput;
  let variantModal, variantModalTitle, variantModalBody;

  document.addEventListener('DOMContentLoaded', () => {
    cartItemsListEl = document.getElementById('cartItemsList');
    cartTotalEl = document.getElementById('cartTotal');
    cartCountBadgeEl = document.getElementById('cartCountBadge');
    paymentBtns = document.querySelectorAll('.payment-btn');
    saveOrderBtn = document.getElementById('saveOrderBtn');
    clearCartBtn = document.getElementById('clearCartBtn');
    customerNameInput = document.getElementById('customerName');
    customerPhoneInput = document.getElementById('customerPhone');

    const modalEl = document.getElementById('variantModal');
    if (modalEl) {
      variantModal = new bootstrap.Modal(modalEl);
      variantModalTitle = document.getElementById('variantModalTitle');
      variantModalBody = document.getElementById('variantModalBody');
    }

    initItemClickListeners();
    initPaymentSelectors();
    initCartActions();
    renderCart();
  });

  function initItemClickListeners() {
    document.querySelectorAll('.item-card').forEach((card) => {
      card.addEventListener('click', () => {
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
      // Direct single variant add
      if (variants.length > 0) {
        addToCart(item, variants[0], 1);
      }
    }
  }

  function showPortionModal(item, variants) {
    if (!variantModal) return;
    variantModalTitle.innerHTML = `<span style="color: #ea580c;">🍗</span> ${escapeHtml(item.name)} — Select Portion`;

    let html = `<div class="portion-grid mb-3">`;
    variants.forEach((v) => {
      const portionIcon = v.variant_name.toLowerCase().includes('full') ? '🍲' : (v.variant_name.toLowerCase().includes('half') ? '🥣' : '🍽️');
      html += `
        <div class="portion-btn" onclick="DineBilling.selectPortion(${v.id})" style="border: 2px solid #fed7aa; background: #fffaf5; border-radius: 1rem; padding: 1.1rem 0.5rem;">
          <div style="font-size: 1.5rem; margin-bottom: 0.25rem;">${portionIcon}</div>
          <div class="portion-name" style="font-weight: 800; font-size: 1.05rem; color: #0f172a;">${escapeHtml(v.variant_name)}</div>
          <div class="portion-price" style="color: #ea580c; font-weight: 900; font-size: 1.15rem; margin-top: 0.25rem;">₹${parseFloat(v.price).toFixed(2)}</div>
        </div>
      `;
    });
    html += `</div>`;

    variantModalBody.innerHTML = html;
    variantModal.show();
  }

  function showPieceModal(item, variant) {
    if (!variantModal) return;
    variantModalTitle.innerHTML = `<span style="color: #eab308;">🥚</span> ${escapeHtml(item.name)} — Quantity`;

    const price = parseFloat(variant.price || 0).toFixed(2);

    variantModalBody.innerHTML = `
      <div class="text-center py-2">
        <div class="fs-4 fw-bold mb-1" style="color: #0f172a;">₹${price} <span class="text-muted fs-6">/ piece</span></div>
        <div class="text-muted small mb-3">Select quantity to add</div>
        <div class="d-flex align-items-center justify-content-center gap-3 my-3">
          <button type="button" class="btn btn-light rounded-circle fw-bold shadow-sm" style="width: 48px; height: 48px; font-size: 1.4rem; border: 1.5px solid #e2e8f0;" onclick="DineBilling.adjustModalQty(-1)">−</button>
          <span id="modalPieceQty" class="fs-3 fw-bold px-2" style="min-width: 45px;">1</span>
          <button type="button" class="btn rounded-circle fw-bold text-white shadow-sm" style="width: 48px; height: 48px; font-size: 1.4rem; background: var(--brand-orange);" onclick="DineBilling.adjustModalQty(1)">+</button>
        </div>
        <button type="button" class="btn btn-save-order w-100 py-2 mt-3" onclick="DineBilling.confirmPieceAdd(${variant.id})">Add to Order</button>
      </div>
    `;
    variantModal.show();
  }

  function showWeightModal(item, variant) {
    if (!variantModal) return;
    const unit = variant.quantity_unit || item.base_unit || 'kg';
    const price = parseFloat(variant.price || 0).toFixed(2);
    variantModalTitle.innerHTML = `<span style="color: #10b981;">🌾</span> ${escapeHtml(item.name)} — By Weight`;

    variantModalBody.innerHTML = `
      <div class="py-2">
        <div class="d-flex justify-content-between align-items-center p-3 mb-3 bg-light rounded-3">
          <span class="fw-semibold text-muted">Authoritative Rate:</span>
          <span class="fs-5 fw-bold" style="color: #0f172a;">₹${price} / ${unit}</span>
        </div>
        <label class="form-label fw-bold small text-muted">ENTER QUANTITY IN ${unit.toUpperCase()}:</label>
        <input type="number" id="modalWeightInput" class="form-control form-control-lg text-center fw-bold mb-2" value="1" step="0.05" min="0.05" style="border: 2px solid #10b981; font-size: 1.5rem;">
        <div class="preset-badges justify-content-center mb-3">
          <span class="preset-badge" onclick="DineBilling.setWeightVal(0.25)">0.25 ${unit}</span>
          <span class="preset-badge" onclick="DineBilling.setWeightVal(0.5)">0.5 ${unit}</span>
          <span class="preset-badge" onclick="DineBilling.setWeightVal(1.0)">1.0 ${unit}</span>
          <span class="preset-badge" onclick="DineBilling.setWeightVal(2.0)">2.0 ${unit}</span>
        </div>
        <button type="button" class="btn btn-save-order w-100 py-2" onclick="DineBilling.confirmWeightAdd(${variant.id})">Add to Order</button>
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
    const listDesktop = document.getElementById('cartItemsList');
    const listMobile = document.getElementById('cartItemsListMobile');
    const banner = document.getElementById('mobileCartBanner');

    if (cart.length === 0) {
      const emptyHtml = `
        <div class="text-center text-muted py-4">
          <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="mb-2 opacity-50"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
          <div>Cart is empty</div>
          <small>Tap items to add to this order</small>
        </div>
      `;
      if (listDesktop) listDesktop.innerHTML = emptyHtml;
      if (listMobile) listMobile.innerHTML = emptyHtml;

      if (cartTotalEl) cartTotalEl.textContent = '₹0.00';
      const cartTotalMobile = document.getElementById('cartTotalMobile');
      if (cartTotalMobile) cartTotalMobile.textContent = '₹0.00';

      if (cartCountBadgeEl) cartCountBadgeEl.textContent = '0';

      if (saveOrderBtn) saveOrderBtn.disabled = true;
      const saveOrderBtnMobile = document.getElementById('saveOrderBtnMobile');
      if (saveOrderBtnMobile) saveOrderBtnMobile.disabled = true;

      if (banner) banner.style.display = 'none';
      return;
    }

    let subtotal = 0;
    let totalItems = 0;
    let html = '';

    cart.forEach((item, index) => {
      subtotal += item.total_price;
      totalItems += item.quantity;
      const formattedQty = (item.quantity % 1 === 0) ? item.quantity : item.quantity.toFixed(2);

      html += `
        <div class="cart-item-row">
          <div class="cart-item-info">
            <div class="cart-item-title">${escapeHtml(item.item_name)}</div>
            <div class="cart-item-variant">${escapeHtml(item.variant_name)} · ₹${item.unit_price.toFixed(2)}</div>
          </div>
          <div class="cart-item-qty-controls">
            <button type="button" class="qty-btn" onclick="DineBilling.updateQty(${index}, -1)">-</button>
            <span class="qty-text">${formattedQty}</span>
            <button type="button" class="qty-btn" onclick="DineBilling.updateQty(${index}, 1)">+</button>
          </div>
          <div class="cart-item-price">₹${item.total_price.toFixed(2)}</div>
          <button type="button" class="cart-remove-btn" title="Remove" onclick="DineBilling.removeItem(${index})">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </button>
        </div>
      `;
    });

    if (listDesktop) listDesktop.innerHTML = html;
    if (listMobile) listMobile.innerHTML = html;

    const formattedTotal = `₹${subtotal.toFixed(2)}`;
    if (cartTotalEl) cartTotalEl.textContent = formattedTotal;
    const cartTotalMobile = document.getElementById('cartTotalMobile');
    if (cartTotalMobile) cartTotalMobile.textContent = formattedTotal;

    if (cartCountBadgeEl) cartCountBadgeEl.textContent = String(cart.length);

    if (saveOrderBtn) saveOrderBtn.disabled = false;
    const saveOrderBtnMobile = document.getElementById('saveOrderBtnMobile');
    if (saveOrderBtnMobile) saveOrderBtnMobile.disabled = false;

    if (banner) {
      banner.style.display = 'block';
      const mCount = document.getElementById('mobileCartCount');
      const mTotal = document.getElementById('mobileCartTotal');
      if (mCount) mCount.textContent = `${cart.length} Item${cart.length > 1 ? 's' : ''}`;
      if (mTotal) mTotal.textContent = formattedTotal;
    }
  }

  function initPaymentSelectors() {
    paymentBtns.forEach((btn) => {
      btn.addEventListener('click', () => {
        paymentBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        selectedPayment = btn.getAttribute('data-payment') || 'Cash';
        
        // Sync mobile buttons
        document.querySelectorAll('.payment-btn-mobile').forEach(b => {
          if (b.getAttribute('data-payment') === selectedPayment) {
            b.classList.add('active');
          } else {
            b.classList.remove('active');
          }
        });
      });
    });
  }

  function initCartActions() {
    if (clearCartBtn) {
      clearCartBtn.addEventListener('click', () => {
        if (cart.length === 0) return;
        if (confirm('Are you sure you want to clear the current unsaved cart?')) {
          cart = [];
          renderCart();
        }
      });
    }

    if (saveOrderBtn) {
      saveOrderBtn.addEventListener('click', () => saveOrder(false));
    }
  }

  async function saveOrder(isMobile = false) {
    if (cart.length === 0) {
      alert('Cart is empty. Please add items to save order.');
      return;
    }

    const currentSaveBtn = isMobile ? document.getElementById('saveOrderBtnMobile') : saveOrderBtn;
    const currentNameInput = isMobile ? document.getElementById('customerNameMobile') : customerNameInput;
    const currentPhoneInput = isMobile ? document.getElementById('customerPhoneMobile') : customerPhoneInput;

    if (currentSaveBtn) {
      currentSaveBtn.disabled = true;
      currentSaveBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        Saving Order...
      `;
    }

    const payload = {
      customer_name: currentNameInput ? currentNameInput.value.trim() : '',
      customer_phone: currentPhoneInput ? currentPhoneInput.value.trim() : '',
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
          'X-CSRF-Token': window.getCSRFToken()
        },
        body: JSON.stringify(payload)
      });

      const data = await response.json();

      if (data.success && data.redirect_url) {
        cart = [];
        const mobileModalEl = document.getElementById('mobileCartModal');
        if (mobileModalEl) {
          const bsModal = bootstrap.Modal.getInstance(mobileModalEl);
          if (bsModal) bsModal.hide();
        }
        window.location.href = data.redirect_url;
      } else {
        alert(data.message || 'Failed to save order. Please try again.');
        if (currentSaveBtn) {
          currentSaveBtn.disabled = false;
          currentSaveBtn.innerHTML = 'Save & Bill Order';
        }
      }
    } catch (err) {
      console.error('Order save error:', err);
      alert('Connection error occurred while saving the order. Please check connection and try again.');
      if (currentSaveBtn) {
        currentSaveBtn.disabled = false;
        currentSaveBtn.innerHTML = 'Save & Bill Order';
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
    },

    openMobileCart: function() {
      const modalEl = document.getElementById('mobileCartModal');
      if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
      }
    },

    setMobilePayment: function(method, btn) {
      document.querySelectorAll('.payment-btn-mobile').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      selectedPayment = method;
      
      // Sync desktop buttons
      document.querySelectorAll('.payment-btn').forEach(b => {
        if (b.getAttribute('data-payment') === method) {
          b.classList.add('active');
        } else {
          b.classList.remove('active');
        }
      });
    },

    clearMobileCart: function() {
      if (cart.length === 0) return;
      if (confirm('Are you sure you want to clear the current unsaved cart?')) {
        cart = [];
        renderCart();
        const modalEl = document.getElementById('mobileCartModal');
        if (modalEl) {
          const bsModal = bootstrap.Modal.getInstance(modalEl);
          if (bsModal) bsModal.hide();
        }
      }
    },

    saveMobileOrder: function() {
      saveOrder(true);
    }
  };
})();
