<?php
/**
 * Database Configuration
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

// Ensure app config is loaded
require_once __DIR__ . '/app.php';

return [
    'default' => env('DB_DRIVER', 'sqlite'),
    
    'connections' => [
        'mysql' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '127.0.0.1'),
            'port'      => (int)env('DB_PORT', 3306),
            'database'  => env('DB_NAME', 'dinner_pos'),
            'username'  => env('DB_USER', 'root'),
            'password'  => env('DB_PASS', ''),
            'charset'   => env('DB_CHARSET', 'utf8mb4'),
            'collation' => 'utf8mb4_unicode_ci',
            'options'   => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
            ],
        ],
        
        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => ROOT_PATH . '/' . env('DB_SQLITE_PATH', 'storage/db/database.sqlite'),
            'options'  => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        ],
    ],
];
