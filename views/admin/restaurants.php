<?php
/**
 * Super Admin Restaurants List View Template (Mobile-Optimized)
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-sm-center flex-column flex-sm-row gap-2 mb-3">
    <div>
        <h4 class="fw-bold m-0" style="letter-spacing: -0.5px;">Restaurants</h4>
        <small class="text-muted">Create, configure, or activate/deactivate tenant restaurants</small>
    </div>
    <a href="<?= url('admin/restaurants/create') ?>" class="btn btn-primary btn-sm px-3 fw-semibold">+ Create Restaurant</a>
</div>

<div class="card shadow-sm border-0 mb-5" style="border-radius: 1rem; overflow: hidden; border: 1px solid #e2e8f0 !important;">
    <div class="card-body p-0">
        <!-- Desktop Table View (>= 768px) -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Restaurant</th>
                        <th>Contact</th>
                        <th>Timezone</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($restaurants)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No restaurants configured yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($restaurants as $r): ?>
                            <tr>
                                <td class="ps-4">#<?= (int)$r['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark fs-6"><?= e($r['name']) ?></div>
                                    <small class="text-muted"><?= e($r['address'] ?? '-') ?></small>
                                </td>
                                <td><?= e($r['phone'] ?? '-') ?></td>
                                <td><code><?= e($r['timezone']) ?></code></td>
                                <td>
                                    <span class="badge bg-<?= $r['status'] === 'active' ? 'success' : 'danger' ?>">
                                        <?= ucfirst(e($r['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?= url('admin/restaurants/edit?id=' . $r['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <form action="<?= url('admin/restaurants/toggle') ?>" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                            <button type="submit" class="btn btn-sm <?= $r['status'] === 'active' ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                                                <?= $r['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Card View (< 768px) -->
        <div class="d-md-none p-3">
            <?php if (empty($restaurants)): ?>
                <div class="text-center py-4 px-2">
                    <div class="fs-1 mb-2">🍽️</div>
                    <div class="fw-bold text-dark mb-1">No restaurants configured yet</div>
                    <p class="text-muted small mb-3">Add your first restaurant to start managing your point of sale.</p>
                    <a href="<?= url('admin/restaurants/create') ?>" class="btn btn-primary btn-sm w-100 py-2 fw-bold">+ Create First Restaurant</a>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($restaurants as $r): ?>
                        <div class="p-3 bg-light rounded-3 border" style="border-color: #e2e8f0 !important;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="text-muted small">#<?= (int)$r['id'] ?></span>
                                    <div class="fw-bold text-dark fs-6"><?= e($r['name']) ?></div>
                                </div>
                                <span class="badge bg-<?= $r['status'] === 'active' ? 'success' : 'danger' ?>">
                                    <?= ucfirst(e($r['status'])) ?>
                                </span>
                            </div>

                            <?php if (!empty($r['phone'])): ?>
                                <div class="small text-muted mb-1">📞 <?= e($r['phone']) ?></div>
                            <?php endif; ?>

                            <?php if (!empty($r['address'])): ?>
                                <div class="small text-muted mb-2">📍 <?= e($r['address']) ?></div>
                            <?php endif; ?>

                            <div class="small text-muted mb-3">
                                Timezone: <code><?= e($r['timezone']) ?></code>
                            </div>

                            <div class="d-flex gap-2 pt-2 border-top">
                                <a href="<?= url('admin/restaurants/edit?id=' . $r['id']) ?>" class="btn btn-sm btn-outline-secondary flex-grow-1 py-1.5 fw-semibold">Edit Details</a>
                                <form action="<?= url('admin/restaurants/toggle') ?>" method="POST" class="flex-grow-1 d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                    <button type="submit" class="btn btn-sm <?= $r['status'] === 'active' ? 'btn-outline-danger' : 'btn-outline-success' ?> w-100 py-1.5 fw-semibold">
                                        <?= $r['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
