<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Haven - Premium Coffee Shop</title>
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/coffee_haven/cart">Cart</a>
                    <a href="/coffee_haven/orders">My Orders</a>
                    <a href="/coffee_haven/profile">Profile</a>
                    <a href="/coffee_haven/logout">Logout</a>
                <?php else: ?>
                    <a href="/coffee_haven/login">Login</a>
                    <a href="/coffee_haven/register">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <header class="hero">
        <h1>Welcome to Coffee Haven</h1>
        <p>Experience the finest coffee beans from around the world</p>
        <a href="/coffee_haven/products" class="btn btn-primary">Shop Now</a>
    </header>

    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-danger">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <section class="featured-products">
            <h2>Featured Products</h2>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="/coffee_haven/<?= $product->image_path ?>" alt="<?= $product->name ?>">
                        <h3><?= $product->name ?></h3>
                        <p><?= substr($product->description, 0, 60) ?>...</p>
                        <div class="price">$<?= number_format($product->price, 2) ?></div>
                        <div class="stock">Stock: <?= $product->stock ?></div>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form action="/coffee_haven/cart/add" method="POST">
                                <input type="hidden" name="product_id" value="<?= $product->id ?>">
                                <input type="number" name="quantity" value="1" min="1" max="<?= $product->stock ?>">
                                <button type="submit" class="btn btn-secondary">Add to Cart</button>
                            </form>
                        <?php else: ?>
                            <a href="/coffee_haven/login" class="btn btn-secondary">Login to Buy</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <footer>
        <p>&copy; 2026 Coffee Haven. All rights reserved.</p>
    </footer>
</body>
</html>
