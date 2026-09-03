<?php
/**
 * Mill Orders List & Management View
 * Atta & Oil Mill Management System
 * Strictly NO emojis used
 */
declare(strict_types=1);

$currentStatus = $status ?? 'all';
$shopName = !empty($user['restaurant_name']) ? $user['restaurant_name'] : 'Atta Mill';
?>

<div class="admin-container" style="max-width: 1100px;">
    <!-- Page Header & Action -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h2 class="admin-page-title mb-0.5"><?= __t('order_list_title') ?></h2>
            <p class="admin-page-subtitle mb-0"><?= count($orders) ?> <?= __t('nav_orders') ?> <?= $currentStatus !== 'all' ? '(' . __t('status_' . $currentStatus) . ')' : '' ?></p>
        </div>

        <a href="<?= url('mill/orders/new') ?>" class="admin-btn-primary py-1.5 px-3 d-none d-md-inline-flex" style="font-size: 0.85rem;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span><?= __t('nav_new_order') ?></span>
        </a>
    </div>

    <!-- Search & Filter Controls -->
    <div class="admin-card p-3 mb-3">
        <form method="GET" action="<?= url('mill/orders') ?>" class="row g-2 align-items-center">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control" 
                        placeholder="<?= __t('search_placeholder') ?>"
                        value="<?= e($search ?? '') ?>"
                        style="font-size: 0.88rem;"
                    >
                    <?php if (!empty($search)): ?>
                        <a href="<?= url('mill/orders') ?>" class="btn btn-outline-secondary btn-sm d-flex align-items-center" title="Clear">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="d-flex flex-wrap gap-1.5 justify-content-md-end">
                    <?php 
                    $chips = [
                        'all'        => __t('status_all'),
                        'received'   => __t('status_received'),
                        'processing' => __t('status_processing'),
                        'ready'      => __t('status_ready'),
                        'delivered'  => __t('status_delivered'),
                        'unpaid'     => __t('status_unpaid'),
                    ];
                    foreach ($chips as $val => $label): 
                        $active = $currentStatus === $val;
                    ?>
                        <a 
                            href="<?= url('mill/orders?status=' . $val . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" 
                            class="badge text-decoration-none px-2.5 py-1.5 border <?= $active ? 'bg-primary text-white border-primary' : 'bg-light text-secondary border' ?>"
                            style="font-size: 0.78rem;"
                        >
                            <?= e($label) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Orders Cards Grid / Table -->
    <?php if (empty($orders)): ?>
        <div class="admin-card p-4 text-center text-muted">
            <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="mb-2 text-secondary"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <h6 class="fw-bold text-dark mb-1"><?= __t('no_orders') ?></h6>
            <a href="<?= url('mill/orders/new') ?>" class="admin-btn-primary py-1.5 px-3 mt-2 d-inline-flex" style="font-size: 0.85rem;">
                <?= __t('quick_new_order') ?>
            </a>
        </div>
    <?php else: ?>
        <div class="row g-2 g-md-3">
            <?php foreach ($orders as $o): ?>
                <?php 
                $cleanPhone = preg_replace('/[^0-9]/', '', (string)$o['customer_phone']);
                $isPaid = $o['payment_status'] === 'paid';
                
                // Pre-written WhatsApp messages
                $msgTemplate = $o['status'] === 'ready' ? __t('msg_ready') : __t('msg_received');
                $defaultMsg = str_replace(
                    ['{customer}', '{order_num}', '{service}', '{weight}', '{amount}', '{shop}', '{payment}'],
                    [$o['customer_name'], $o['order_number'], $o['service_name'], $o['weight_kg'], $o['total_amount'], $shopName, $isPaid ? __t('paid') : __t('unpaid')],
                    $msgTemplate
                );
                ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="admin-card h-100 d-flex flex-column p-3">
                        <!-- Top Row: Order # & Status Badge -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="fw-bold text-dark h6 mb-0">#<?= (int)$o['order_number'] ?></span>
                                <small class="text-muted d-block" style="font-size: 0.72rem;">
                                    <?= format_date($o['order_date']) ?> &bull; <?= format_time($o['order_time']) ?>
                                </small>
                            </div>

                            <!-- Status Changer Dropdown -->
                            <div class="dropdown">
                                <?php 
                                $statusBadgeClass = match ($o['status']) {
                                    'received'   => 'bg-warning-subtle text-warning border-warning',
                                    'processing' => 'bg-info-subtle text-info border-info',
                                    'ready'      => 'bg-success-subtle text-success border-success',
                                    'delivered'  => 'bg-light text-secondary border',
                                    'cancelled'  => 'bg-danger-subtle text-danger border-danger',
                                    default      => 'bg-light text-dark',
                                };
                                ?>
                                <button class="badge border <?= $statusBadgeClass ?> dropdown-toggle py-1 px-2 text-decoration-none" type="button" data-bs-toggle="dropdown" style="font-size: 0.78rem;">
                                    <?= __t('status_' . $o['status']) ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 0.84rem;">
                                    <?php foreach (['received', 'processing', 'ready', 'delivered', 'cancelled'] as $st): ?>
                                        <li>
                                            <form method="POST" action="<?= url('mill/orders/status') ?>">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                                                <input type="hidden" name="status" value="<?= $st ?>">
                                                <button type="submit" class="dropdown-item <?= $o['status'] === $st ? 'active fw-bold' : '' ?>">
                                                    <?= __t('status_' . $st) ?>
                                                </button>
                                            </form>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <!-- Customer Info -->
                        <div class="mb-2.5 pb-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?= url('mill/customers/view?phone=' . urlencode($o['customer_phone'])) ?>" class="fw-bold text-dark text-decoration-none" style="font-size: 0.94rem;">
                                    <?= e($o['customer_name']) ?>
                                </a>
                                <small class="text-muted fw-semibold"><?= e($o['customer_phone']) ?></small>
                            </div>
                            <?php if (!empty($o['notes'])): ?>
                                <div class="text-muted small mt-1 fst-italic" style="font-size: 0.78rem;">
                                    &ldquo;<?= e($o['notes']) ?>&rdquo;
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Service & Weight Details -->
                        <div class="row g-1 mb-2.5 flex-grow-1" style="font-size: 0.84rem;">
                            <div class="col-7">
                                <span class="text-muted small d-block"><?= __t('service') ?></span>
                                <span class="fw-semibold text-dark"><?= e($o['service_name']) ?></span>
                            </div>
                            <div class="col-5 text-end">
                                <span class="text-muted small d-block"><?= __t('weight') ?></span>
                                <span class="fw-bold text-dark"><?= (float)$o['weight_kg'] ?> KG</span>
                            </div>
                        </div>

                        <!-- Rate & Total Amount & Payment Status -->
                        <div class="p-2 bg-light rounded border mb-2.5 d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.72rem;">@ <?= format_currency($o['rate_per_kg']) ?>/KG</small>
                                <span class="fw-bold text-dark h6 mb-0"><?= format_currency($o['total_amount']) ?></span>
                            </div>

                            <!-- Payment Status Toggle Form -->
                            <form method="POST" action="<?= url('mill/orders/payment') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                                <input type="hidden" name="payment_status" value="<?= $isPaid ? 'unpaid' : 'paid' ?>">
                                <input type="hidden" name="payment_method" value="<?= $isPaid ? '' : 'Cash' ?>">
                                <button type="submit" class="btn btn-sm <?= $isPaid ? 'btn-success' : 'btn-outline-danger' ?> py-0.5 px-2 fw-semibold" style="font-size: 0.76rem;" title="Click to toggle payment status">
                                    <?= $isPaid ? __t('paid') . ' (' . e($o['payment_method'] ?: 'Cash') . ')' : __t('unpaid') ?>
                                </button>
                            </form>
                        </div>

                        <!-- Card Footer Action: WhatsApp & Actions -->
                        <div class="d-flex gap-1.5 pt-1 border-top">
                            <!-- WhatsApp Trigger Button -->
                            <button 
                                type="button" 
                                class="btn btn-sm btn-outline-success flex-grow-1 d-flex align-items-center justify-content-center gap-1.5 py-1"
                                style="font-size: 0.8rem; font-weight: 600;"
                                onclick="openWhatsAppModal('<?= e($cleanPhone) ?>', <?= htmlspecialchars(json_encode($defaultMsg), ENT_QUOTES) ?>)"
                            >
                                <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.694.062-1.127-.082-.363-.122-.82-.294-1.411-.555-2.518-1.109-4.148-3.663-4.275-3.834-.125-.171-1.02-1.358-1.02-2.589 0-1.231.644-1.836.872-2.087.228-.251.498-.314.664-.314.166 0 .332.002.477.01.155.008.363-.058.568.435.21.503.716 1.748.778 1.874.062.125.104.271.021.438-.083.166-.124.271-.249.417-.124.145-.262.325-.374.436-.125.125-.255.26-.11.51.145.249.645 1.064 1.385 1.722.952.846 1.756 1.108 2.006 1.233.25.125.395.104.54-.062.146-.167.623-.728.789-.978.166-.249.332-.208.56-.125.228.084 1.453.686 1.702.81.249.125.415.187.477.291.063.104.063.603-.081 1.008z"/></svg>
                                <span><?= __t('whatsapp_btn') ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal for WhatsApp Message Preview and Edit -->
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-2.5 px-3 bg-light">
                <h6 class="modal-title fw-bold text-dark" style="font-size: 0.92rem;"><?= __t('whatsapp_modal_title') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <p class="text-muted small mb-2"><?= __t('whatsapp_edit_hint') ?></p>
                <textarea id="whatsappMessageText" class="form-control mb-3" rows="4" style="font-size: 0.88rem;"></textarea>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal"><?= __t('cancel') ?></button>
                    <button type="button" class="btn btn-sm btn-success px-3.5 fw-semibold d-flex align-items-center gap-1.5" onclick="sendWhatsAppDirect()">
                        <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.694.062-1.127-.082-.363-.122-.82-.294-1.411-.555-2.518-1.109-4.148-3.663-4.275-3.834-.125-.171-1.02-1.358-1.02-2.589 0-1.231.644-1.836.872-2.087.228-.251.498-.314.664-.314.166 0 .332.002.477.01.155.008.363-.058.568.435.21.503.716 1.748.778 1.874.062.125.104.271.021.438-.083.166-.124.271-.249.417-.124.145-.262.325-.374.436-.125.125-.255.26-.11.51.145.249.645 1.064 1.385 1.722.952.846 1.756 1.108 2.006 1.233.25.125.395.104.54-.062.146-.167.623-.728.789-.978.166-.249.332-.208.56-.125.228.084 1.453.686 1.702.81.249.125.415.187.477.291.063.104.063.603-.081 1.008z"/></svg>
                        <span><?= __t('send_via_whatsapp') ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentWhatsAppPhone = '';

function openWhatsAppModal(phone, message) {
    currentWhatsAppPhone = phone;
    document.getElementById('whatsappMessageText').value = message;
    const modalEl = document.getElementById('whatsappModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function sendWhatsAppDirect() {
    const text = document.getElementById('whatsappMessageText').value;
    let cleanPhone = currentWhatsAppPhone.replace(/[^0-9]/g, '');
    // If 10 digits Indian phone without country code, prefix 91
    if (cleanPhone.length === 10) {
        cleanPhone = '91' + cleanPhone;
    }
    const url = 'https://wa.me/' + cleanPhone + '?text=' + encodeURIComponent(text);
    window.open(url, '_blank');
}
</script>
