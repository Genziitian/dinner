<?php
/**
 * Account & Data Deletion Request Page
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
    <link rel="icon" type="image/png" sizes="32x32" href="<?= asset('icons/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= asset('icons/favicon-16x16.png') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= asset('icons/icon.svg') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= asset('icons/apple-touch-icon.png') ?>">
    <link rel="shortcut icon" href="<?= asset('icons/favicon.ico') ?>">
    <title>Delete Account & Data Deletion · GI ORDER</title>
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
            width: 52px;
            height: 52px;
            background: #0f172a;
            color: #ffffff;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.15);
            margin-bottom: 1.25rem;
        }
        .app-logo-badge svg {
            width: 28px;
            height: 28px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .content-box {
            font-size: 0.975rem;
            line-height: 1.75;
            color: #334155;
        }
        .highlight-notice {
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
            border-left: 4px solid #e11d48;
            border-radius: 0.75rem;
            padding: 1.25rem 1.5rem;
            margin: 1.5rem 0;
        }
        .step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: #0f172a;
            color: #ffffff;
            border-radius: 50%;
            font-size: 0.85rem;
            font-weight: 700;
            margin-right: 0.6rem;
        }
        .btn-email-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            background: #e11d48;
            color: #ffffff;
            font-weight: 600;
            font-size: 1rem;
            padding: 0.85rem 1.75rem;
            border-radius: 0.75rem;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.25);
            transition: all 0.2s ease;
        }
        .btn-email-action:hover {
            background: #be123c;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(225, 29, 72, 0.35);
        }
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.875rem;
            padding: 1.25rem;
            margin-top: 1.25rem;
        }
    </style>
</head>
<body>

<div class="legal-container">
    <div class="legal-card">
        <div class="text-center mb-4">
            <div class="app-logo-badge">
                <svg viewBox="0 0 24 24">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                    <line x1="3" y1="3" x2="21" y2="21" stroke="#e11d48" stroke-width="2.5"></line>
                </svg>
            </div>
            <h1 class="h3 fw-bold text-dark mb-1">Account & Data Deletion Request</h1>
            <p class="text-muted small">GI ORDER — Restaurant Billing & POS Management</p>
        </div>

        <div class="content-box">
            <div class="highlight-notice">
                <h5 class="fw-bold text-danger mb-2" style="color: #be123c !important;">
                    Thank you for visiting. We are sorry that you want to delete your account.
                </h5>
                <p class="mb-0 text-secondary" style="font-size: 0.95rem;">
                    If you wish to delete your GI ORDER account along with your personal and store data, please follow the simple steps outlined below.
                </p>
            </div>

            <h4 class="fw-bold text-dark mt-4 mb-3">How to Request Account Deletion</h4>
            <p>
                To safeguard your account against unauthorized deletion requests, please send an email directly to our support team from your registered email address:
            </p>

            <div class="info-card">
                <div class="d-flex align-items-start mb-3">
                    <span class="step-badge">1</span>
                    <div>
                        <strong>Email Recipient:</strong><br>
                        <a href="mailto:Pay.laxmikant@gmail.com" class="fw-semibold text-primary">Pay.laxmikant@gmail.com</a>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-3">
                    <span class="step-badge">2</span>
                    <div>
                        <strong>Information to Include in Your Email:</strong>
                        <ul class="mt-2 mb-0 ps-3">
                            <li><strong>Your Username</strong> (the username/email used to log into GI ORDER)</li>
                            <li><strong>Restaurant Name / Account Details</strong></li>
                            <li><strong>Cause / Reason for Deletion</strong> (optional, helps us improve our service)</li>
                        </ul>
                    </div>
                </div>

                <div class="d-flex align-items-start">
                    <span class="step-badge">3</span>
                    <div>
                        <strong>Processing Timeframe:</strong><br>
                        <span class="text-dark fw-semibold">We will completely delete your account and associated personal data within 7 days of receiving this information.</span>
                    </div>
                </div>
            </div>

            <div class="text-center my-4">
                <a href="mailto:Pay.laxmikant@gmail.com?subject=Account%20Deletion%20Request%20-%20GI%20ORDER&body=Hello%20GI%20ORDER%20Team,%0A%0AThank%20you%20for%20your%20service.%20I%20would%20like%20to%20request%20the%20permanent%20deletion%20of%20my%20account%20and%20associated%20data.%0A%0AMy%20Account%20Details:%0A-%20Username%20/%20Registered%20Email:%20%0A-%20Restaurant%20Name:%20%0A-%20Reason%20for%20Deletion:%20%0A%0APlease%20confirm%20once%20the%20data%20deletion%20is%20completed.%0A%0AThank%20you." class="btn-email-action">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    Send Account Deletion Email Request
                </a>
            </div>

            <h4 class="fw-bold text-dark mt-4 mb-2">What Data is Deleted?</h4>
            <ul class="ps-3">
                <li><strong>User Profile & Authentication Data:</strong> Your usernames, hashed credentials, contact details, and session tokens.</li>
                <li><strong>Staff & Role Associations:</strong> Cashier and manager accounts linked under your restaurant.</li>
                <li><strong>Personal Identifiers:</strong> Any customer names or phone numbers entered for digital receipt delivery.</li>
            </ul>

            <h4 class="fw-bold text-dark mt-4 mb-2">Data Retention Policy</h4>
            <p class="text-muted small">
                Certain financial aggregate and tax transaction records may be retained in anonymized form strictly to comply with local financial accounting, taxation, and anti-fraud statutory legal obligations as described in our <a href="<?= url('privacy-policy') ?>">Privacy Policy</a>.
            </p>
        </div>
    </div>

    <div class="text-center text-muted small pb-4">
        &copy; <?= date('Y') ?> GI ORDER. All rights reserved. · <a href="<?= url('privacy-policy') ?>" class="text-muted text-decoration-none">Privacy Policy</a>
    </div>
</div>

</body>
</html>
