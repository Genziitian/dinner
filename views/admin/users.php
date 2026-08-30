<?php
/**
 * Super Admin Global Users List View Template (Mobile-Optimized)
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-sm-center flex-column flex-sm-row gap-2 mb-3">
    <div>
        <h4 class="fw-bold m-0" style="letter-spacing: -0.5px;">Global Users</h4>
        <small class="text-muted">Manage Super Admins, Managers, and Cashiers across all restaurants</small>
    </div>
    <a href="<?= url('admin/users/create') ?>" class="btn btn-primary btn-sm px-3 fw-semibold">+ Create User</a>
</div>

<div class="card shadow-sm border-0 mb-5" style="border-radius: 1rem; overflow: hidden; border: 1px solid #e2e8f0 !important;">
    <div class="card-body p-0">
        <!-- Desktop Table View (>= 768px) -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Username</th>
                        <th>Role</th>
                        <th>Assigned Restaurant</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <p class="mb-2">No users created yet.</p>
                                <a href="<?= url('admin/users/create') ?>" class="btn btn-sm btn-primary">+ Create First User</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?= e($u['username']) ?></div>
                                    <small class="text-muted">ID: #<?= (int)$u['id'] ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $u['role'] === 'superadmin' ? 'dark' : 
                                        ($u['role'] === 'manager' ? 'primary' : 'info')
                                    ?>">
                                        <?= strtoupper(e($u['role'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= !empty($u['restaurant_name']) ? e($u['restaurant_name']) : '<span class="text-muted">Global (All)</span>' ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $u['status'] === 'active' ? 'success' : 'danger' ?>">
                                        <?= ucfirst(e($u['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted"><?= format_date($u['created_at']) ?></small>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= url('admin/users/edit?id=' . $u['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Card View (< 768px) -->
        <div class="d-md-none p-3">
            <?php if (empty($users)): ?>
                <div class="text-center py-4 px-2">
                    <div class="fs-1 mb-2">👤</div>
                    <div class="fw-bold text-dark mb-1">No users created yet</div>
                    <p class="text-muted small mb-3">Add Managers or Cashiers to assign them to restaurants.</p>
                    <a href="<?= url('admin/users/create') ?>" class="btn btn-primary btn-sm w-100 py-2 fw-bold">+ Create First User</a>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($users as $u): ?>
                        <div class="p-3 bg-light rounded-3 border" style="border-color: #e2e8f0 !important;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="text-muted small">#<?= (int)$u['id'] ?></span>
                                    <div class="fw-bold text-dark fs-6"><?= e($u['username']) ?></div>
                                </div>
                                <div class="d-flex gap-1 flex-wrap justify-content-end">
                                    <span class="badge bg-<?= 
                                        $u['role'] === 'superadmin' ? 'dark' : 
                                        ($u['role'] === 'manager' ? 'primary' : 'info')
                                    ?>">
                                        <?= strtoupper(e($u['role'])) ?>
                                    </span>
                                    <span class="badge bg-<?= $u['status'] === 'active' ? 'success' : 'danger' ?>">
                                        <?= ucfirst(e($u['status'])) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="small text-muted mb-2">
                                <strong>Restaurant:</strong> <?= !empty($u['restaurant_name']) ? e($u['restaurant_name']) : 'Global (All)' ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <small class="text-muted">Created: <?= format_date($u['created_at']) ?></small>
                                <a href="<?= url('admin/users/edit?id=' . $u['id']) ?>" class="btn btn-sm btn-outline-secondary px-3 py-1 fw-semibold">Edit User</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
