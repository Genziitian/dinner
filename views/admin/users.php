<?php
/**
 * Super Admin Global Users List View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold m-0">Global Users</h4>
        <small class="text-muted">Manage Super Admins, Managers, and Cashiers across all restaurants</small>
    </div>
    <a href="<?= url('admin/users/create') ?>" class="btn btn-primary btn-sm">+ Create User</a>
</div>

<div class="card shadow-sm border-0" style="border-radius: 1rem;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Assigned Restaurant</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
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
                                <td>
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
                                <td class="text-end">
                                    <a href="<?= url('admin/users/edit?id=' . $u['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
