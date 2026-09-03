<?php
/**
 * Android Native Design System Footer Layout Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
$currentUser = current_user();
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
</main>

<?php if ($currentUser): ?>
<!-- Android Material 3 Bottom Navigation Dock -->
<div class="app-bottom-nav">
    <div class="app-bottom-nav-surface">
        <?php if (($currentUser['shop_type'] ?? '') === 'mill' && $currentUser['role'] !== User::ROLE_SUPERADMIN): ?>
            <!-- Mill Navigation: Dashboard | Orders | [Center New Order] | Customers | Rates -->
            <a href="<?= url('mill/dashboard') ?>" class="app-nav-item <?= (str_contains($currentUri, 'mill/dashboard') || $currentUri === '/') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <span class="nav-label"><?= __t('nav_dashboard') ?></span>
            </a>

            <a href="<?= url('mill/orders') ?>" class="app-nav-item <?= (str_contains($currentUri, 'mill/orders') && !str_contains($currentUri, 'mill/orders/new')) ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="nav-label"><?= __t('nav_orders') ?></span>
            </a>

            <!-- Elevated Center New Order Button -->
            <div class="app-nav-fab-wrap">
                <a href="<?= url('mill/orders/new') ?>" class="app-nav-fab-btn text-decoration-none d-flex align-items-center justify-content-center" title="New Order">
                    <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </a>
            </div>

            <a href="<?= url('mill/customers') ?>" class="app-nav-item <?= str_contains($currentUri, 'mill/customers') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="nav-label"><?= __t('nav_customers') ?></span>
            </a>

            <a href="<?= url('mill/services') ?>" class="app-nav-item <?= str_contains($currentUri, 'mill/services') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="nav-label"><?= __t('nav_services') ?></span>
            </a>
        <?php elseif ($currentUser['role'] === User::ROLE_CASHIER): ?>
            <!-- Cashier Navigation: POS | Orders | [Center QR Scanner] | Shift | Profile -->
            <a href="<?= url('cashier/order') ?>" class="app-nav-item <?= (str_contains($currentUri, 'cashier/order') || $currentUri === '/') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="nav-label">POS</span>
            </a>

            <a href="<?= url('manager/orders') ?>" class="app-nav-item <?= str_contains($currentUri, 'manager/orders') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="nav-label">Orders</span>
            </a>

            <!-- Elevated Center QR Scanner Button -->
            <div class="app-nav-fab-wrap">
                <button type="button" class="app-nav-fab-btn border-0" onclick="openQrLookupModal()" title="Scan Receipt QR Code">
                    <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </button>
            </div>

            <a href="<?= url('cashier/summary') ?>" class="app-nav-item <?= str_contains($currentUri, 'cashier/summary') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="nav-label">Shift</span>
            </a>

            <a href="<?= url('manager/settings') ?>" class="app-nav-item <?= (str_contains($currentUri, 'manager/settings') || str_contains($currentUri, 'profile')) ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span class="nav-label">Profile</span>
            </a>

        <?php elseif ($currentUser['role'] === User::ROLE_MANAGER): ?>
            <!-- Manager Navigation: Home | Orders | [Center QR Scanner] | Menu | Profile -->
            <a href="<?= url('manager/dashboard') ?>" class="app-nav-item <?= (str_contains($currentUri, 'manager/dashboard') || $currentUri === '/') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <span class="nav-label">Home</span>
            </a>

            <a href="<?= url('manager/orders') ?>" class="app-nav-item <?= str_contains($currentUri, 'manager/orders') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="nav-label">Orders</span>
            </a>

            <!-- Elevated Center QR Scanner Button -->
            <div class="app-nav-fab-wrap">
                <button type="button" class="app-nav-fab-btn border-0" onclick="openQrLookupModal()" title="Scan Receipt QR Code">
                    <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </button>
            </div>

            <a href="<?= url('manager/items') ?>" class="app-nav-item <?= str_contains($currentUri, 'manager/items') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="nav-label">Menu</span>
            </a>

            <a href="<?= url('manager/settings') ?>" class="app-nav-item <?= (str_contains($currentUri, 'manager/settings') || str_contains($currentUri, 'profile')) ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span class="nav-label">Profile</span>
            </a>

        <?php elseif ($currentUser['role'] === User::ROLE_SUPERADMIN): ?>
            <!-- Super Admin Navigation: Admin | Restaurants | Users | Profile -->
            <a href="<?= url('admin/dashboard') ?>" class="app-nav-item <?= (str_contains($currentUri, 'admin/dashboard') || $currentUri === '/') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </div>
                <span class="nav-label">Admin</span>
            </a>
            <a href="<?= url('admin/restaurants') ?>" class="app-nav-item <?= str_contains($currentUri, 'admin/restaurants') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="nav-label">Restaurants</span>
            </a>
            <a href="<?= url('admin/users') ?>" class="app-nav-item <?= str_contains($currentUri, 'admin/users') ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="nav-label">Users</span>
            </a>
            <a href="<?= url('manager/settings') ?>" class="app-nav-item <?= (str_contains($currentUri, 'manager/settings') || str_contains($currentUri, 'profile')) ? 'active' : '' ?>">
                <div class="nav-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span class="nav-label">Profile</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Universal Live Camera QR Code Scanner & Lookup Modal -->
<div class="modal fade" id="cameraQrLookupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content modal-content-android">
            <div class="modal-header modal-header-android">
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size: 1.25rem;">📷</span>
                    <h5 class="modal-title modal-title-android m-0">Scan Receipt QR</h5>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-sm btn-light border p-1 rounded-circle" onclick="DineQrScanner.toggleCamera()" title="Switch Front/Back Camera" style="width: 32px; height: 32px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <div class="modal-body p-3">
                <!-- Camera Viewport Container -->
                <div class="camera-viewport-wrap" style="position: relative; width: 100%; height: 260px; background: #0f172a; border-radius: 16px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                    <!-- Video Feed -->
                    <video id="qrCameraVideo" playsinline autoplay muted style="width: 100%; height: 100%; object-fit: cover;"></video>

                    <!-- Loading Placeholder -->
                    <div id="qrScannerLoading" style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #0f172a; color: #ffffff;">
                        <div class="spinner-border text-warning mb-2" role="status"></div>
                        <div class="small fw-bold">Starting Camera Feed...</div>
                    </div>

                    <!-- Error Notice Placeholder -->
                    <div id="qrScannerError" style="position: absolute; inset: 0; display: none; padding: 1.5rem; text-align: center; background: #0f172a; color: #ffffff;"></div>

                    <!-- Animated Scanner Reticle Overlay -->
                    <div id="qrScannerOverlay" style="position: absolute; inset: 0; pointer-events: none; display: none;">
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 180px; height: 180px; border: 2.5px solid #ea580c; border-radius: 16px; box-shadow: 0 0 0 9999px rgba(0,0,0,0.5);">
                            <!-- Laser line -->
                            <div class="qr-laser-line" style="position: absolute; width: 100%; height: 2px; background: #ea580c; box-shadow: 0 0 8px #ea580c; animation: scanLine 2s infinite alternate;"></div>
                        </div>
                    </div>
                </div>

                <div id="qrScannerStatus" class="text-center my-2">
                    <small class="text-secondary">Point camera directly at the receipt QR code</small>
                </div>

                <!-- Hidden Native Photo Input for instantaneous snapshot fallback -->
                <input type="file" id="qrCameraFileInput" accept="image/*" capture="environment" style="display:none;" onchange="DineQrScanner.handleFileInput(this)">

                <div class="d-grid gap-2 mb-3">
                    <button type="button" class="btn btn-sm btn-light border fw-bold py-2" style="border-radius: 12px;" onclick="DineQrScanner.triggerPhotoCapture()">
                        📸 Snap / Upload QR Photo
                    </button>
                </div>

                <!-- Manual Input Fallback -->
                <div class="p-3 bg-light rounded-3 border">
                    <label class="form-label small fw-bold text-dark mb-1">Or Enter Order # / Receipt Token:</label>
                    <div class="d-flex gap-2">
                        <input 
                            type="text" 
                            id="manualQrInput" 
                            class="form-control" 
                            placeholder="e.g. 7, #7, or paste token" 
                            style="border-radius: 12px;"
                            onkeydown="if(event.key==='Enter'){event.preventDefault(); DineQrScanner.submitManual();}"
                        >
                        <button type="button" class="btn fw-bold text-white px-3" style="background: var(--brand-orange); border-radius: 12px; white-space: nowrap;" onclick="DineQrScanner.submitManual()">
                            Lookup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes scanLine {
  0% { top: 5%; }
  100% { top: 95%; }
}
</style>
<?php endif; ?>

<!-- Core JavaScript -->
<script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('js/jsqr.min.js') ?>"></script>
<script src="<?= asset('js/qrcode.min.js') ?>"></script>
<script src="<?= asset('js/qr-scanner.js?v=' . filemtime(ROOT_PATH . '/public/assets/js/qr-scanner.js')) ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>

</body>
</html>
