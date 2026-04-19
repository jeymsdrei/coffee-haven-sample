<?php
require_once __DIR__ . '/db_connect.php';
session_start();

// Simple registration handler
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($fullname) || empty($email) || empty($password)) {
    die('All fields are required.');
}

// Basic email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email address.');
}

// Check if email exists
$stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    die('Email already registered. Please log in.');
}

// Hash password with password_hash
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$insert = $pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
try {
    $insert->execute([$fullname, $email, $password_hash]);
    $user_id = $pdo->lastInsertId();
    // Log the user in
    $_SESSION['user_id'] = $user_id;
    // Redirect to the main site page (my.html)
    header('Location: ../my.html');
    exit;
} catch (Exception $e) {
    error_log('Register error: ' . $e->getMessage());
    die('Registration failed.');
}

?>
