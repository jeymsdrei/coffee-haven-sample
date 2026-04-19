<?php
require_once __DIR__ . '/db_connect.php';
session_start();
header('Content-Type: application/json');

$body = json_decode(file_get_contents('php://input'), true);
if (!$body) { echo json_encode(['ok'=>false,'error'=>'invalid']); exit; }
$email = trim($body['email'] ?? '');
$password = $body['password'] ?? '';
if ($email === '' || $password === '') { echo json_encode(['ok'=>false,'error'=>'missing']); exit; }

$stmt = $pdo->prepare('SELECT id, username, email, password_hash FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) { echo json_encode(['ok'=>false,'error'=>'not_found']); exit; }

// Support both password_hash and legacy SHA2 if needed
if ($user['password_hash'] && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    echo json_encode(['ok'=>true,'user'=>['id'=>$user['id'],'username'=>$user['username'],'email'=>$user['email']]]);
    exit;
}

// fallback SHA2 check (legacy)
$sha2 = hash('sha256', $password);
if ($user['password_hash'] === $sha2) {
    // upgrade hash
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $up = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
    $up->execute([$newHash, $user['id']]);
    $_SESSION['user_id'] = $user['id'];
    echo json_encode(['ok'=>true,'user'=>['id'=>$user['id'],'username'=>$user['username'],'email'=>$user['email']]]);
    exit;
}

echo json_encode(['ok'=>false,'error'=>'invalid_credentials']);
exit;

?>
