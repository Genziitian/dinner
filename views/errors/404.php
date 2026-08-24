<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Not Found | DinePOS</title>
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light p-3">
    <div class="card shadow-sm p-4 text-center" style="max-width: 420px; width: 100%;">
        <div class="mb-3 text-muted">
            <svg width="54" height="54" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="fw-bold mb-2">Page / Receipt Not Found</h3>
        <p class="text-muted mb-4">The requested page or receipt could not be found, has expired, or the link is invalid.</p>
        <a href="<?= url('/') ?>" class="btn btn-primary w-100">Go to Home</a>
    </div>
</body>
</html>
