<?php
/**
 * DinePOS HTTP End-to-End Integration Test Suite
 */

declare(strict_types=1);

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = tempnam(sys_get_temp_dir(), 'dinepos_cookie_');

echo "========================================================\n";
echo " DinePOS HTTP End-to-End Integration Verification\n";
echo "========================================================\n\n";

function httpRequest(string $url, string $method = 'GET', array $data = [], array $headers = [], ?string $cookieJar = null): array {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

    if ($cookieJar) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    }

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if (!empty($headers['Content-Type']) && str_contains($headers['Content-Type'], 'json')) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
    }

    $headerLines = [];
    foreach ($headers as $k => $v) {
        $headerLines[] = "{$k}: {$v}";
    }
    if (!empty($headerLines)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerLines);
    }

    $raw = curl_exec($ch);
    $curlErr = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

    if ($raw === false) {
        return [
            'code' => 0,
            'headers' => '',
            'body' => "cURL Error: {$curlErr}"
        ];
    }

    $headerStr = substr((string)$raw, 0, $headerSize);
    $body = substr((string)$raw, $headerSize);

    return [
        'code' => $httpCode,
        'headers' => $headerStr,
        'body' => $body
    ];
}

$passed = 0;
$failed = 0;

function assertHttp(bool $condition, string $msg): void {
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "  [PASS] {$msg}\n";
    } else {
        $failed++;
        echo "  [FAIL] {$msg}\n";
    }
}

// 1. Test Login Page & CSRF Token Extraction
echo "1. Testing Login Page & Security Headers...\n";
$res = httpRequest("{$baseUrl}/login", 'GET', [], [], $cookieFile);
assertHttp($res['code'] === 200, "GET /login returns HTTP 200 OK");
assertHttp(str_contains($res['headers'], 'X-Frame-Options: SAMEORIGIN'), "Security header X-Frame-Options present");
assertHttp(str_contains($res['headers'], 'X-Content-Type-Options: nosniff'), "Security header X-Content-Type-Options present");

preg_match('/name="csrf_token"\s+value="([a-f0-9]+)"/', $res['body'], $matches);
$csrfToken = $matches[1] ?? '';
assertHttp(!empty($csrfToken), "Extracted valid CSRF token: " . substr($csrfToken, 0, 10) . "...");

// 2. Test Failed Login
echo "\n2. Testing Failed Login Handling...\n";
$badLogin = httpRequest("{$baseUrl}/login", 'POST', [
    'csrf_token' => $csrfToken,
    'username' => 'cashier01',
    'password' => 'wrongpass123'
], [], $cookieFile);
assertHttp($badLogin['code'] === 302, "Failed login returns HTTP 302 Redirect");

// 3. Test Successful Cashier Login
echo "\n3. Testing Successful Cashier Login & Session Initiation...\n";
$goodLogin = httpRequest("{$baseUrl}/login", 'POST', [
    'csrf_token' => $csrfToken,
    'username' => 'cashier01',
    'password' => 'password123'
], [], $cookieFile);
assertHttp($goodLogin['code'] === 302, "Valid login returns HTTP 302 Redirect to /cashier/order");

// 4. Test Cashier Billing Screen
echo "\n4. Testing Cashier Billing Screen...\n";
$billingRes = httpRequest("{$baseUrl}/cashier/order", 'GET', [], [], $cookieFile);
assertHttp($billingRes['code'] === 200, "GET /cashier/order returns HTTP 200 OK");
assertHttp(str_contains($billingRes['body'], 'Chicken Curry'), "Contains 'Chicken Curry' item in menu");
assertHttp(str_contains($billingRes['body'], 'Boiled Egg'), "Contains 'Boiled Egg' item in menu");
assertHttp(str_contains($billingRes['body'], 'manifest.json'), "Contains PWA Web App Manifest link");

// Re-extract fresh CSRF token from cashier session
preg_match('/name="csrf-token"\s+content="([a-f0-9]+)"/', $billingRes['body'], $matches);
$cashierCsrf = $matches[1] ?? $csrfToken;

// 5. Test Cashier Order Creation via API
echo "\n5. Testing AJAX Order Creation (POST /api/orders)...\n";
$orderRes = httpRequest(
    "{$baseUrl}/api/orders",
    'POST',
    [
        'customer_name' => 'John Doe',
        'customer_phone' => '9876543210',
        'payment_method' => 'Online / UPI',
        'items' => [
            ['item_id' => 1, 'variant_id' => 1, 'quantity' => 1], // Full Chicken Curry ₹120
            ['item_id' => 4, 'variant_id' => 6, 'quantity' => 0.5] // 0.5 kg Rice ₹60
        ]
    ],
    [
        'Content-Type' => 'application/json',
        'X-CSRF-Token' => $cashierCsrf
    ],
    $cookieFile
);

assertHttp($orderRes['code'] === 200, "POST /api/orders returns HTTP 200 OK");
$orderJson = json_decode($orderRes['body'], true);
assertHttp(!empty($orderJson['success']) && $orderJson['success'] === true, "JSON response indicates success: true");
assertHttp(!empty($orderJson['receipt_token']), "Receipt token generated in response");
$receiptToken = $orderJson['receipt_token'] ?? '';
$orderTotal = $orderJson['total'] ?? 0;
assertHttp((float)$orderTotal === 180.00, "Server calculated ₹180.00 total for 1 Full Curry + 0.5kg Rice");

// 6. Test Public QR Receipt Viewing (No Cookie Required)
echo "\n6. Testing Public QR Receipt (GET /receipt/{token})...\n";
$publicReceipt = httpRequest("{$baseUrl}/receipt/{$receiptToken}", 'GET');
assertHttp($publicReceipt['code'] === 200, "Public customer receipt returns HTTP 200 OK");
assertHttp(str_contains($publicReceipt['body'], 'Chicken Curry'), "Receipt contains 'Chicken Curry'");
assertHttp(str_contains($publicReceipt['body'], 'John Doe'), "Receipt displays customer name 'John Doe'");
assertHttp(stripos($publicReceipt['body'], 'Online / UPI') !== false, "Receipt displays payment method 'Online / UPI'");

// 7. Test Fake Token Rejection
echo "\n7. Testing Invalid/Tampered Token Access (404)...\n";
$badTokenRes = httpRequest("{$baseUrl}/receipt/invalid_token_12345678901234567890", 'GET');
assertHttp($badTokenRes['code'] === 404, "Invalid token correctly returns HTTP 404 Not Found");

// 8. Test Role Authorization Guard (Cashier blocked from Manager Reports)
echo "\n8. Testing Server-Side Role Protection...\n";
$reportsBlock = httpRequest("{$baseUrl}/manager/reports", 'GET', [], [], $cookieFile);
assertHttp($reportsBlock['code'] === 403, "Cashier attempting to access /manager/reports receives HTTP 403 Forbidden");

// 9. Test Manager Login and CSV Export
echo "\n9. Testing Manager Session & CSV Export Download...\n";
$managerCookie = tempnam(sys_get_temp_dir(), 'dinepos_mgr_');
$mgrPage = httpRequest("{$baseUrl}/login", 'GET', [], [], $managerCookie);
preg_match('/name="csrf_token"\s+value="([a-f0-9]+)"/', $mgrPage['body'], $mMatches);
$mgrCsrf = $mMatches[1] ?? '';

$mgrLogin = httpRequest("{$baseUrl}/login", 'POST', [
    'csrf_token' => $mgrCsrf,
    'username' => 'manager01',
    'password' => 'password123'
], [], $managerCookie);
assertHttp($mgrLogin['code'] === 302, "Manager login succeeded with HTTP 302");

$mgrDashboard = httpRequest("{$baseUrl}/manager/dashboard", 'GET', [], [], $managerCookie);
assertHttp($mgrDashboard['code'] === 200, "Manager accesses /manager/dashboard with HTTP 200 OK");

$csvRes = httpRequest("{$baseUrl}/manager/exports/download?type=daily", 'GET', [], [], $managerCookie);
assertHttp($csvRes['code'] === 200, "Manager downloads Daily CSV with HTTP 200 OK");
assertHttp(str_contains($csvRes['headers'], 'text/csv'), "Response content-type is text/csv");
assertHttp(str_contains($csvRes['body'], 'RESTAURANT SALES EXPORT REPORT'), "CSV body contains report header");

// Cleanup temp cookies
@unlink($cookieFile);
@unlink($managerCookie);

echo "\n========================================================\n";
echo " HTTP Verification Results: Passed: {$passed}, Failed: {$failed}\n";
echo "========================================================\n";

if ($failed > 0) {
    exit(1);
}
