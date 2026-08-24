<?php
/**
 * Flash Message Layout Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
$flash = get_flash();
?>
<?php if ($flash): ?>
<div class="alert alert-<?= e($flash['type'] === 'error' ? 'danger' : $flash['type']) ?> alert-dismissible fade show shadow-sm mb-3" role="alert">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <?= e($flash['message']) ?>
        </div>
        <button type="button" class="btn-close" onclick="this.closest('.alert').remove()">&times;</button>
    </div>
</div>
<?php endif; ?>
