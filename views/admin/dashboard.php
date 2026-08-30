<?php
/**
 * Super Admin Dashboard View Template (Mobile-Optimized)
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-sm-center flex-column flex-sm-row gap-3 mb-4">
    <div>
        <h3 class="fw-bold m-0 text-dark" style="letter-spacing: -0.5px;">Super Admin Console</h3>
        <small class="text-muted">Multi-tenant restaurant system overview</small>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= url('admin/restaurants/create') ?>" class="btn btn-primary btn-sm px-3 fw-semibold">+ Create Restaurant</a>
        <a href="<?= url('admin/users/create') ?>" class="btn btn-outline-secondary btn-sm px-3 fw-semibold">+ Create User</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="card shadow-sm border-0 bg-primary text-white p-4" style="border-radius: 1rem;">
            <div class="text-white-50 small text-uppercase fw-semibold">TOTAL RESTAURANTS</div>
            <div class="metric-value text-white my-2"><?= (int)$totalRestaurants ?></div>
            <a href="<?= url('admin/restaurants') ?>" class="text-white text-decoration-underline small fw-medium">Manage all restaurants →</a>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="card shadow-sm border-0 p-4" style="border-radius: 1rem; background: #ffffff; border: 1px solid #e2e8f0 !important;">
            <div class="text-muted small text-uppercase fw-semibold">TOTAL ACTIVE USERS</div>
            <div class="metric-value text-dark my-2"><?= (int)$totalUsers ?></div>
            <a href="<?= url('admin/users') ?>" class="text-primary text-decoration-underline small fw-medium">Manage user accounts →</a>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-5" style="border-radius: 1rem; overflow: hidden; border: 1px solid #e2e8f0 !important;">
    <div class="card-header bg-white py-3 px-3 px-md-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0 fs-6 fs-md-5">Restaurants Overview</h5>
        <a href="<?= url('admin/restaurants') ?>" class="btn btn-sm btn-outline-primary">View All</a>
    </div>

    <!-- Desktop Table View (>= 768px) -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Restaurant Name</th>
                    <th>Phone</th>
                    <th>Timezone</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($restaurants)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <p class="mb-2">No restaurants added yet.</p>
                            <a href="<?= url('admin/restaurants/create') ?>" class="btn btn-sm btn-primary">+ Create First Restaurant</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($restaurants as $r): ?>
                        <tr>
                            <td class="ps-4">#<?= (int)$r['id'] ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= e($r['name']) ?></div>
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
                                <a href="<?= url('admin/restaurants/edit?id=' . $r['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
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
                <div class="fw-bold text-dark mb-1">No restaurants added yet</div>
                <p class="text-muted small mb-3">Add your first restaurant to start managing menu and staff.</p>
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

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="small text-muted">Timezone: <code><?= e($r['timezone']) ?></code></span>
                            <a href="<?= url('admin/restaurants/edit?id=' . $r['id']) ?>" class="btn btn-sm btn-outline-secondary px-3 py-1">Edit</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
