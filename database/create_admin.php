<?php
/**
 * CLI Tool to Create Super Admin User
 * Usage: php database/create_admin.php [username] [password]
 */

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/models/Database.php';
require_once ROOT_PATH . '/models/User.php';

echo "====================================================\n";
echo " DinePOS - Create Master Super Admin User\n";
echo "====================================================\n\n";

$username = $argv[1] ?? '';
$password = $argv[2] ?? '';

if (empty($username)) {
    echo "Enter Super Admin Username (3-10 characters): ";
    $username = trim((string)fgets(STDIN));
}

if (empty($password)) {
    echo "Enter Master Password (min 8 characters): ";
    $password = trim((string)fgets(STDIN));
}

try {
    $userId = User::create([
        'restaurant_id' => null,
        'username' => $username,
        'password' => $password,
        'role' => User::ROLE_SUPERADMIN,
        'status' => 'active',
    ]);

    echo "\n✓ Super Admin '{$username}' created successfully (ID: #{$userId}).\n";
    echo "You can now log in at your website's login page.\n\n";
} catch (Throwable $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n\n";
    exit(1);
}
