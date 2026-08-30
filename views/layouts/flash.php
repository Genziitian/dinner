<?php
/**
 * Flash Message Layout Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
$flash = get_flash();
?>
<?php if ($flash): ?>
    <?php
        $type = $flash['type'] === 'error' ? 'danger' : $flash['type'];
        $icon = match($type) {
            'danger' => '⚠️',
            'warning' => '⚡',
            'success' => '✓',
            default => 'ℹ️',
        };
    ?>
    <div class="alert alert-<?= e($type) ?> d-flex align-items-center justify-content-between p-3 mb-3 shadow-sm" role="alert" style="border-radius: 0.75rem; font-size: 0.9rem; font-weight: 500;">
        <div class="d-flex align-items-center gap-2">
            <span style="font-size: 1.1rem; line-height: 1;"><?= $icon ?></span>
            <div><?= e($flash['message']) ?></div>
        </div>
        <button type="button" class="btn-close" style="font-size: 1.1rem; border: none; background: transparent; cursor: pointer; opacity: 0.7;" onclick="this.closest('.alert').remove()">&times;</button>
    </div>
<?php endif; ?>
