<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Coffee Haven</title>
    <link rel="stylesheet" href="/coffee_haven/style.css">
    <link rel="stylesheet" href="/coffee_haven/public/css/app.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">☕ Coffee Haven</div>
            <div class="nav-links">
                <a href="/coffee_haven/">Home</a>
                <a href="/coffee_haven/login">Login</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2>Register</h2>
            
            <?php if (isset($_SESSION['errors'])): ?>
                <div class="alert alert-danger">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <p><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <form method="POST" action="/coffee_haven/register">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary">Register</button>
            </form>

            <p>Already have an account? <a href="/coffee_haven/login">Login here</a></p>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Coffee Haven. All rights reserved.</p>
    </footer>
</body>
</html>
