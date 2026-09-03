<?php
/**
 * Super Admin Restaurants Directory View Template
 * GI ORDER POS
 */
declare(strict_types=1);

$activeCount = count(array_filter($restaurants, fn($r) => ($r['status'] ?? '') === 'active'));
$inactiveCount = count($restaurants) - $activeCount;
?>

<div class="admin-container">
    <!-- Header Section -->
    <div class="admin-header-box">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h2 class="admin-page-title">Restaurants</h2>
                <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill fw-bold" style="font-size: 0.8rem;">
                    <?= count($restaurants) ?>
                </span>
            </div>
            <p class="admin-page-subtitle">Tenant branches & outlet configurations</p>
        </div>
        <div>
            <a href="<?= url('admin/restaurants/create') ?>" class="admin-btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Restaurant</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="admin-card mb-3 mb-md-4">
        <div class="p-2.5 p-md-3">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5 pe-1.5">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input 
                            type="text" 
                            id="restaurantSearchInput" 
                            class="form-control border-start-0 ps-1" 
                            style="font-size: 0.88rem;"
                            placeholder="Search by name, phone, or location..."
                            onkeyup="filterRestaurants()"
                        >
                    </div>
                </div>
                <div class="col-12 col-md-6 d-flex justify-content-md-end align-items-center gap-1.5">
                    <div class="admin-filters-scroll">
                        <button type="button" class="admin-filter-chip active" data-status="all" onclick="setStatusFilter('all', this)">
                            All (<?= count($restaurants) ?>)
                        </button>
                        <button type="button" class="admin-filter-chip" data-status="active" onclick="setStatusFilter('active', this)">
                            Active (<?= $activeCount ?>)
                        </button>
                        <button type="button" class="admin-filter-chip" data-status="inactive" onclick="setStatusFilter('inactive', this)">
                            Inactive (<?= $inactiveCount ?>)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Restaurants Cards Grid -->
    <?php if (empty($restaurants)): ?>
        <div class="admin-card p-4 text-center">
            <div class="mb-2">
                <div class="brand-icon-box mx-auto" style="width: 48px; height: 48px; border-radius: 12px;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <h6 class="fw-bold text-dark mb-1">No restaurants configured yet</h6>
            <p class="text-muted small mb-3">Add your first restaurant outlet to begin setting up menus and staff.</p>
            <a href="<?= url('admin/restaurants/create') ?>" class="admin-btn-primary">+ Add Restaurant</a>
        </div>
    <?php else: ?>
        <div class="row g-2 g-md-3" id="restaurantsContainer">
            <?php foreach ($restaurants as $r): ?>
                <div class="col-12 col-md-6 col-xl-4 restaurant-item" data-status="<?= e($r['status']) ?>" data-search="<?= strtolower(e($r['name'] . ' ' . ($r['phone'] ?? '') . ' ' . ($r['address'] ?? ''))) ?>">
                    <div class="restaurant-grid-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="restaurant-avatar">
                                    <?= strtoupper(substr($r['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <h5 class="fw-bold text-dark m-0" style="font-size: 0.95rem;"><?= e($r['name']) ?></h5>
                                        <span class="badge <?= ($r['shop_type'] ?? 'restaurant') === 'mill' ? 'bg-warning-subtle text-dark border-warning' : 'bg-light text-secondary border' ?> px-1.5 py-0.5" style="font-size: 0.68rem; font-weight: 600;">
                                            <?= ($r['shop_type'] ?? 'restaurant') === 'mill' ? 'MILL' : 'RESTAURANT' ?>
                                        </span>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.74rem;">ID #<?= (int)$r['id'] ?></small>
                                </div>
                            </div>
                            <span class="admin-pill-status <?= $r['status'] === 'active' ? 'active' : 'inactive' ?>">
                                <span class="dot"></span>
                                <?= ucfirst(e($r['status'])) ?>
                            </span>
                        </div>

                        <div class="d-flex flex-column gap-1.5 my-2 flex-grow-1" style="font-size: 0.84rem; color: #334155;">
                            <div class="d-flex align-items-center gap-1.5">
                                <svg width="14" height="14" fill="none" stroke="#64748b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span><?= !empty($r['phone']) ? e($r['phone']) : '<span class="text-muted">No phone</span>' ?></span>
                            </div>

                            <div class="d-flex align-items-start gap-1.5">
                                <svg width="14" height="14" fill="none" stroke="#64748b" viewBox="0 0 24 24" class="mt-0.5 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-truncate-2"><?= !empty($r['address']) ? e($r['address']) : '<span class="text-muted">No address</span>' ?></span>
                            </div>

                            <div class="d-flex align-items-center gap-1.5 mt-1">
                                <svg width="14" height="14" fill="none" stroke="#64748b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 0.72rem; font-family: monospace;">
                                    <?= e($r['timezone']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-1.5 pt-2 border-top mt-auto">
                            <a href="<?= url('admin/restaurants/edit?id=' . $r['id']) ?>" class="admin-btn-secondary flex-grow-1 text-center justify-content-center" style="padding: 0.4rem; font-size: 0.8rem;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </a>
                            <form action="<?= url('admin/restaurants/toggle') ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to <?= $r['status'] === 'active' ? 'deactivate' : 'activate' ?> this restaurant?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <button type="submit" class="btn btn-sm <?= $r['status'] === 'active' ? 'btn-outline-danger' : 'btn-outline-success' ?> py-1 px-2.5 fw-semibold rounded-2" style="font-size: 0.78rem;">
                                    <?= $r['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div id="noRestaurantsMatch" class="admin-card p-4 text-center text-muted small" style="display: none;">
            No restaurants match your search.
        </div>
    <?php endif; ?>
</div>

<script>
let currentStatusFilter = 'all';

function setStatusFilter(status, btn) {
    currentStatusFilter = status;
    document.querySelectorAll('.admin-filter-chip').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filterRestaurants();
}

function filterRestaurants() {
    const q = (document.getElementById('restaurantSearchInput')?.value || '').toLowerCase().trim();
    const items = document.querySelectorAll('.restaurant-item');
    let count = 0;

    items.forEach(el => {
        const text = el.getAttribute('data-search') || '';
        const status = el.getAttribute('data-status') || '';
        const matchesQuery = (q === '' || text.includes(q));
        const matchesStatus = (currentStatusFilter === 'all' || status === currentStatusFilter);

        if (matchesQuery && matchesStatus) {
            el.style.display = '';
            count++;
        } else {
            el.style.display = 'none';
        }
    });

    const noMatch = document.getElementById('noRestaurantsMatch');
    if (noMatch) {
        noMatch.style.display = (count === 0 && items.length > 0) ? '' : 'none';
    }
}
</script>
