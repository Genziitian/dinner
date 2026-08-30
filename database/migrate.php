<?php
/**
 * Database Migration & Seeder CLI Script
 * Restaurant Billing & Order Management System
 */

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.\n");
}

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/models/Database.php';

echo "====================================================\n";
echo " Restaurant Billing System - Database Migration\n";
echo "====================================================\n";

try {
    $driver = Database::getDriver();
    echo "Using database driver: [{$driver}]\n";

    $db = Database::getConnection();

    $schemaFile = $driver === 'sqlite' 
        ? ROOT_PATH . '/database/schema_sqlite.sql' 
        : ROOT_PATH . '/database/schema.sql';

    if (!file_exists($schemaFile)) {
        throw new RuntimeException("Schema file not found: {$schemaFile}");
    }

    echo "Running schema: " . basename($schemaFile) . "...\n";
    $schemaSql = file_get_contents($schemaFile);

    if ($driver === 'sqlite') {
        $db->exec($schemaSql);
    } else {
        // Execute multi-query MySQL statements
        $db->exec($schemaSql);
    }

    echo "✓ Clean database schema initialized successfully.\n\n";
    echo "Next step: Visit your website URL to create your Master Super Admin account,\n";
    echo "or run: php database/create_admin.php <username> <password>\n\n";
    echo "====================================================\n";
    echo " Migration Completed Successfully (Zero Fake Data)\n";
    echo "====================================================\n";

} catch (Exception $e) {
    echo "Error during migration: " . $e->getMessage() . "\n";
    exit(1);
}

