<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Coffee Haven</title>
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
        <h2>Shopping Cart</h2>

        <?php if (empty($cartItems)): ?>
            <p>Your cart is empty. <a href="/coffee_haven/products">Continue shopping</a></p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td><?= $item->product->name ?></td>
                            <td>$<?= number_format($item->product->price, 2) ?></td>
                            <td><?= $item->quantity ?></td>
                            <td>$<?= number_format($item->product->price * $item->quantity, 2) ?></td>
                            <td>
                                <form action="/coffee_haven/cart/remove/<?= $item->id ?>" method="POST" style="display:inline;">
                                    <button type="submit" class="btn btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <h3>Total: $<?= number_format($total, 2) ?></h3>
                <form action="/coffee_haven/checkout" method="POST">
                    <button type="submit" class="btn btn-primary">Proceed to Checkout</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2026 Coffee Haven. All rights reserved.</p>
    </footer>
</body>
</html>
