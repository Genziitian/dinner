<?php
/**
 * Super Admin Dashboard View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold m-0 text-dark">Super Admin Console</h3>
        <small class="text-muted">Multi-tenant restaurant system overview</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/restaurants/create') ?>" class="btn btn-primary btn-sm">+ Create Restaurant</a>
        <a href="<?= url('admin/users/create') ?>" class="btn btn-outline-secondary btn-sm">+ Create User</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="card shadow-sm border-0 bg-primary text-white p-4" style="border-radius: 1rem;">
            <div class="text-white-50 small text-uppercase fw-semibold">TOTAL RESTAURANTS</div>
            <div class="display-5 fw-bold my-2"><?= (int)$totalRestaurants ?></div>
            <a href="<?= url('admin/restaurants') ?>" class="text-white text-decoration-underline small">Manage all restaurants →</a>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="card shadow-sm border-0 p-4" style="border-radius: 1rem;">
            <div class="text-muted small text-uppercase fw-semibold">TOTAL ACTIVE USERS</div>
            <div class="display-5 fw-bold text-dark my-2"><?= (int)$totalUsers ?></div>
            <a href="<?= url('admin/users') ?>" class="text-primary text-decoration-underline small">Manage user accounts →</a>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0" style="border-radius: 1rem;">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0">Restaurants Overview</h5>
        <a href="<?= url('admin/restaurants') ?>" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Restaurant Name</th>
                        <th>Phone</th>
                        <th>Timezone</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
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
                                <td>#<?= (int)$r['id'] ?></td>
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
                                <td class="text-end">
                                    <a href="<?= url('admin/restaurants/edit?id=' . $r['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
