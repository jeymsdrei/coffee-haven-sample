<?php
require_once __DIR__ . '/db_connect.php';
session_start();
header('Content-Type: application/json');

if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT id, username, email, is_admin, created_at FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo json_encode(['ok' => true, 'user' => $user]);
        exit;
    }
}

echo json_encode(['ok' => false]);
exit;

?>
