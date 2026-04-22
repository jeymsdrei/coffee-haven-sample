<?php
// Coffee Haven - Laravel-style Application Entry Point
session_start();

// Autoload classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        include $file;
    }
});

// Initialize database
\App\Database::initialize();

// Load routes
$router = require __DIR__ . '/routes/web.php';

// Get the requested path
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/coffee_haven', '', $path);
if (empty($path)) {
    $path = '/';
}

// Dispatch request
$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($method, $path);

