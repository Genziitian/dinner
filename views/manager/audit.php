<?php
/**
 * Manager Audit Trail Log View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold m-0">Audit Trail</h4>
        <small class="text-muted">Immutable log of billing actions, edits, logins, and settings changes</small>
    </div>
</div>

<div class="card shadow-sm border-0" style="border-radius: 1rem;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Action</th>
                        <th>User</th>
                        <th>Entity</th>
                        <th>Details / Metadata</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No audit records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <div class="small fw-semibold"><?= format_date($log['created_at']) ?></div>
                                    <small class="text-muted"><?= format_time($log['created_at']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        str_contains($log['action'], 'DELETE') ? 'danger' : 
                                        (str_contains($log['action'], 'EDIT') ? 'warning' : 
                                        (str_contains($log['action'], 'LOGIN') ? 'info' : 'primary'))
                                    ?>">
                                        <?= e($log['action']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= e($log['username'] ?? 'System') ?></div>
                                    <small class="text-muted"><?= e($log['role'] ?? '') ?></small>
                                </td>
                                <td>
                                    <span class="text-uppercase small fw-bold text-muted"><?= e($log['entity_type']) ?></span>
                                    <?php if (!empty($log['entity_id'])): ?>
                                        <small class="text-muted">#<?= (int)$log['entity_id'] ?></small>
                                    <?php endif; ?>
                                </td>
                                <td style="max-width: 300px;" class="small text-truncate" title="<?= e($log['metadata'] ?? '') ?>">
                                    <?= e($log['metadata'] ?? '-') ?>
                                </td>
                                <td>
                                    <small class="text-muted"><?= e($log['ip_address'] ?? '127.0.0.1') ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
