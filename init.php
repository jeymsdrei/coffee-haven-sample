#!/usr/bin/env php
<?php
/**
 * Coffee Haven - Initialization Script
 * 
 * This script initializes the SQLite database for the Coffee Haven application.
 * Run this once to set up the database and seed initial data.
 */

echo "========================================\n";
echo "Coffee Haven - Database Initialization\n";
echo "========================================\n\n";

// Set up class autoloading
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        include $file;
    }
});

// Initialize database
echo "Initializing SQLite database...\n";
try {
    $db = \App\Database::initialize();
    echo "✓ Database initialized successfully!\n\n";
} catch (Exception $e) {
    echo "✗ Error initializing database: " . $e->getMessage() . "\n";
    exit(1);
}

// Verify tables
echo "Verifying database tables...\n";
$tables = ['users', 'products', 'cart_items', 'orders', 'order_items'];

foreach ($tables as $table) {
    try {
        $result = $db->query("SELECT COUNT(*) as count FROM $table");
        $row = $result->fetch(\PDO::FETCH_ASSOC);
        echo "  ✓ $table: " . $row['count'] . " rows\n";
    } catch (Exception $e) {
        echo "  ✗ Error checking $table: " . $e->getMessage() . "\n";
    }
}

echo "\n========================================\n";
echo "✓ Initialization Complete!\n";
echo "========================================\n\n";

echo "Database location: " . __DIR__ . "/storage/database/coffee_haven.db\n";
echo "Admin login:\n";
echo "  Email: admin@example.com\n";
echo "  Password: adminpass\n\n";

echo "Application ready at: http://localhost/coffee_haven/\n";
echo "\n";
