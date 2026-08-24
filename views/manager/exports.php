<?php
/**
 * Manager CSV Export View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold m-0">CSV Data Exports</h4>
        <small class="text-muted">Export detailed order logs with financial summaries to CSV/Excel</small>
    </div>
    <a href="<?= url('manager/reports') ?>" class="btn btn-outline-secondary btn-sm">← View Reports</a>
</div>

<div class="row g-4 justify-content-center">
    <!-- Option 1: Daily Export -->
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 1rem;">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold m-0 text-dark">Daily Export</h5>
            </div>
            <div class="card-body p-3">
                <p class="text-muted small">Download all orders and payment breakdown for a single selected date.</p>
                <form action="<?= url('manager/exports/download') ?>" method="GET">
                    <input type="hidden" name="type" value="daily">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Select Date</label>
                        <input type="date" name="date" class="form-control" value="<?= e($todayDate) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download Daily CSV
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Option 2: Monthly Export -->
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 1rem;">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold m-0 text-dark">Monthly Export</h5>
            </div>
            <div class="card-body p-3">
                <p class="text-muted small">Download complete monthly sales reports with item-level snapshots.</p>
                <form action="<?= url('manager/exports/download') ?>" method="GET">
                    <input type="hidden" name="type" value="monthly">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Select Month</label>
                        <input type="month" name="month" class="form-control" value="<?= e($currentMonth) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download Monthly CSV
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Option 3: Custom Date Range -->
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 1rem;">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold m-0 text-dark">Custom Range Export</h5>
            </div>
            <div class="card-body p-3">
                <p class="text-muted small">Select any custom date range to export customized order data.</p>
                <form action="<?= url('manager/exports/download') ?>" method="GET">
                    <input type="hidden" name="type" value="custom">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="<?= e($todayDate) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?= e($todayDate) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download Range CSV
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
