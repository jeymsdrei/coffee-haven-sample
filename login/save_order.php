<?php
require_once __DIR__ . '/db_connect.php';
session_start();

header('Content-Type: application/json');

// Read JSON body
$body = json_decode(file_get_contents('php://input'), true);
if (!$body) {
    echo json_encode(['ok' => false, 'error' => 'Invalid request']);
    exit;
}

$items = $body['items'] ?? [];
$total = isset($body['total']) ? floatval($body['total']) : 0.0;
$method = $body['method'] ?? 'unknown';

// Enforce authenticated user: do not allow anonymous/guest orders
if (empty($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'error' => 'Authentication required']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Insert order
    $ins = $pdo->prepare('INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, ?, NOW())');
    $ins->execute([$user_id, $total, 'completed']);
    $order_id = $pdo->lastInsertId();

    // Insert order items
    $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)');
    foreach ($items as $it) {
        // items may be passed with product id or name only; try to find product id by name if not provided
        $product_id = isset($it['product_id']) ? intval($it['product_id']) : null;
        if (!$product_id && !empty($it['name'])) {
            $pstmt = $pdo->prepare('SELECT id FROM products WHERE name = ? LIMIT 1');
            $pstmt->execute([$it['name']]);
            $p = $pstmt->fetch();
            if ($p) $product_id = $p['id'];
        }
        $unit_price = isset($it['price']) ? floatval($it['price']) : 0.0;
        $qty = isset($it['quantity']) ? intval($it['quantity']) : 1;
        $itemStmt->execute([$order_id, $product_id ?? 0, $qty, $unit_price]);
    }

    echo json_encode(['ok' => true, 'order_id' => $order_id, 'user_id' => $user_id]);
    exit;
} catch (Exception $e) {
    error_log('save_order error: ' . $e->getMessage());
    echo json_encode(['ok' => false, 'error' => 'Server error']);
    exit;
}

?>
