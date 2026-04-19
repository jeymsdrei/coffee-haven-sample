<?php
// Import a local (client-side) user into the server users table.
require_once __DIR__ . '/db_connect.php';
session_start();
header('Content-Type: application/json');

$body = json_decode(file_get_contents('php://input'), true);
if (!$body) { echo json_encode(['ok'=>false,'error'=>'invalid']); exit; }
$username = trim($body['username'] ?? '');
$email = trim($body['email'] ?? '');
$password = $body['password'] ?? null; // optional
if ($username === '' || $email === '') { echo json_encode(['ok'=>false,'error'=>'missing']); exit; }

// If email already exists, return that user
$stmt = $pdo->prepare('SELECT id, username, email FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
if ($u = $stmt->fetch(PDO::FETCH_ASSOC)) { echo json_encode(['ok'=>true,'user'=>$u]); exit; }

$hash = $password ? password_hash($password, PASSWORD_DEFAULT) : '';
$ins = $pdo->prepare('INSERT INTO users (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())');
try {
    $ins->execute([$username, $email, $hash]);
    $id = $pdo->lastInsertId();
    echo json_encode(['ok'=>true,'user'=>['id'=>$id,'username'=>$username,'email'=>$email]]);
    exit;
} catch (Exception $e) {
    error_log('import_user error: '.$e->getMessage());
    echo json_encode(['ok'=>false,'error'=>'server']);
    exit;
}

?>
