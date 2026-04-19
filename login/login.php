<?php
require_once __DIR__ . '/db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    die('Email and password are required.');
}

$stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    die('No user found with that email.');
}

$stored = $user['password_hash'];
$ok = false;

// Prefer password_verify (password_hash). Also accept legacy SHA256 demo hashes and migrate.
if (password_verify($password, $stored)) {
    $ok = true;
} elseif (hash('sha256', $password) === $stored) {
    // Legacy demo entry - accept and migrate to password_hash
    $ok = true;
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $upd = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
    $upd->execute([$newHash, $user['id']]);
}

if (!$ok) {
    die('Invalid credentials.');
}

// Login success
$_SESSION['user_id'] = $user['id'];
// Redirect to the main site page (my.html)
header('Location: ../my.html');
exit;

?>
