<?php
/**
 * Manager Staff Accounts View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold m-0">Staff Accounts</h4>
        <small class="text-muted">Manage Cashiers and Managers for this restaurant</small>
    </div>
    <a href="<?= url('manager/users/create') ?>" class="btn btn-primary btn-sm">+ Add Staff Member</a>
</div>

<div class="card shadow-sm border-0" style="border-radius: 1rem;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No staff accounts found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= e($u['username']) ?></div>
                                    <small class="text-muted">ID: #<?= (int)$u['id'] ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $u['role'] === 'manager' ? 'primary' : 'info' ?>">
                                        <?= strtoupper(e($u['role'])) ?>
                                    </span>
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
                                    <a href="<?= url('manager/users/edit?id=' . $u['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
