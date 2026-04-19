<?php
// Simple PDO connection example for XAMPP / local development
$DB_HOST = '127.0.0.1';
$DB_NAME = 'coffee_haven';
$DB_USER = 'root';
$DB_PASS = ''; // XAMPP default: empty password

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    error_log('DB connection failed: ' . $e->getMessage());
    // In development you may want to show the error, but avoid exposing details in production.
    die('Database connection failed.');
}

?>
