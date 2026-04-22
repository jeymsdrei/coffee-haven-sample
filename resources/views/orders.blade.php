<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Coffee Haven</title>
    <link rel="stylesheet" href="/coffee_haven/style.css">
    <link rel="stylesheet" href="/coffee_haven/public/css/app.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">☕ Coffee Haven</div>
            <div class="nav-links">
                <a href="/coffee_haven/">Home</a>
                <a href="/coffee_haven/products">Products</a>
                <a href="/coffee_haven/cart">Cart</a>
                <a href="/coffee_haven/orders">My Orders</a>
                <a href="/coffee_haven/profile">Profile</a>
                <a href="/coffee_haven/logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>My Orders</h2>

        <?php if (empty($orders)): ?>
            <p>You haven't placed any orders yet. <a href="/coffee_haven/products">Start shopping</a></p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <h3>Order #<?= $order->id ?></h3>
                    <p><strong>Date:</strong> <?= $order->created_at ?></p>
                    <p><strong>Status:</strong> <span class="status-<?= $order->status ?>"><?= ucfirst($order->status) ?></span></p>
                    <p><strong>Total:</strong> $<?= number_format($order->total_amount, 2) ?></p>
                    
                    <h4>Items:</h4>
                    <ul>
                        <?php foreach ($order->items as $item): ?>
                            <li>
                                <?= $item->product->name ?> x <?= $item->quantity ?> @ $<?= number_format($item->unit_price, 2) ?> each
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2026 Coffee Haven. All rights reserved.</p>
    </footer>
</body>
</html>
