<?php
require_once __DIR__ . '/db_connect.php';
session_start();
header('Content-Type: application/json');

$body = json_decode(file_get_contents('php://input'), true);
if (!$body) { echo json_encode(['ok'=>false,'error'=>'invalid']); exit; }
$username = trim($body['username'] ?? '');
$email = trim($body['email'] ?? '');
$password = $body['password'] ?? '';
if ($username === '' || $email === '' || $password === '') { echo json_encode(['ok'=>false,'error'=>'missing']); exit; }

// check email uniqueness
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
if ($stmt->fetch()) { echo json_encode(['ok'=>false,'error'=>'exists']); exit; }

$hash = password_hash($password, PASSWORD_DEFAULT);
$ins = $pdo->prepare('INSERT INTO users (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())');
try {
    $ins->execute([$username, $email, $hash]);
    $userId = $pdo->lastInsertId();
    $_SESSION['user_id'] = $userId;
    echo json_encode(['ok'=>true,'user'=>['id'=>$userId,'username'=>$username,'email'=>$email]]);
    exit;
} catch (Exception $e) {
    error_log('register error: '.$e->getMessage());
    echo json_encode(['ok'=>false,'error'=>'server']);
    exit;
}

?>
