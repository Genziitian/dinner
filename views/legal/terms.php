<?php
/**
 * Terms and Conditions View Template
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
    <title>Terms & Conditions · DinePOS</title>
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
        .highlight-box-warning {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin: 1.5rem 0;
            font-size: 0.975rem;
            line-height: 1.65;
            color: #92400e;
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
                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h2 class="fw-bold m-0" style="letter-spacing: -0.5px;">Terms and Conditions</h2>
            <p class="text-muted" style="font-size: 0.9rem; margin-top: 0.35rem;">DinePOS — Restaurant Billing & Order Management Platform</p>
        </div>

        <hr class="my-4" style="border-color: #f1f5f9;">

        <!-- Platform & Data Responsibility Notice -->
        <div class="highlight-box-warning">
            <div class="fw-bold mb-1" style="font-size: 1.05rem;">Important Platform Notice:</div>
            <p class="mb-0">
                <strong>We are a platform only. We provide you tools and software to store, manage, and process your restaurant records, but you are solely responsible for your data. If data loss happens, we are not responsible for it.</strong>
            </p>
        </div>

        <!-- 1. Platform Scope -->
        <div class="section-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            1. Platform Services & Scope
        </div>
        <p class="section-text">
            DinePOS provides point-of-sale billing, order processing, menu management, and operational reporting tools for restaurant businesses. By accessing or continuing to use DinePOS on web browsers, desktop, or mobile applications, you accept and agree to be bound by these Terms and Conditions.
        </p>

        <!-- 2. Data Ownership & Responsibility -->
        <div class="section-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7zm0 4h16M8 4v4m8-4v4"/></svg>
            2. Data Responsibility & Backups
        </div>
        <p class="section-text">
            You retain ownership of all data entered into your account, including menu catalogs, sales figures, and billing records. You acknowledge and agree that:
        </p>
        <ul class="section-text ps-3">
            <li class="mb-1">You are responsible for regularly exporting and creating backups of your reports and transaction history using the provided export tools.</li>
            <li class="mb-1">DinePOS operates as a software management utility and shall not be held liable for accidental deletion, data corruption, hardware failure, or server interruption.</li>
            <li class="mb-1">You must ensure that data entered into the system conforms with applicable local business and tax regulations.</li>
        </ul>

        <!-- 3. Account Security & Role Management -->
        <div class="section-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            3. Account Security & Roles
        </div>
        <p class="section-text">
            Each user account is assigned a specific role (Super Admin, Restaurant Owner, Manager, or Cashier). You are responsible for:
        </p>
        <ul class="section-text ps-3">
            <li class="mb-1">Maintaining the confidentiality of all staff usernames and passwords.</li>
            <li class="mb-1">Promptly revoking or deactivating access for departed employees or compromised credentials.</li>
            <li class="mb-1">All activities, orders, voids, discounts, or modifications conducted under your restaurant's user accounts.</li>
        </ul>

        <!-- 4. Limitation of Liability -->
        <div class="section-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            4. Limitation of Liability
        </div>
        <p class="section-text">
            To the maximum extent permitted by applicable law, DinePOS and its operators shall not be liable for any direct, indirect, incidental, special, or consequential damages resulting from:
        </p>
        <ul class="section-text ps-3">
            <li class="mb-1">Loss of data, lost profits, revenue disruptions, or business interruptions.</li>
            <li class="mb-1">Network downtime, server outages, or hardware incompatibilities.</li>
            <li class="mb-1">Unauthorized access or alterations to your data resulting from credential compromise on your end.</li>
        </ul>

        <!-- 5. Service Availability & Modifications -->
        <div class="section-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            5. Modifications & Updates
        </div>
        <p class="section-text">
            We reserve the right to improve, modify, or update software features and these terms as needed. Continued use of the platform constitutes acceptance of any updated terms.
        </p>

        <!-- 6. Contact -->
        <div class="section-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            6. Contact Support
        </div>
        <p class="section-text mb-0">
            For questions or inquiries regarding these Terms and Conditions, please contact your restaurant administrator or the DinePOS support team.
        </p>
    </div>

    <div class="text-center text-muted small pb-4">
        &copy; <?= date('Y') ?> DinePOS Platform. All rights reserved.
    </div>
</div>

</body>
</html>
