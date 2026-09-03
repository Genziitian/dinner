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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#f8fafc">
    <meta name="color-scheme" content="light">
    <link rel="manifest" href="<?= url('manifest.json') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= asset('icons/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= asset('icons/favicon-16x16.png') ?>">
    <link rel="icon" type="image/svg+xml" href="<?= asset('icons/icon.svg') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= asset('icons/apple-touch-icon.png') ?>">
    <link rel="shortcut icon" href="<?= asset('icons/favicon.ico') ?>">
    <title>Sign In · GI ORDER POS</title>
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css?v=' . filemtime(ROOT_PATH . '/public/assets/css/app.css')) ?>">
    <style>
        html, body, input {
            color-scheme: light !important;
        }
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
            color: #0f172a !important;
            -webkit-text-fill-color: #0f172a !important;
            background-color: #ffffff !important;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            outline: none;
            transition: all 0.15s ease-in-out;
            caret-color: #ea580c;
        }
        .form-control-android::placeholder {
            color: #94a3b8 !important;
            -webkit-text-fill-color: #94a3b8 !important;
        }
        .form-control-android:focus {
            border-color: #ea580c;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15);
        }
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #ffffff inset !important;
            -webkit-text-fill-color: #0f172a !important;
            box-shadow: 0 0 0 30px #ffffff inset !important;
            caret-color: #ea580c !important;
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
        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            padding: 6px;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: color 0.15s ease, background-color 0.15s ease;
        }
        .password-toggle-btn:hover {
            color: #0f172a;
            background-color: #f1f5f9;
        }
    </style>
</head>
<body>

<div class="login-card-android">
    <!-- Brand Logo -->
    <div class="text-center">
        <div class="mb-3">
            <img src="<?= asset('icons/icon-192.png') ?>" alt="GI ORDER POS" class="app-brand-logo-img" width="76" height="76" style="border-radius: 20px; box-shadow: 0 8px 24px rgba(253, 108, 1, 0.35);">
        </div>
        <h3 class="fw-bold m-0" style="letter-spacing: -0.5px; color: #0f172a; font-size: 1.5rem;">GI ORDER POS</h3>
        <p class="text-secondary" style="font-size: 0.85rem; margin-top: 0.25rem; margin-bottom: 1.5rem;">Billing & Store Management</p>
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
                style="padding-right: 2.75rem;"
            >
            <button 
                type="button" 
                class="password-toggle-btn" 
                id="togglePasswordBtn" 
                onclick="togglePasswordVisibility()" 
                title="Toggle password visibility"
                tabindex="-1"
            >
                <svg id="eyeIcon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
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

<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
    }
}
</script>
</body>
</html>
