<?php
/**
 * Modern Android Native Login View Template
 * Restaurant Billing & Order Management System
 */
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#f8fafc">
    <link rel="manifest" href="<?= url('manifest.json') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= asset('icons/icon.svg') ?>">
    <title>Sign In · GI ORDER POS</title>
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <style>
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Inter", sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 1.25rem;
        }
        .login-card-android {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 420px;
            padding: 2.25rem 2rem;
        }
        .app-circle-logo {
            width: 68px;
            height: 68px;
            background: #ea580c;
            color: #ffffff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.35);
            margin-bottom: 1.25rem;
        }
        .input-group-android {
            position: relative;
            margin-bottom: 1.15rem;
        }
        .input-group-android .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            pointer-events: none;
        }
        .form-control-android {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            font-size: 0.95rem;
            font-family: inherit;
            color: #0f172a;
            background-color: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            outline: none;
            transition: all 0.15s ease-in-out;
        }
        .form-control-android:focus {
            border-color: #ea580c;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15);
        }
        .btn-signin-android {
            width: 100%;
            padding: 0.9rem;
            font-size: 1rem;
            font-weight: 700;
            color: #ffffff;
            background-color: #ea580c;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.35);
            transition: all 0.15s ease;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-signin-android:hover {
            background-color: #c2410c;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(234, 88, 12, 0.45);
        }
        .btn-signin-android:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>

<div class="login-card-android">
    <!-- Brand Logo Circle -->
    <div class="text-center">
        <div class="app-circle-logo">
            <svg width="34" height="34" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <h3 class="fw-bold m-0" style="letter-spacing: -0.5px; color: #0f172a; font-size: 1.5rem;">GI ORDER POS</h3>
        <p class="text-secondary" style="font-size: 0.85rem; margin-top: 0.25rem; margin-bottom: 1.5rem;">Restaurant Billing & Order Management</p>
    </div>

    <?php require ROOT_PATH . '/views/layouts/flash.php'; ?>

    <form action="<?= url('login') ?>" method="POST" autocomplete="off">
        <?= csrf_field() ?>

        <div class="input-group-android">
            <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <input 
                type="text" 
                class="form-control-android" 
                id="username" 
                name="username" 
                maxlength="10" 
                pattern="[a-zA-Z0-9_]{3,10}"
                placeholder="Username" 
                required 
                autofocus
            >
        </div>

        <div class="input-group-android">
            <svg class="input-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <input 
                type="password" 
                class="form-control-android" 
                id="password" 
                name="password" 
                minlength="8" 
                maxlength="72"
                placeholder="Password" 
                required
            >
        </div>

        <button type="submit" class="btn-signin-android mt-2">
            Sign In
        </button>

        <p class="text-center text-secondary" style="font-size: 0.76rem; margin-top: 1.5rem; margin-bottom: 0; line-height: 1.5;">
            By continuing, you agree to our 
            <a href="<?= url('privacy-policy') ?>" target="_blank" rel="noopener noreferrer" style="color: #0f172a; font-weight: 700; text-decoration: underline;">Privacy Policy</a> 
            and 
            <a href="<?= url('terms-and-conditions') ?>" target="_blank" rel="noopener noreferrer" style="color: #0f172a; font-weight: 700; text-decoration: underline;">Terms and Conditions</a>.
        </p>
    </form>
</div>

</body>
</html>
