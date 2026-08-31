<?php
/**
 * Privacy Policy View Template
 * Restaurant Billing & Order Management System
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
    <title>Privacy Policy · DinePOS</title>
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Inter", sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 2rem 1.25rem;
        }
        .legal-container {
            width: 100%;
            max-width: 820px;
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
        .highlight-box {
            background-color: #f8fafc;
            border-left: 4px solid #0f172a;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin: 1.5rem 0;
            font-size: 0.975rem;
            line-height: 1.65;
            color: #1e293b;
        }
        .section-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 1.75rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section-text {
            color: #475569;
            line-height: 1.7;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }
        .role-badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 0.375rem;
            font-size: 0.8rem;
            font-weight: 600;
            margin-right: 0.35rem;
        }
        .role-badge-owner { background: #e0e7ff; color: #3730a3; }
        .role-badge-manager { background: #fef3c7; color: #92400e; }
        .role-badge-cashier { background: #dcfce7; color: #166534; }
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
        <div class="text-muted small">Effective: <?= date('F d, Y') ?></div>
    </div>

    <div class="legal-card">
        <!-- Brand Header -->
        <div class="text-center mb-4">
            <div class="app-logo-badge">
                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h2 class="fw-bold m-0" style="letter-spacing: -0.5px;">Privacy Policy</h2>
            <p class="text-muted" style="font-size: 0.9rem; margin-top: 0.35rem;">DinePOS — Restaurant Billing & Order Management Platform</p>
        </div>

        <hr class="my-4" style="border-color: #f1f5f9;">

        <!-- Core Overview & Mission -->
        <div class="highlight-box">
            <p class="mb-2">
                <strong>Our restaurant management and billing software gives restaurant owners complete control over their daily operations from one secure platform.</strong>
            </p>
            <p class="mb-2">
                Owners can register their restaurant, manage billing, track transactions, monitor sales, review audit logs, and oversee staff activities in real time. The system supports role-based access for Owners, Managers, and Cashiers, ensuring each team member only has access to the tools and information required for their role.
            </p>
            <p class="mb-0">
                From billing and transaction management to staff accountability and operational audits, the platform is designed to make restaurant operations simpler, safer, and easier to manage.
            </p>
        </div>

        <!-- 1. Role-Based Access & Data Isolation -->
        <div class="section-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            1. Role-Based Access & Isolation
        </div>
        <p class="section-text">
            To maintain strict privacy and data boundaries, DinePOS isolates data per restaurant tenant and enforces granular role privileges:
        </p>
        <ul class="section-text ps-3">
            <li class="mb-2"><span class="role-badge role-badge-owner">Owners / Admins</span> Full access to restaurant configuration, billing, reporting, user management, audit logs, and financial analytics.</li>
            <li class="mb-2"><span class="role-badge role-badge-manager">Managers</span> Access to inventory items, daily order management, reporting, cash summaries, and cashier accountability.</li>
            <li class="mb-2"><span class="role-badge role-badge-cashier">Cashiers</span> Limited to order creation, point-of-sale billing, receipt generation, and viewing their own shift summaries.</li>
        </ul>

        <!-- 2. Information We Collect -->
        <div class="section-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            2. Information We Process
        </div>
        <p class="section-text">
            We process the following operational information solely to deliver POS and restaurant management services:
        </p>
        <ul class="section-text ps-3">
            <li class="mb-1"><strong>Account & Authentication:</strong> Usernames, securely hashed passwords (Bcrypt), and assigned roles for staff authorization.</li>
            <li class="mb-1"><strong>Order & Transaction Records:</strong> Items ordered, portion sizes, quantities, totals, timestamps, and payment modes (e.g. Cash, Card, UPI).</li>
            <li class="mb-1"><strong>Operational Audit Logs:</strong> Timestamps and actions performed (such as logins, order updates, price changes) to ensure accountability.</li>
            <li class="mb-1"><strong>Optional Customer Information:</strong> Customer name or contact details if optionally entered by staff strictly for receipt generation. We never sell, rent, or monetize customer data.</li>
        </ul>

        <!-- 3. Security & Data Protection -->
        <div class="section-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            3. Security Measures
        </div>
        <p class="section-text">
            We apply industry-standard security safeguards including encrypted data transmission (HTTPS/TLS), brute-force rate limiting, CSRF protection, secure HTTP-only cookies, and encrypted on-device token storage for mobile applications.
        </p>

        <!-- 4. Third-Party Sharing -->
        <div class="section-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            4. No Third-Party Selling
        </div>
        <p class="section-text">
            DinePOS does not sell, lease, or share your restaurant transaction data, staff credentials, or customer information with advertisers or data brokers.
        </p>

        <!-- 5. Contact & Support -->
        <div class="section-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            5. Contact Information
        </div>
        <p class="section-text mb-0">
            For questions regarding privacy, role configuration, or data administration, please contact your restaurant administrator or the DinePOS support team.
        </p>
    </div>

    <div class="text-center text-muted small pb-4">
        &copy; <?= date('Y') ?> DinePOS Platform. All rights reserved.
    </div>
</div>

</body>
</html>
