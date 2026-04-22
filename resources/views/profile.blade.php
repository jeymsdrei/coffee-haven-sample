<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Coffee Haven</title>
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
        <div class="profile-container">
            <h2>My Profile</h2>
            
            <div class="profile-info">
                <p><strong>Username:</strong> <?= $user->username ?></p>
                <p><strong>Email:</strong> <?= $user->email ?></p>
                <p><strong>Account Type:</strong> <?= $user->is_admin ? 'Administrator' : 'Customer' ?></p>
                <p><strong>Member Since:</strong> <?= $user->created_at ?></p>
            </div>

            <div class="profile-actions">
                <a href="/coffee_haven/orders" class="btn btn-primary">View Orders</a>
                <a href="/coffee_haven/products" class="btn btn-secondary">Continue Shopping</a>
                <a href="/coffee_haven/logout" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Coffee Haven. All rights reserved.</p>
    </footer>
</body>
</html>
