<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product->name ?> - Coffee Haven</title>
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

    <div class="container">
        <div class="product-detail">
            <div class="product-image">
                <img src="/coffee_haven/<?= $product->image_path ?>" alt="<?= $product->name ?>">
            </div>

            <div class="product-info">
                <h1><?= $product->name ?></h1>
                <p class="description"><?= $product->description ?></p>
                
                <div class="price">
                    <h2>$<?= number_format($product->price, 2) ?></h2>
                </div>

                <div class="stock">
                    <p>In Stock: <?= $product->stock ?></p>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="/coffee_haven/cart/add" method="POST">
                        <input type="hidden" name="product_id" value="<?= $product->id ?>">
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= $product->stock ?>">
                        </div>
                        <button type="submit" class="btn btn-primary btn-large">Add to Cart</button>
                    </form>
                <?php else: ?>
                    <a href="/coffee_haven/login" class="btn btn-primary btn-large">Login to Buy</a>
                <?php endif; ?>

                <a href="/coffee_haven/products" class="btn btn-secondary">Back to Products</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Coffee Haven. All rights reserved.</p>
    </footer>
</body>
</html>
