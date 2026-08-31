<?php
/**
 * Modern Minimalist Login View Template
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
    <title>Sign In · GI ORDER</title>
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Inter", sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 1.25rem;
        }
        .login-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            width: 100%;
            max-width: 400px;
            padding: 2.25rem;
            transition: transform 0.2s ease;
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
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
            margin-bottom: 1rem;
        }
        .form-control-custom {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            font-family: inherit;
            color: #0f172a;
            background-color: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.75rem;
            transition: all 0.15s ease-in-out;
            outline: none;
            box-sizing: border-box;
        }
        .form-control-custom:focus {
            border-color: #0f172a;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.08);
            background-color: #ffffff;
        }
        .btn-submit {
            width: 100%;
            padding: 0.8rem;
            font-size: 1rem;
            font-weight: 600;
            color: #ffffff;
            background-color: #0f172a;
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-submit:hover {
            background-color: #1e293b;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        }
        .btn-submit:active {
            transform: scale(0.98);
        }
        /* Flash alert styles */
        .alert {
            display: flex;
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1rem;
            border: none;
        }
        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        .alert-warning {
            background-color: #fffbeb;
            color: #92400e;
            border-left: 4px solid #f59e0b;
        }
        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border-left: 4px solid #22c55e;
        }
        .alert-info {
            background-color: #eff6ff;
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }
    </style>
</head>
<body>

<div class="login-card">
    <!-- Brand Header -->
    <div class="text-center mb-4">
        <div class="app-logo-badge">
            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <h3 class="fw-bold m-0" style="letter-spacing: -0.5px;">GI ORDER</h3>
        <p class="text-muted" style="font-size: 0.875rem; margin-top: 0.25rem;">Restaurant Billing & Order Management</p>
    </div>

    <?php require ROOT_PATH . '/views/layouts/flash.php'; ?>

    <form action="<?= url('login') ?>" method="POST" autocomplete="off">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="username" class="form-label" style="font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 0.4rem; display: block;">Username / User ID</label>
            <input 
                type="text" 
                class="form-control-custom" 
                id="username" 
                name="username" 
                maxlength="10" 
                pattern="[a-zA-Z0-9_]{3,10}"
                placeholder="Enter username" 
                required 
                autofocus
            >
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">3 to 10 alphanumeric characters</div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label" style="font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 0.4rem; display: block;">Password</label>
            <input 
                type="password" 
                class="form-control-custom" 
                id="password" 
                name="password" 
                minlength="8" 
                maxlength="72"
                placeholder="Enter password" 
                required
            >
        </div>

        <button type="submit" class="btn-submit">
            <span>Sign In</span>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </button>

        <p class="text-center text-muted" style="font-size: 0.78rem; margin-top: 1.25rem; margin-bottom: 0; line-height: 1.5;">
            By continuing, you agree to our 
            <a href="<?= url('privacy-policy') ?>" target="_blank" rel="noopener noreferrer" style="color: #0f172a; font-weight: 600; text-decoration: underline;">Privacy Policy</a> 
            and 
            <a href="<?= url('terms-and-conditions') ?>" target="_blank" rel="noopener noreferrer" style="color: #0f172a; font-weight: 600; text-decoration: underline;">Terms and Conditions</a>.
        </p>
    </form>
</div>

</body>
</html>
