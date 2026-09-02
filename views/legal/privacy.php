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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <link rel="manifest" href="<?= url('manifest.json') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= asset('icons/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= asset('icons/favicon-16x16.png') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= asset('icons/icon.svg') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= asset('icons/apple-touch-icon.png') ?>">
    <link rel="shortcut icon" href="<?= asset('icons/favicon.ico') ?>">
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
            max-width: 860px;
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
            <h2 class="fw-bold m-0" style="letter-spacing: -0.5px;">PRIVACY POLICY</h2>
            <p class="text-muted" style="font-size: 0.9rem; margin-top: 0.35rem;">Last updated August 30, 2026</p>
        </div>

        <hr class="my-4" style="border-color: #f1f5f9;">

        <!-- Full Exact Privacy Policy -->
        <div class="content-box">
            <p>
                This Privacy Notice for GI ORDER ("we," "us," or "our"), describes how and why we might access, collect, store, use, and/or share ("process") your personal information when you use our services ("Services"), including when you:
            </p>
            <ul>
                <li>Visit our website at <a href="https://dinner.genziitian.in/" target="_blank" rel="noopener noreferrer">https://dinner.genziitian.in/</a> or any website of ours that links to this Privacy Notice</li>
                <li>Download and use our mobile application (GI ORDER), or any other application of ours that links to this Privacy Notice</li>
                <li>Use Restraunt Billing Software. Our restaurant management and billing software gives restaurant owners complete control over their daily operations from one secure platform. Owners can register their restaurant, manage billing, track transactions, monitor sales, review audit logs, and oversee staff activities in real time. The system supports role-based access for Owners, Managers, and Cashiers, ensuring each team member only has access to the tools and information required for their role. From billing and transaction management to staff accountability and operational audits, the platform is designed to make restaurant operations simpler, safer, and easier to manage.</li>
                <li>Engage with us in other related ways, including any marketing or events</li>
            </ul>
            <p>
                <strong>Questions or concerns?</strong> Reading this Privacy Notice will help you understand your privacy rights and choices. We are responsible for making decisions about how your personal information is processed. If you do not agree with our policies and practices, please do not use our Services. If you still have any questions or concerns, please contact us at <a href="mailto:pay.laxmikant@gmail.com">pay.laxmikant@gmail.com</a>.
            </p>

            <h3>SUMMARY OF KEY POINTS</h3>
            <p>
                This summary provides key points from our Privacy Notice, but you can find out more details about any of these topics by clicking the link following each key point or by using our table of contents below to find the section you are looking for.
            </p>
            <p>
                <strong>What personal information do we process?</strong> When you visit, use, or navigate our Services, we may process personal information depending on how you interact with us and the Services, the choices you make, and the products and features you use. Learn more about personal information you disclose to us.
            </p>
            <p>
                <strong>Do we process any sensitive personal information?</strong> Some of the information may be considered "special" or "sensitive" in certain jurisdictions, for example your racial or ethnic origins, sexual orientation, and religious beliefs. We do not process sensitive personal information.
            </p>
            <p>
                <strong>Do we collect any information from third parties?</strong> We do not collect any information from third parties.
            </p>
            <p>
                <strong>How do we process your information?</strong> We process your information to provide, improve, and administer our Services, communicate with you, for security and fraud prevention, and to comply with law. We may also process your information for other purposes with your consent. We process your information only when we have a valid legal reason to do so. Learn more about how we process your information.
            </p>
            <p>
                <strong>In what situations and with which parties do we share personal information?</strong> We may share information in specific situations and with specific third parties. Learn more about when and with whom we share your personal information.
            </p>
            <p>
                <strong>How do we keep your information safe?</strong> We have adequate organizational and technical processes and procedures in place to protect your personal information. However, no electronic transmission over the internet or information storage technology can be guaranteed to be 100% secure, so we cannot promise or guarantee that hackers, cybercriminals, or other unauthorized third parties will not be able to defeat our security and improperly collect, access, steal, or modify your information. Learn more about how we keep your information safe.
            </p>
            <p>
                <strong>What are your rights?</strong> Depending on where you are located geographically, the applicable privacy law may mean you have certain rights regarding your personal information. Learn more about your privacy rights.
            </p>
            <p>
                <strong>How do you exercise your rights?</strong> The easiest way to exercise your rights is by submitting a data subject access request, or by contacting us. We will consider and act upon any request in accordance with applicable data protection laws.
            </p>
            <p>
                Want to learn more about what we do with any information we collect? Review the Privacy Notice in full.
            </p>

            <h3>TABLE OF CONTENTS</h3>
            <ol>
                <li>1. WHAT INFORMATION DO WE COLLECT?</li>
                <li>2. HOW DO WE PROCESS YOUR INFORMATION?</li>
                <li>3. WHEN AND WITH WHOM DO WE SHARE YOUR PERSONAL INFORMATION?</li>
                <li>4. HOW LONG DO WE KEEP YOUR INFORMATION?</li>
                <li>5. HOW DO WE KEEP YOUR INFORMATION SAFE?</li>
                <li>6. DO WE COLLECT INFORMATION FROM MINORS?</li>
                <li>7. WHAT ARE YOUR PRIVACY RIGHTS?</li>
                <li>8. CONTROLS FOR DO-NOT-TRACK FEATURES</li>
                <li>9. DO WE MAKE UPDATES TO THIS NOTICE?</li>
                <li>10. HOW CAN YOU CONTACT US ABOUT THIS NOTICE?</li>
                <li>11. HOW CAN YOU REVIEW, UPDATE, OR DELETE THE DATA WE COLLECT FROM YOU?</li>
            </ol>

            <h3>1. WHAT INFORMATION DO WE COLLECT?</h3>
            <h4>Personal information you disclose to us</h4>
            <p><strong>In Short:</strong> We collect personal information that you provide to us.</p>
            <p>
                We collect personal information that you voluntarily provide to us when you express an interest in obtaining information about us or our products and Services, when you participate in activities on the Services, or otherwise when you contact us.
            </p>
            <p>
                <strong>Personal Information Provided by You.</strong> The personal information that we collect depends on the context of your interactions with us and the Services, the choices you make, and the products and features you use. The personal information we collect may include the following:
            </p>
            <ul>
                <li>names</li>
                <li>phone numbers</li>
                <li>email addresses</li>
                <li>passwords</li>
                <li>usernames</li>
                <li>contact preferences</li>
            </ul>
            <p><strong>Sensitive Information.</strong> We do not process sensitive information.</p>
            <p>
                <strong>Application Data.</strong> If you use our application(s), we also may collect the following information if you choose to provide us with access or permission:
            </p>
            <p>
                <em>Push Notifications.</em> We may request to send you push notifications regarding your account or certain features of the application(s). If you wish to opt out from receiving these types of communications, you may turn them off in your device's settings.
            </p>
            <p>
                This information is primarily needed to maintain the security and operation of our application(s), for troubleshooting, and for our internal analytics and reporting purposes.
            </p>
            <p>
                All personal information that you provide to us must be true, complete, and accurate, and you must notify us of any changes to such personal information.
            </p>

            <h3>2. HOW DO WE PROCESS YOUR INFORMATION?</h3>
            <p><strong>In Short:</strong> We process your information to provide, improve, and administer our Services, communicate with you, for security and fraud prevention, and to comply with law. We may also process your information for other purposes with your consent.</p>
            <p>
                We process your personal information for a variety of reasons, depending on how you interact with our Services, including:
            </p>
            <ul>
                <li><strong>To deliver and facilitate delivery of services to the user.</strong> We may process your information to provide you with the requested service.</li>
            </ul>

            <h3>3. WHEN AND WITH WHOM DO WE SHARE YOUR PERSONAL INFORMATION?</h3>
            <p><strong>In Short:</strong> We may share information in specific situations described in this section and/or with the following third parties.</p>
            <p>
                We may need to share your personal information in the following situations:
            </p>
            <ul>
                <li><strong>Business Transfers.</strong> We may share or transfer your information in connection with, or during negotiations of, any merger, sale of company assets, financing, or acquisition of all or a portion of our business to another company.</li>
            </ul>

            <h3>4. HOW LONG DO WE KEEP YOUR INFORMATION?</h3>
            <p><strong>In Short:</strong> We keep your information for as long as necessary to fulfill the purposes outlined in this Privacy Notice unless otherwise required by law.</p>
            <p>
                We will only keep your personal information for as long as it is necessary for the purposes set out in this Privacy Notice, unless a longer retention period is required or permitted by law (such as tax, accounting, or other legal requirements). No purpose in this notice will require us keeping your personal information for longer than 2 years.
            </p>
            <p>
                When we have no ongoing legitimate business need to process your personal information, we will either delete or anonymize such information, or, if this is not possible (for example, because your personal information has been stored in backup archives), then we will securely store your personal information and isolate it from any further processing until deletion is possible.
            </p>

            <h3>5. HOW DO WE KEEP YOUR INFORMATION SAFE?</h3>
            <p><strong>In Short:</strong> We aim to protect your personal information through a system of organizational and technical security measures.</p>
            <p>
                We have implemented appropriate and reasonable technical and organizational security measures designed to protect the security of any personal information we process. However, despite our safeguards and efforts to secure your information, no electronic transmission over the Internet or information storage technology can be guaranteed to be 100% secure, so we cannot promise or guarantee that hackers, cybercriminals, or other unauthorized third parties will not be able to defeat our security and improperly collect, access, steal, or modify your information. Although we will do our best to protect your personal information, transmission of personal information to and from our Services is at your own risk. You should only access the Services within a secure environment.
            </p>

            <h3>6. DO WE COLLECT INFORMATION FROM MINORS?</h3>
            <p><strong>In Short:</strong> We do not knowingly collect data from or market to children under 18 years of age.</p>
            <p>
                We do not knowingly collect, solicit data from, or market to children under 18 years of age, nor do we knowingly sell such personal information. By using the Services, you represent that you are at least 18 or that you are the parent or guardian of such a minor and consent to such minor dependent’s use of the Services. If we learn that personal information from users less than 18 years of age has been collected, we will deactivate the account and take reasonable measures to promptly delete such data from our records. If you become aware of any data we may have collected from children under age 18, please contact us at <a href="mailto:Pay.laxmikant@gmail.com">Pay.laxmikant@gmail.com</a>.
            </p>

            <h3>7. WHAT ARE YOUR PRIVACY RIGHTS?</h3>
            <p><strong>In Short:</strong> You may review, change, or terminate your account at any time, depending on your country, province, or state of residence.</p>
            <p>
                <strong>Withdrawing your consent:</strong> If we are relying on your consent to process your personal information, which may be express and/or implied consent depending on the applicable law, you have the right to withdraw your consent at any time. You can withdraw your consent at any time by contacting us by using the contact details provided in the section "HOW CAN YOU CONTACT US ABOUT THIS NOTICE?" below.
            </p>
            <p>
                However, please note that this will not affect the lawfulness of the processing before its withdrawal nor, when applicable law allows, will it affect the processing of your personal information conducted in reliance on lawful processing grounds other than consent.
            </p>
            <p>
                If you have questions or comments about your privacy rights, you may email us at <a href="mailto:pay.laxmikant@gmail.com">pay.laxmikant@gmail.com</a>.
            </p>

            <h3>8. CONTROLS FOR DO-NOT-TRACK FEATURES</h3>
            <p>
                Most web browsers and some mobile operating systems and mobile applications include a Do-Not-Track ("DNT") feature or setting you can activate to signal your privacy preference not to have data about your online browsing activities monitored and collected. At this stage, no uniform technology standard for recognizing and implementing DNT signals has been finalized. As such, we do not currently respond to DNT browser signals or any other mechanism that automatically communicates your choice not to be tracked online. If a standard for online tracking is adopted that we must follow in the future, we will inform you about that practice in a revised version of this Privacy Notice.
            </p>

            <h3>9. DO WE MAKE UPDATES TO THIS NOTICE?</h3>
            <p><strong>In Short:</strong> Yes, we will update this notice as necessary to stay compliant with relevant laws.</p>
            <p>
                We may update this Privacy Notice from time to time. The updated version will be indicated by an updated "Revised" date at the top of this Privacy Notice. If we make material changes to this Privacy Notice, we may notify you either by prominently posting a notice of such changes or by directly sending you a notification. We encourage you to review this Privacy Notice frequently to be informed of how we are protecting your information.
            </p>

            <h3>10. HOW CAN YOU CONTACT US ABOUT THIS NOTICE?</h3>
            <p>
                If you have questions or comments about this notice, you may email us at <a href="mailto:Pay.laxmikant@gmail.com">Pay.laxmikant@gmail.com</a> or contact us by post at:
            </p>
            <address class="fst-normal mb-3 ps-3 border-start border-2 border-primary">
                <strong>GI ORDER</strong><br>
                GI ORDER -PATNA<br>
                BIHAR<br>
                PATNA, BIHAR 800001<br>
                India
            </address>

            <h3>11. HOW CAN YOU REVIEW, UPDATE, OR DELETE THE DATA WE COLLECT FROM YOU?</h3>
            <p>
                Based on the applicable laws of your country, you may have the right to request access to the personal information we collect from you, details about how we have processed it, correct inaccuracies, or delete your personal information. You may also have the right to withdraw your consent to our processing of your personal information. These rights may be limited in some circumstances by applicable law. To request to review, update, or delete your personal information, please fill out and submit a data subject access request.
            </p>
        </div>
    </div>

    <div class="text-center text-muted small pb-4">
        &copy; <?= date('Y') ?> GI ORDER. All rights reserved.
    </div>
</div>

</body>
</html>
