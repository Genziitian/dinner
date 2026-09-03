<?php
/**
 * Mill Services & Rate Management View
 * Atta & Oil Mill Management System
 * Strictly NO emojis used
 */
declare(strict_types=1);
?>

<div class="admin-container" style="max-width: 900px;">
    <!-- Page Header & Action -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h2 class="admin-page-title mb-0.5"><?= __t('services_title') ?></h2>
            <p class="admin-page-subtitle mb-0"><?= count($services) ?> <?= __t('service') ?></p>
        </div>

        <button type="button" class="admin-btn-primary py-1.5 px-3" style="font-size: 0.85rem;" onclick="openAddServiceModal()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span><?= __t('add_new_service') ?></span>
        </button>
    </div>

    <!-- Notice Box: Snapshot Rate Retention -->
    <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size: 0.82rem;">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Changing prices here will only apply to future orders. All past orders permanently retain their original rates.</span>
    </div>

    <!-- Services Table -->
    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3"><?= __t('service_name') ?></th>
                        <th><?= __t('service_name_hi') ?></th>
                        <th><?= __t('rate_fixed') ?></th>
                        <th><?= __t('status') ?></th>
                        <th class="text-end pe-3"><?= __t('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $s): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-dark"><?= e($s['name']) ?></td>
                            <td class="text-muted"><?= !empty($s['name_hi']) ? e($s['name_hi']) : '-' ?></td>
                            <td class="fw-bold text-primary"><?= format_currency($s['rate_per_kg']) ?> / KG</td>
                            <td>
                                <span class="badge <?= $s['active'] ? 'bg-success-subtle text-success border border-success' : 'bg-secondary-subtle text-secondary border' ?> px-2 py-0.5" style="font-size: 0.74rem;">
                                    <?= $s['active'] ? __t('active') : __t('inactive') ?>
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="d-flex justify-content-end gap-1.5">
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-light border py-1 px-2.5" 
                                        style="font-size: 0.78rem;"
                                        onclick="openEditServiceModal(<?= (int)$s['id'] ?>, '<?= e(addslashes($s['name'])) ?>', '<?= e(addslashes($s['name_hi'] ?? '')) ?>', <?= (float)$s['rate_per_kg'] ?>, <?= (int)$s['active'] ?>)"
                                    >
                                        <?= __t('edit_service') ?>
                                    </button>

                                    <form method="POST" action="<?= url('mill/services/toggle') ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                                        <button type="submit" class="btn btn-sm <?= $s['active'] ? 'btn-outline-danger' : 'btn-outline-success' ?> py-1 px-2" style="font-size: 0.78rem;">
                                            <?= $s['active'] ? __t('disable') : __t('enable') ?>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Adding / Editing Service -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="<?= url('mill/services/save') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="serviceId" value="0">

                <div class="modal-header py-2.5 px-3 bg-light">
                    <h6 class="modal-title fw-bold text-dark" id="serviceModalTitle" style="font-size: 0.92rem;">
                        <?= __t('add_new_service') ?>
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">
                            <?= __t('service_name') ?> <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="serviceName" class="form-control" placeholder="e.g. Wheat grinding" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">
                            <?= __t('service_name_hi') ?>
                        </label>
                        <input type="text" name="name_hi" id="serviceNameHi" class="form-control" placeholder="e.g. गेहूं पिसाई">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.86rem;">
                            <?= __t('rate_fixed') ?> <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">Rs.</span>
                            <input type="number" name="rate_per_kg" id="serviceRate" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                            <span class="input-group-text bg-light text-muted">/ KG</span>
                        </div>
                    </div>

                    <div class="form-check" id="serviceActiveWrap">
                        <input class="form-check-input" type="checkbox" name="active" value="1" id="serviceActive" checked>
                        <label class="form-check-label fw-semibold text-dark" for="serviceActive" style="font-size: 0.86rem;">
                            <?= __t('active') ?>
                        </label>
                    </div>
                </div>

                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal"><?= __t('cancel') ?></button>
                    <button type="submit" class="admin-btn-primary py-1 px-3.5" style="font-size: 0.85rem;">
                        <?= __t('save_service') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddServiceModal() {
    document.getElementById('serviceId').value = '0';
    document.getElementById('serviceName').value = '';
    document.getElementById('serviceNameHi').value = '';
    document.getElementById('serviceRate').value = '';
    document.getElementById('serviceActive').checked = true;
    document.getElementById('serviceModalTitle').textContent = '<?= __t('add_new_service') ?>';
    new bootstrap.Modal(document.getElementById('serviceModal')).show();
}

function openEditServiceModal(id, name, nameHi, rate, active) {
    document.getElementById('serviceId').value = id;
    document.getElementById('serviceName').value = name;
    document.getElementById('serviceNameHi').value = nameHi;
    document.getElementById('serviceRate').value = rate;
    document.getElementById('serviceActive').checked = active === 1;
    document.getElementById('serviceModalTitle').textContent = '<?= __t('edit_service') ?>';
    new bootstrap.Modal(document.getElementById('serviceModal')).show();
}
</script>
