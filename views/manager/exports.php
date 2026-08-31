<?php
/**
 * Manager CSV Export View Template
 * Matches Android Native App UI 1:1 (Dynamic Data)
 */
declare(strict_types=1);
?>

<div class="app-container">
    <!-- Top App Bar Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="<?= url('manager/dashboard') ?>" class="top-bar-back-btn" title="Back to Dashboard">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h4 class="top-bar-title m-0">CSV Data Exports</h4>
        </div>
        <div class="top-bar-actions">
            <a href="<?= url('manager/reports') ?>" class="top-bar-icon-btn" title="Reports">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </a>
        </div>
    </div>

    <div class="row g-3 justify-content-center">
        <!-- Option 1: Daily Export -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 h-100 p-3" style="border-radius: 18px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
                <div class="fw-bold fs-6 text-dark mb-1 d-flex align-items-center gap-2">
                    <span>📅</span> Daily Export
                </div>
                <p class="text-secondary small mb-3">Download all orders and payment breakdown for a single selected date.</p>
                <form action="<?= url('manager/exports/download') ?>" method="GET">
                    <input type="hidden" name="type" value="daily">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Select Date</label>
                        <input type="date" name="date" class="form-control" value="<?= e($todayDate) ?>" required style="border-radius: 12px;">
                    </div>
                    <button type="submit" class="btn w-100 py-2.5 fw-bold text-white shadow-sm" style="background: var(--brand-orange); border-radius: 12px;">
                        Download Daily CSV
                    </button>
                </form>
            </div>
        </div>

        <!-- Option 2: Monthly Export -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 h-100 p-3" style="border-radius: 18px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
                <div class="fw-bold fs-6 text-dark mb-1 d-flex align-items-center gap-2">
                    <span>📊</span> Monthly Export
                </div>
                <p class="text-secondary small mb-3">Download complete monthly sales reports with item-level snapshots.</p>
                <form action="<?= url('manager/exports/download') ?>" method="GET">
                    <input type="hidden" name="type" value="monthly">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Select Month</label>
                        <input type="month" name="month" class="form-control" value="<?= e($currentMonth) ?>" required style="border-radius: 12px;">
                    </div>
                    <button type="submit" class="btn w-100 py-2.5 fw-bold text-white shadow-sm" style="background: var(--brand-orange); border-radius: 12px;">
                        Download Monthly CSV
                    </button>
                </form>
            </div>
        </div>

        <!-- Option 3: Custom Date Range -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 h-100 p-3" style="border-radius: 18px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
                <div class="fw-bold fs-6 text-dark mb-1 d-flex align-items-center gap-2">
                    <span>🗓️</span> Custom Range Export
                </div>
                <p class="text-secondary small mb-3">Select any custom date range to export customized order data.</p>
                <form action="<?= url('manager/exports/download') ?>" method="GET">
                    <input type="hidden" name="type" value="custom">
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="<?= e($todayDate) ?>" required style="border-radius: 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?= e($todayDate) ?>" required style="border-radius: 12px;">
                    </div>
                    <button type="submit" class="btn w-100 py-2.5 fw-bold text-white shadow-sm" style="background: var(--brand-orange); border-radius: 12px;">
                        Download Custom CSV
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
