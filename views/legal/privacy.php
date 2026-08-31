<?php
/**
 * Privacy Policy View Template
 * GI ORDER - Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f172a">
    <link rel="manifest" href="<?= url('manifest.json') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= asset('icons/icon.svg') ?>">
    <title>Privacy Policy · GI ORDER</title>
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Inter", sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 2.5rem 1.25rem;
        }
        .legal-container {
            width: 100%;
            max-width: 760px;
            margin: 0 auto;
        }
        .legal-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            padding: 2.5rem 2.25rem;
            margin-bottom: 2rem;
        }
        @media (max-width: 576px) {
            .legal-card {
                padding: 1.75rem 1.25rem;
            }
        }
        .app-logo-badge {
            width: 48px;
            height: 48px;
            background: #0f172a;
            color: #ffffff;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
            margin-bottom: 1rem;
        }
        .content-box {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #334155;
        }
        .content-box p {
            margin-bottom: 1.25rem;
        }
        .content-box p:last-child {
            margin-bottom: 0;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.75rem;
            color: #0f172a;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .btn-back:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }
    </style>
</head>
<body>

<div class="legal-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= url('login') ?>" class="btn-back">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Sign In</span>
        </a>
    </div>

    <div class="legal-card">
        <!-- Brand Header -->
        <div class="text-center mb-4">
            <div class="app-logo-badge">
                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h2 class="fw-bold m-0" style="letter-spacing: -0.5px;">Privacy Policy</h2>
            <p class="text-muted" style="font-size: 0.9rem; margin-top: 0.35rem;">GI ORDER</p>
        </div>

        <hr class="my-4" style="border-color: #f1f5f9;">

        <!-- Exact Privacy Policy as given -->
        <div class="content-box">
            <p>
                Our restaurant management and billing software gives restaurant owners complete control over their daily operations from one secure platform.
            </p>
            <p>
                Owners can register their restaurant, manage billing, track transactions, monitor sales, review audit logs, and oversee staff activities in real time. The system supports role-based access for Owners, Managers, and Cashiers, ensuring each team member only has access to the tools and information required for their role.
            </p>
            <p>
                From billing and transaction management to staff accountability and operational audits, the platform is designed to make restaurant operations simpler, safer, and easier to manage.
            </p>
        </div>
    </div>

    <div class="text-center text-muted small pb-4">
        &copy; <?= date('Y') ?> GI ORDER. All rights reserved.
    </div>
</div>

</body>
</html>
