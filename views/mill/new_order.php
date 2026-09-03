<?php
/**
 * Create New Order View
 * Atta & Oil Mill Management System
 * Strictly NO emojis used
 */
declare(strict_types=1);
?>

<div class="admin-container" style="max-width: 680px;">
    <!-- Breadcrumb Header -->
    <div class="d-flex align-items-center gap-2 mb-2.5">
        <a href="<?= url('mill/dashboard') ?>" class="admin-btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span><?= __t('nav_dashboard') ?></span>
        </a>
    </div>

    <div class="admin-header-box mb-3">
        <div>
            <h2 class="admin-page-title"><?= __t('create_order_title') ?></h2>
            <p class="admin-page-subtitle"><?= __t('today_summary') ?> &bull; <?= format_date(date('Y-m-d')) ?></p>
        </div>
    </div>

    <div class="admin-card">
        <div class="p-3 p-md-3.5">
            <form method="POST" action="<?= url('mill/orders/new') ?>" autocomplete="off" id="millOrderForm">
                <?= csrf_field() ?>

                <!-- Customer Search & Auto-Remember -->
                <div class="position-relative mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">
                        <?= __t('customer_phone') ?> <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </span>
                        <input 
                            type="tel" 
                            name="customer_phone" 
                            id="customerPhoneInput" 
                            class="form-control" 
                            style="font-size: 0.92rem;" 
                            placeholder="e.g. 9876543210" 
                            value="<?= e($prefillCustomer['phone'] ?? '') ?>"
                            required 
                            maxlength="15"
                            oninput="lookupCustomer(this.value)"
                        >
                    </div>
                    <!-- Customer Autocomplete Dropdown -->
                    <div id="customerSuggestions" class="list-group position-absolute w-100 shadow-sm d-none" style="z-index: 1050; max-height: 200px; overflow-y: auto;"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">
                        <?= __t('customer_name') ?> <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        <input 
                            type="text" 
                            name="customer_name" 
                            id="customerNameInput" 
                            class="form-control" 
                            style="font-size: 0.92rem;" 
                            placeholder="e.g. Ramesh Kumar" 
                            value="<?= e($prefillCustomer['name'] ?? '') ?>"
                            required
                        >
                    </div>
                </div>

                <!-- Service Selector Grid -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">
                        <?= __t('service_type') ?> <span class="text-danger">*</span>
                    </label>
                    <div class="row g-2" id="serviceOptions">
                        <?php foreach ($services as $idx => $s): ?>
                            <?php 
                            $displayName = current_lang() === 'hi' && !empty($s['name_hi']) ? $s['name_hi'] : $s['name'];
                            $isSelected = $idx === 0;
                            ?>
                            <div class="col-6 col-sm-4">
                                <label class="w-100 border rounded p-2.5 text-center cursor-pointer service-card <?= $isSelected ? 'border-primary bg-primary-subtle' : 'bg-white' ?>" style="cursor: pointer;">
                                    <input 
                                        type="radio" 
                                        name="service_id" 
                                        value="<?= (int)$s['id'] ?>" 
                                        data-rate="<?= (float)$s['rate_per_kg'] ?>"
                                        class="d-none service-radio" 
                                        <?= $isSelected ? 'checked' : '' ?>
                                        onchange="onServiceChange()"
                                    >
                                    <div class="fw-semibold text-dark mb-0.5" style="font-size: 0.86rem;"><?= e($displayName) ?></div>
                                    <div class="text-primary small fw-bold" style="font-size: 0.8rem;">
                                        <?= format_currency($s['rate_per_kg']) ?> / KG
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Weight and Automatic Price Calculation -->
                <div class="row g-2 mb-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">
                            <?= __t('weight_kg') ?> <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input 
                                type="number" 
                                name="weight_kg" 
                                id="weightInput" 
                                class="form-control" 
                                style="font-size: 1.1rem; font-weight: bold;" 
                                step="0.1" 
                                min="0.1" 
                                placeholder="0.0" 
                                required
                                oninput="calculateTotal()"
                            >
                            <span class="input-group-text bg-light text-dark fw-bold">KG</span>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">
                            <?= __t('rate_per_kg') ?>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">Rs.</span>
                            <input 
                                type="text" 
                                id="rateDisplayInput" 
                                class="form-control bg-light" 
                                style="font-size: 1.1rem; font-weight: bold;" 
                                readonly
                            >
                        </div>
                    </div>
                </div>

                <!-- Prominent Calculated Total Display -->
                <div class="p-3 bg-light rounded border mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small d-block"><?= __t('total_price') ?></span>
                        <div class="h2 fw-bold text-primary mb-0" id="totalDisplay">₹0.00</div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1" id="calculationFormula" style="font-family: monospace;">
                            0.0 KG &times; ₹0.00
                        </span>
                    </div>
                </div>

                <!-- Payment Status & Method -->
                <div class="row g-2 mb-3">
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">
                            <?= __t('payment_status') ?> <span class="text-danger">*</span>
                        </label>
                        <select name="payment_status" id="paymentStatusSelect" class="form-select" onchange="onPaymentStatusChange()">
                            <option value="unpaid" selected><?= __t('unpaid') ?></option>
                            <option value="paid"><?= __t('paid') ?></option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">
                            <?= __t('payment_method') ?>
                        </label>
                        <select name="payment_method" class="form-select">
                            <option value="Cash"><?= __t('cash') ?></option>
                            <option value="UPI"><?= __t('upi') ?></option>
                        </select>
                    </div>
                </div>

                <!-- Optional Notes -->
                <div class="mb-3.5">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">
                        <?= __t('optional_notes') ?>
                    </label>
                    <textarea 
                        name="notes" 
                        class="form-control" 
                        rows="2" 
                        placeholder="<?= __t('notes_placeholder') ?>"
                        style="font-size: 0.88rem;"
                    ></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 pt-2 border-top">
                    <button type="submit" class="admin-btn-primary py-2.5 px-3 flex-grow-1 justify-content-center" style="font-size: 0.95rem;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span><?= __t('save_order_btn') ?></span>
                    </button>
                    <a href="<?= url('mill/dashboard') ?>" class="admin-btn-secondary py-2.5 px-3">
                        <?= __t('cancel') ?>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentRate = 0;

function onServiceChange() {
    const radios = document.querySelectorAll('.service-radio');
    radios.forEach(radio => {
        const card = radio.closest('.service-card');
        if (radio.checked) {
            card.classList.add('border-primary', 'bg-primary-subtle');
            card.classList.remove('bg-white');
            currentRate = parseFloat(radio.getAttribute('data-rate') || '0');
            document.getElementById('rateDisplayInput').value = currentRate.toFixed(2);
        } else {
            card.classList.remove('border-primary', 'bg-primary-subtle');
            card.classList.add('bg-white');
        }
    });
    calculateTotal();
}

function calculateTotal() {
    const weight = parseFloat(document.getElementById('weightInput').value || '0');
    const total = Math.round(weight * currentRate * 100) / 100;
    document.getElementById('totalDisplay').textContent = '₹' + total.toFixed(2);
    document.getElementById('calculationFormula').textContent = weight.toFixed(1) + ' KG × ₹' + currentRate.toFixed(2);
}

// Auto-suggest customer lookup
let lookupTimeout = null;
function lookupCustomer(query) {
    clearTimeout(lookupTimeout);
    const box = document.getElementById('customerSuggestions');
    if (!query || query.length < 3) {
        box.classList.add('d-none');
        box.innerHTML = '';
        return;
    }

    lookupTimeout = setTimeout(() => {
        fetch('<?= url('mill/api/customers') ?>?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                if (data.success && data.customers && data.customers.length > 0) {
                    box.innerHTML = '';
                    data.customers.forEach(c => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action py-2 px-3 text-start';
                        item.innerHTML = '<div class="fw-bold text-dark">' + escapeHtml(c.name) + '</div><small class="text-muted">' + escapeHtml(c.phone) + '</small>';
                        item.onclick = function() {
                            document.getElementById('customerPhoneInput').value = c.phone;
                            document.getElementById('customerNameInput').value = c.name;
                            box.classList.add('d-none');
                        };
                        box.appendChild(item);
                    });
                    box.classList.remove('d-none');
                } else {
                    box.classList.add('d-none');
                }
            })
            .catch(() => box.classList.add('d-none'));
    }, 250);
}

function escapeHtml(str) {
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

document.addEventListener('click', function(e) {
    const box = document.getElementById('customerSuggestions');
    if (!box.contains(e.target) && e.target !== document.getElementById('customerPhoneInput')) {
        box.classList.add('d-none');
    }
});

// Initialize rate on page load
document.addEventListener('DOMContentLoaded', function() {
    onServiceChange();
});
</script>
