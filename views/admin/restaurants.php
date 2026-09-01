<?php
/**
 * Modern Super Admin Restaurants Directory View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="admin-container">
    <!-- Header Section -->
    <div class="admin-header-box">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h2 class="admin-page-title">Restaurants Directory</h2>
                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.85rem;">
                    <?= count($restaurants) ?>
                </span>
            </div>
            <p class="admin-page-subtitle">Manage tenant restaurants, operational status, timezones, and billing metadata</p>
        </div>
        <div>
            <a href="<?= url('admin/restaurants/create') ?>" class="admin-btn-primary">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Restaurant</span>
            </a>
        </div>
    </div>

    <!-- Search / Filter Bar -->
    <div class="admin-card mb-4">
        <div class="p-3">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input 
                            type="text" 
                            id="restaurantSearchInput" 
                            class="form-control border-start-0 ps-0" 
                            placeholder="Search by restaurant name, phone, or address..."
                            onkeyup="filterRestaurants()"
                        >
                    </div>
                </div>
                <div class="col-12 col-md-6 text-md-end text-muted small">
                    Showing <span id="filteredCount" class="fw-bold text-dark"><?= count($restaurants) ?></span> of <?= count($restaurants) ?> outlets
                </div>
            </div>
        </div>
    </div>

    <!-- Restaurants Cards Grid -->
    <?php if (empty($restaurants)): ?>
        <div class="admin-card p-5 text-center">
            <div class="mb-3">
                <div class="brand-icon-box mx-auto" style="width: 60px; height: 60px; border-radius: 16px;">
                    <svg width="30" height="30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <h5 class="fw-bold text-dark mb-1">No restaurants configured yet</h5>
            <p class="text-muted small mb-3">Add your first restaurant outlet to begin setting up menus, tables, and staff accounts.</p>
            <a href="<?= url('admin/restaurants/create') ?>" class="admin-btn-primary">+ Add First Restaurant</a>
        </div>
    <?php else: ?>
        <div class="row g-3" id="restaurantsContainer">
            <?php foreach ($restaurants as $r): ?>
                <div class="col-12 col-md-6 col-xl-4 restaurant-item" data-search="<?= strtolower(e($r['name'] . ' ' . ($r['phone'] ?? '') . ' ' . ($r['address'] ?? ''))) ?>">
                    <div class="restaurant-grid-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="restaurant-avatar">
                                    <?= strtoupper(substr($r['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark m-0 fs-6"><?= e($r['name']) ?></h5>
                                    <small class="text-muted">Branch ID: #<?= (int)$r['id'] ?></small>
                                </div>
                            </div>
                            <span class="admin-pill-status <?= $r['status'] === 'active' ? 'active' : 'inactive' ?>">
                                <span class="dot"></span>
                                <?= ucfirst(e($r['status'])) ?>
                            </span>
                        </div>

                        <div class="d-flex flex-column gap-2 mb-3 flex-grow-1" style="font-size: 0.88rem; color: #334155;">
                            <div class="d-flex align-items-center gap-2">
                                <svg width="16" height="16" fill="none" stroke="#64748b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span><?= !empty($r['phone']) ? e($r['phone']) : '<span class="text-muted">No phone set</span>' ?></span>
                            </div>

                            <div class="d-flex align-items-start gap-2">
                                <svg width="16" height="16" fill="none" stroke="#64748b" viewBox="0 0 24 24" class="mt-0.5 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-truncate-2"><?= !empty($r['address']) ? e($r['address']) : '<span class="text-muted">No address specified</span>' ?></span>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <svg width="16" height="16" fill="none" stroke="#64748b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="badge bg-light text-dark border fw-medium px-2 py-0.5">
                                    <code><?= e($r['timezone']) ?></code>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3 border-top">
                            <a href="<?= url('admin/restaurants/edit?id=' . $r['id']) ?>" class="admin-btn-secondary flex-grow-1 text-center justify-content-center" style="padding: 0.45rem 0.75rem; font-size: 0.85rem;">
                                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit Details
                            </a>
                            <form action="<?= url('admin/restaurants/toggle') ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to <?= $r['status'] === 'active' ? 'deactivate' : 'activate' ?> this restaurant?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <button type="submit" class="btn btn-sm <?= $r['status'] === 'active' ? 'btn-outline-danger' : 'btn-outline-success' ?> py-2 px-3 fw-semibold rounded-3">
                                    <?= $r['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function filterRestaurants() {
    const q = document.getElementById('restaurantSearchInput').value.toLowerCase().trim();
    const items = document.querySelectorAll('.restaurant-item');
    let count = 0;

    items.forEach(el => {
        const text = el.getAttribute('data-search') || '';
        if (q === '' || text.includes(q)) {
            el.style.display = '';
            count++;
        } else {
            el.style.display = 'none';
        }
    });

    document.getElementById('filteredCount').innerText = count;
}
</script>
