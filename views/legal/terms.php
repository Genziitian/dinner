<?php
/**
 * Terms and Conditions View Template
 * GI ORDER - POS & Management System
 */
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <link rel="manifest" href="<?= url('manifest.json') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= asset('icons/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= asset('icons/favicon-16x16.png') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= asset('icons/icon.svg') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= asset('icons/apple-touch-icon.png') ?>">
    <link rel="shortcut icon" href="<?= asset('icons/favicon.ico') ?>">
    <title>Terms & Conditions · GI ORDER</title>
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
            max-width: 800px;
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
            font-size: 0.975rem;
            line-height: 1.75;
            color: #334155;
        }
        .content-box h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 1.75rem;
            margin-bottom: 0.75rem;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 0.35rem;
        }
        .content-box h4 {
            font-size: 1.025rem;
            font-weight: 600;
            color: #1e293b;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
        }
        .content-box p {
            margin-bottom: 1rem;
        }
        .content-box ul {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }
        .content-box li {
            margin-bottom: 0.35rem;
        }
        .disclaimer-banner {
            background-color: #fff7ed;
            border: 1.5px solid #fed7aa;
            border-radius: 0.875rem;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.75rem;
            color: #9a3412;
        }
        .disclaimer-banner h4 {
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.025em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            color: #c2410c;
        }
        .disclaimer-banner p {
            margin: 0;
            font-size: 0.925rem;
            line-height: 1.6;
            color: #9a3412;
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
            <span>Return to GI ORDER</span>
        </a>
    </div>

    <div class="legal-card">
        <!-- Brand Header -->
        <div class="text-center mb-4">
            <div class="app-logo-badge">
                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h2 class="fw-bold m-0" style="letter-spacing: -0.5px;">TERMS AND CONDITIONS</h2>
            <p class="text-muted" style="font-size: 0.9rem; margin-top: 0.35rem;">Last updated September 01, 2026</p>
        </div>

        <!-- Disclaimer Banner -->
        <div class="disclaimer-banner">
            <h4>IMPORTANT: PLATFORM NATURE &amp; DATA RESPONSIBILITY DISCLAIMER</h4>
            <p>We are a software platform only. We provide you with digital tools to record and store your billing, inventory, and ledger records, but you are solely responsible for your data. In the event of any data loss, service interruption, or device failure, GI ORDER and its operators are not responsible or liable for any lost data, records, or financial impacts.</p>
        </div>

        <hr class="my-4" style="border-color: #f1f5f9;">

        <!-- Full Terms Content -->
        <div class="content-box">
            <h3>1. AGREEMENT TO TERMS</h3>
            <p>
                These Terms and Conditions constitute a legally binding agreement made between you, whether personally or on behalf of an entity ("you") and GI ORDER ("we," "us," or "our"), concerning your access to and use of the <a href="https://gi-shop.genziitian.in" target="_blank" rel="noopener noreferrer">https://gi-shop.genziitian.in</a> website as well as the GI ORDER: POS &amp; Khata Ledger mobile application (collectively, the "Platform" or "Services").
            </p>
            <p>
                By accessing or using the Services, you agree that you have read, understood, and agree to be bound by all of these Terms and Conditions. If you do not agree with all of these terms, you are expressly prohibited from using the Services and must discontinue use immediately.
            </p>

            <h3>2. NATURE OF SERVICES (PLATFORM ONLY)</h3>
            <p>
                GI ORDER is a technological software utility designed to help small businesses, grocery stores, kirana shops, and individual customers maintain Point of Sale (POS) receipts, digital Khata credit/debit records, product catalogs, and local grocery price discovery.
            </p>
            <p>
                <strong>No Financial Intermediary:</strong> GI ORDER is strictly a record-keeping and billing calculator tool. We do not act as a bank, payment wallet, escrow service, or financial institution.
            </p>
            <p>
                <strong>Offline / Cash Settlements:</strong> All physical exchanges of cash, UPI transfers, credit terms, and repayment agreements take place directly between shopkeepers and customers. GI ORDER is not involved in resolving private debt or credit disagreements.
            </p>

            <h3>3. USER DATA RESPONSIBILITY &amp; NO LIABILITY FOR DATA LOSS</h3>
            <p>
                You are solely responsible for all data, transaction records, inventory details, and customer information that you enter into the Platform.
            </p>
            <p>
                <strong>No Liability for Data Losses:</strong> Although we implement standard cloud synchronization and database safeguards, GI ORDER IS NOT RESPONSIBLE FOR ANY DATA LOSSES WHATSOEVER. This includes data loss caused by server crashes, network outages, unintended overwrites, cyber incidents, device damage, browser cache clearance, or third-party cloud failures.
            </p>
            <p>
                <strong>Independent Record Keeping:</strong> Store owners and users are strongly advised to keep periodic physical or exported accounting backups for critical financial documentation.
            </p>
            <p>
                <strong>No Consequential Damages:</strong> In no event shall GI ORDER, its founders, developers, or affiliates be liable to you or any third party for any direct, indirect, consequential, exemplary, incidental, special, or punitive damages, including lost profit, lost revenue, or loss of data arising from your use of the service.
            </p>

            <h3>4. USER REGISTRATION &amp; SECURITY PIN</h3>
            <p>
                To access the Platform features, you may be required to register with an email, phone number, password, and a 4-digit Security PIN. You agree to:
            </p>
            <ul>
                <li>Provide accurate, current, and complete registration information.</li>
                <li>Maintain the confidentiality of your password and 4-digit PIN.</li>
                <li>Accept full responsibility for all activities and transactions recorded under your account.</li>
                <li>Notify us immediately if you suspect any unauthorized access to your account.</li>
            </ul>

            <h3>5. ACCEPTABLE USE POLICY</h3>
            <p>
                You agree not to access or use the Platform for any purpose other than that for which we make it available. Prohibited activities include:
            </p>
            <ul>
                <li>Entering fake, fraudulent, abusive, or unlawful transaction records.</li>
                <li>Attempting to bypass security mechanisms, reverse-engineer, or tamper with the application source code.</li>
                <li>Using automated scripts, bots, or scrapers to extract platform data or catalog information.</li>
                <li>Using the Platform to harass, defraud, or impersonate another business or individual.</li>
            </ul>

            <h3>6. ACCOUNT DELETION &amp; TERMINATION</h3>
            <p>
                You may terminate your account at any time. To submit an account and personal data deletion request, you can visit our dedicated account deletion portal at <a href="https://gi-shop.genziitian.in/delete" target="_blank" rel="noopener noreferrer">https://gi-shop.genziitian.in/delete</a> or email us directly at <a href="mailto:pay.laxmikant@gmail.com">pay.laxmikant@gmail.com</a>. Account deletions are processed within up to 7 days.
            </p>
            <p>
                We reserve the right to suspend, terminate, or restrict access to any account without notice if we believe you have violated these Terms or engaged in unauthorized activity.
            </p>

            <h3>7. DISCLAIMER OF WARRANTIES</h3>
            <p>
                THE PLATFORM AND SERVICES ARE PROVIDED ON AN "AS-IS" AND "AS-AVAILABLE" BASIS. TO THE MAXIMUM EXTENT PERMITTED BY LAW, WE DISCLAIM ALL WARRANTIES, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT. WE MAKE NO WARRANTIES OR REPRESENTATIONS ABOUT THE ACCURACY OR COMPLETENESS OF THE PLATFORM'S CONTENT OR DATA CALCULATIONS.
            </p>

            <h3>8. MODIFICATIONS TO TERMS</h3>
            <p>
                We reserve the right to modify, amend, or update these Terms and Conditions at any time. Changes become effective immediately upon posting to this page. Continued use of the Services following any updates constitutes acceptance of the modified Terms.
            </p>

            <h3>9. CONTACT INFORMATION</h3>
            <p>
                If you have questions or comments regarding these Terms and Conditions, please contact us at:
            </p>
            <p>
                <strong>GI ORDER</strong><br>
                PATNA, BIHAR 800001, India<br>
                Email: <a href="mailto:pay.laxmikant@gmail.com">pay.laxmikant@gmail.com</a>
            </p>
        </div>
    </div>

    <div class="text-center text-muted small pb-4">
        &copy; 2026 GI ORDER &bull; All Rights Reserved
    </div>
</div>

</body>
</html>
