<?php
/**
 * Super Admin Restaurants List View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold m-0">Restaurants</h4>
        <small class="text-muted">Create, configure, or activate/deactivate tenant restaurants</small>
    </div>
    <a href="<?= url('admin/restaurants/create') ?>" class="btn btn-primary btn-sm">+ Create Restaurant</a>
</div>

<div class="card shadow-sm border-0" style="border-radius: 1rem;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Restaurant</th>
                        <th>Contact</th>
                        <th>Timezone</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
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
                                <td>#<?= (int)$r['id'] ?></td>
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
                                <td class="text-end">
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
    </div>
</div>
