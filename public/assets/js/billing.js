/**
 * DinePOS Android-Parity Reactive Billing Engine
 * Zero-Trust Pricing, Client-Side Cart, Dual Weight/Rupee Calculator & PhonePe Payment Success Animation
 */
(function () {
  'use strict';

  let cart = [];
  let currentActiveItem = null;
  let currentActiveVariant = null;
  let weightModalMode = 'weight'; // 'weight' or 'rupees'
  let selectedPayment = 'Cash';

  function getCSRFToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }

  function getVariantModal() {
    const el = document.getElementById('variantModal');
    if (!el) return null;
    if (!window.bootstrap || !window.bootstrap.Modal) return null;
    return bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
  }

  function getCartModal() {
    const el = document.getElementById('mobileCartModal');
    if (!el) return null;
    if (!window.bootstrap || !window.bootstrap.Modal) return null;
    return bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
  }

  function playSuccessChime() {
    try {
      const AudioCtx = window.AudioContext || window.webkitAudioContext;
      if (!AudioCtx) return;
      const ctx = new AudioCtx();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
      osc.frequency.setValueAtTime(880, ctx.currentTime + 0.12); // A5
      gain.gain.setValueAtTime(0.25, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start();
      osc.stop(ctx.currentTime + 0.6);
    } catch (e) {
      console.warn('Audio chime note:', e);
    }
  }

  function showPhonePeSuccessAnimation(res, onDone) {
    playSuccessChime();

    const overlay = document.getElementById('paymentSuccessOverlay');
    const amountEl = document.getElementById('successOverlayAmount');
    const badgeEl = document.getElementById('successOverlayBadge');
    const viewBtn = document.getElementById('successViewReceiptBtn');

    const totalStr = res.total_amount ? ('₹' + parseFloat(res.total_amount).toFixed(2)) : (document.getElementById('mobileCartTotalModal')?.textContent || '₹0.00');
    const orderNum = res.order_number || res.order_id || '0';
    const payMethod = res.payment_method || selectedPayment || 'CASH';

    if (amountEl) amountEl.textContent = totalStr;
    if (badgeEl) badgeEl.textContent = `Order #${orderNum} • ${payMethod.toUpperCase()}`;

    if (overlay) {
      overlay.style.display = 'flex';
    }

    let redirected = false;
    function proceed() {
      if (redirected) return;
      redirected = true;
      if (overlay) overlay.style.display = 'none';
      if (typeof onDone === 'function') onDone();
    }

    if (viewBtn) {
      viewBtn.onclick = proceed;
    }
    if (overlay) {
      overlay.onclick = proceed;
    }

    // Auto-transition after 1.8s
    setTimeout(proceed, 1800);
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
      currentActiveVariant = v;
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

    titleEl.innerHTML = `<span class="fw-bold" style="color: #0f172a;">${escapeHtml(item.name)}</span>`;

    let html = `<div class="portion-grid-android mb-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.75rem;">`;
    variants.forEach((v) => {
      const priceVal = parseFloat(v.price || 0).toFixed(2);
      html += `
        <button type="button" class="btn text-center p-3 border rounded-3 portion-card-btn" style="background: #ffffff; border-color: #fed7aa; border-radius: 14px !important;" onclick="DineBilling.selectPortion(${v.id})">
          <div style="font-weight: 800; font-size: 1.1rem; color: #0f172a;">${escapeHtml(v.variant_name)}</div>
          <div style="color: #ea580c; font-weight: 900; font-size: 1.2rem; margin-top: 0.35rem;">₹${priceVal}</div>
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

    weightModalMode = 'weight';
    const unit = variant.quantity_unit || item.base_unit || 'kg';
    const price = parseFloat(variant.price || item.price_per_unit || 0).toFixed(2);
    titleEl.innerHTML = `<span class="fw-bold" style="color: #0f172a;">${escapeHtml(item.name)}</span>`;

    bodyEl.innerHTML = `
      <div class="py-1">
        <div class="d-flex justify-content-between align-items-center p-3 mb-3 bg-light rounded-3 border">
          <span class="fw-semibold text-secondary" style="font-size: 0.9rem;">Unit Rate:</span>
          <span class="fs-5 fw-bold" style="color: #0f172a;">₹${price} / ${unit}</span>
        </div>

        <!-- Mode Toggle Segmented Button -->
        <div class="d-flex p-1 bg-light border rounded-3 mb-3" style="gap: 4px;">
          <button type="button" class="btn flex-fill py-1.5 fw-bold rounded-2 active" id="weightTabWeight" style="background: #0f172a; color: #ffffff; font-size: 0.85rem;" onclick="DineBilling.switchWeightMode('weight')">
            By Weight (${unit})
          </button>
          <button type="button" class="btn flex-fill py-1.5 fw-bold rounded-2" id="weightTabRupees" style="background: transparent; color: #64748b; font-size: 0.85rem;" onclick="DineBilling.switchWeightMode('rupees')">
            By Rupees (₹ Amount)
          </button>
        </div>

        <!-- By Weight Section -->
        <div id="weightInputSection">
          <label class="form-label fw-bold small text-secondary">ENTER QUANTITY IN ${unit.toUpperCase()}:</label>
          <input type="number" id="modalWeightInput" class="form-control form-control-lg text-center fw-bold mb-2" value="1.0" step="0.05" min="0.01" style="border: 2px solid #0f172a; font-size: 1.3rem; border-radius: 12px;" oninput="DineBilling.recalcWeightOutput()">
          <div class="d-flex gap-2 justify-content-center mb-3">
            <button type="button" class="btn btn-sm btn-light border fw-bold px-2 py-1" style="border-radius: 10px;" onclick="DineBilling.setWeightVal(0.25)">0.25 ${unit}</button>
            <button type="button" class="btn btn-sm btn-light border fw-bold px-2 py-1" style="border-radius: 10px;" onclick="DineBilling.setWeightVal(0.5)">0.5 ${unit}</button>
            <button type="button" class="btn btn-sm btn-light border fw-bold px-2 py-1" style="border-radius: 10px;" onclick="DineBilling.setWeightVal(1.0)">1.0 ${unit}</button>
            <button type="button" class="btn btn-sm btn-light border fw-bold px-2 py-1" style="border-radius: 10px;" onclick="DineBilling.setWeightVal(2.0)">2.0 ${unit}</button>
          </div>
        </div>

        <!-- By Rupees Section (Auto-Calculator) -->
        <div id="rupeesInputSection" style="display: none;">
          <label class="form-label fw-bold small text-secondary">ENTER RUPEES (₹ AMOUNT):</label>
          <input type="number" id="modalRupeesInput" class="form-control form-control-lg text-center fw-bold mb-2" placeholder="e.g. 10, 12, 50, 100" value="50" step="1" min="1" style="border: 2px solid #ea580c; font-size: 1.3rem; border-radius: 12px;" oninput="DineBilling.recalcRupeesOutput()">
          <div class="d-flex gap-2 justify-content-center mb-3">
            <button type="button" class="btn btn-sm btn-light border fw-bold px-3 py-1" style="border-radius: 10px;" onclick="DineBilling.setRupeesVal(10)">₹10</button>
            <button type="button" class="btn btn-sm btn-light border fw-bold px-3 py-1" style="border-radius: 10px;" onclick="DineBilling.setRupeesVal(20)">₹20</button>
            <button type="button" class="btn btn-sm btn-light border fw-bold px-3 py-1" style="border-radius: 10px;" onclick="DineBilling.setRupeesVal(50)">₹50</button>
            <button type="button" class="btn btn-sm btn-light border fw-bold px-3 py-1" style="border-radius: 10px;" onclick="DineBilling.setRupeesVal(100)">₹100</button>
          </div>
        </div>

        <!-- Calculated Summary Box -->
        <div class="p-2.5 rounded-3 mb-3 border" style="background: #f0fdf4; border-color: #bbf7d0 !important;" id="weightCalculationBox">
          <div class="fw-bold" style="color: #15803d; font-size: 0.95rem;" id="calcWeightSummary">Gives: 1.000 ${unit}</div>
          <div class="fw-bold text-dark small" id="calcPriceSummary">Total: ₹${price}</div>
        </div>

        <button type="button" class="btn w-100 py-2.5 fw-bold text-white shadow-sm" style="background: var(--brand-orange); border-radius: 12px; font-size: 1rem;" onclick="DineBilling.confirmWeightAdd(${variant.id || 0})">
          Add to Order
        </button>
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
        item_type: item.item_type,
        unit: variant.quantity_unit || item.base_unit || 'piece',
        unit_price: unitPrice,
        quantity: quantity,
        total_price: Math.round(quantity * unitPrice * 100) / 100
      });
    }

    renderCart();
    playTapHaptic();
  }

  function playTapHaptic() {
    if (navigator.vibrate) {
      try { navigator.vibrate(30); } catch (e) {}
    }
  }

  function renderCart() {
    const totalAmount = cart.reduce((sum, item) => sum + (parseFloat(item.total_price) || 0), 0);
    const totalCount = cart.length;

    // Update Bottom Floating Cart Bar
    const totalTextEl = document.getElementById('cartTotalText');
    const itemsSubEl = document.getElementById('cartItemsSub');
    const viewCartBtn = document.getElementById('viewCartActionBtn');
    const modalTotalEl = document.getElementById('mobileCartTotalModal');
    const saveBtn = document.getElementById('saveOrderBtnMobile');

    if (totalTextEl) totalTextEl.textContent = '₹' + totalAmount.toFixed(2);
    if (modalTotalEl) modalTotalEl.textContent = '₹' + totalAmount.toFixed(2);

    if (itemsSubEl) {
      itemsSubEl.textContent = totalCount === 0 ? 'Cart is empty' : `${totalCount} item${totalCount > 1 ? 's' : ''} in cart`;
    }

    if (viewCartBtn) {
      viewCartBtn.disabled = totalCount === 0;
      if (totalCount === 0) {
        viewCartBtn.classList.add('disabled');
      } else {
        viewCartBtn.classList.remove('disabled');
      }
    }

    if (saveBtn) {
      saveBtn.disabled = totalCount === 0;
    }

    // Render Items inside Cart Modal Drawer
    const listContainer = document.getElementById('mobileCartList');
    if (!listContainer) return;

    if (cart.length === 0) {
      listContainer.innerHTML = `
        <div class="text-center py-4 text-secondary">
          <div class="fs-1 mb-2">🛒</div>
          <div class="fw-bold">Your cart is empty</div>
          <div class="small">Tap any menu card to add items.</div>
        </div>
      `;
      return;
    }

    let html = '<div class="d-flex flex-column gap-2">';
    cart.forEach((item, index) => {
      const qtyDisplay = item.item_type === 'weight' ? parseFloat(item.quantity).toFixed(3) + ' ' + item.unit : item.quantity;
      html += `
        <div class="d-flex align-items-center justify-content-between p-2.5 bg-white border rounded-3 shadow-2xs" style="border-radius: 12px !important;">
          <div style="flex: 1; min-width: 0; padding-right: 0.5rem;">
            <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">${escapeHtml(item.item_name)}</div>
            <div class="text-secondary small">
              ${escapeHtml(item.variant_name)} • ₹${parseFloat(item.unit_price).toFixed(2)}
            </div>
          </div>
          
          <div class="d-flex align-items-center gap-2">
            <div class="d-flex align-items-center bg-light border rounded-pill px-1 py-0.5">
              <button type="button" class="btn btn-sm btn-link text-dark p-0 px-1 text-decoration-none fw-bold" onclick="DineBilling.updateQty(${index}, -1)">−</button>
              <span class="px-2 fw-bold text-dark small">${qtyDisplay}</span>
              <button type="button" class="btn btn-sm btn-link text-dark p-0 px-1 text-decoration-none fw-bold" onclick="DineBilling.updateQty(${index}, 1)">+</button>
            </div>
            <div class="fw-bold text-end" style="min-width: 65px; color: #0f172a; font-size: 0.95rem;">
              ₹${parseFloat(item.total_price).toFixed(2)}
            </div>
            <button type="button" class="btn btn-sm text-danger p-1" onclick="DineBilling.removeItem(${index})" title="Remove item">
              <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>
      `;
    });
    html += '</div>';
    listContainer.innerHTML = html;
  }

  async function saveOrder() {
    if (cart.length === 0) {
      alert('Cart is empty. Please add items to complete billing.');
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

        showPhonePeSuccessAnimation(res, function () {
          window.location.href = res.redirect_url || '/receipt/view?id=' + res.order_id;
        });
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
    handleCardClick: function (cardEl) {
      try {
        const raw = cardEl.getAttribute('data-item');
        if (raw) {
          handleItemClick(JSON.parse(raw));
        }
      } catch (e) {
        console.error(e);
      }
    },

    selectPortion: function (variantId) {
      if (!currentActiveItem) return;
      const v = (currentActiveItem.variants || []).find(x => x.id === variantId);
      if (v) {
        addToCart(currentActiveItem, v, 1);
        const modal = getVariantModal();
        if (modal) modal.hide();
      }
    },

    switchWeightMode: function (mode) {
      weightModalMode = mode;
      const tabWeight = document.getElementById('weightTabWeight');
      const tabRupees = document.getElementById('weightTabRupees');
      const secWeight = document.getElementById('weightInputSection');
      const secRupees = document.getElementById('rupeesInputSection');

      if (mode === 'weight') {
        if (tabWeight) { tabWeight.style.background = '#0f172a'; tabWeight.style.color = '#ffffff'; }
        if (tabRupees) { tabRupees.style.background = 'transparent'; tabRupees.style.color = '#64748b'; }
        if (secWeight) secWeight.style.display = 'block';
        if (secRupees) secRupees.style.display = 'none';
        this.recalcWeightOutput();
      } else {
        if (tabWeight) { tabWeight.style.background = 'transparent'; tabWeight.style.color = '#64748b'; }
        if (tabRupees) { tabRupees.style.background = '#0f172a'; tabRupees.style.color = '#ffffff'; }
        if (secWeight) secWeight.style.display = 'none';
        if (secRupees) secRupees.style.display = 'block';
        this.recalcRupeesOutput();
      }
    },

    setWeightVal: function (val) {
      const input = document.getElementById('modalWeightInput');
      if (input) {
        input.value = val;
        this.recalcWeightOutput();
      }
    },

    setRupeesVal: function (val) {
      const input = document.getElementById('modalRupeesInput');
      if (input) {
        input.value = val;
        this.recalcRupeesOutput();
      }
    },

    recalcWeightOutput: function () {
      if (!currentActiveItem || !currentActiveVariant) return;
      const unit = currentActiveVariant.quantity_unit || currentActiveItem.base_unit || 'kg';
      const price = parseFloat(currentActiveVariant.price || currentActiveItem.price_per_unit || 0);
      const input = document.getElementById('modalWeightInput');
      const qty = input ? parseFloat(input.value) : 1;
      const total = isNaN(qty) ? 0 : (qty * price);

      const sumW = document.getElementById('calcWeightSummary');
      const sumP = document.getElementById('calcPriceSummary');
      if (sumW) sumW.textContent = `Gives: ${(isNaN(qty) ? 0 : qty).toFixed(3)} ${unit}`;
      if (sumP) sumP.textContent = `Total: ₹${total.toFixed(2)}`;
    },

    recalcRupeesOutput: function () {
      if (!currentActiveItem || !currentActiveVariant) return;
      const unit = currentActiveVariant.quantity_unit || currentActiveItem.base_unit || 'kg';
      const price = parseFloat(currentActiveVariant.price || currentActiveItem.price_per_unit || 0);
      const input = document.getElementById('modalRupeesInput');
      const rupees = input ? parseFloat(input.value) : 0;
      const calculatedQty = (price > 0 && !isNaN(rupees)) ? (rupees / price) : 0;

      const sumW = document.getElementById('calcWeightSummary');
      const sumP = document.getElementById('calcPriceSummary');
      if (sumW) sumW.textContent = `Gives: ${calculatedQty.toFixed(3)} ${unit}`;
      if (sumP) sumP.textContent = `Total: ₹${(isNaN(rupees) ? 0 : rupees).toFixed(2)}`;
    },

    confirmWeightAdd: function (variantId) {
      if (!currentActiveItem) return;
      const v = (currentActiveItem.variants || []).find(x => x.id === variantId) || currentActiveVariant || { id: 0, price: currentActiveItem.price_per_unit || 0 };
      const unitPrice = parseFloat(v.price || currentActiveItem.price_per_unit || 0);

      let finalQty = 1;
      if (weightModalMode === 'weight') {
        const input = document.getElementById('modalWeightInput');
        finalQty = input ? parseFloat(input.value) : 1;
      } else {
        const input = document.getElementById('modalRupeesInput');
        const rupees = input ? parseFloat(input.value) : 0;
        finalQty = unitPrice > 0 ? (rupees / unitPrice) : 0;
      }

      if (isNaN(finalQty) || finalQty <= 0) {
        alert('Please enter a valid weight or amount.');
        return;
      }

      addToCart(currentActiveItem, v, finalQty);
      const modal = getVariantModal();
      if (modal) modal.hide();
    },

    updateQty: function (index, delta) {
      if (!cart[index]) return;
      cart[index].quantity += delta;
      if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
      } else {
        cart[index].total_price = Math.round(cart[index].quantity * cart[index].unit_price * 100) / 100;
      }
      renderCart();
    },

    removeItem: function (index) {
      if (cart[index]) {
        cart.splice(index, 1);
        renderCart();
      }
    },

    clearCart: function () {
      if (cart.length > 0 && confirm('Clear all items from this order cart?')) {
        cart = [];
        renderCart();
      }
    },

    openMobileCart: function () {
      const cartModal = getCartModal();
      if (cartModal) {
        cartModal.show();
      }
    },

    setPaymentMethod: function (method) {
      selectedPayment = method;
    },

    saveOrder: saveOrder
  };
})();
