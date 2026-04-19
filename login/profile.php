<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db_connect.php';
require_login();

$user_id = current_user_id();

// Fetch user info
$stmt = $pdo->prepare('SELECT id, username, email, created_at FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) {
    // Something is wrong; logout
    header('Location: logout.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update email or password
    if (!empty($_POST['email'])) {
        $newEmail = trim($_POST['email']);
        if (filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $upd = $pdo->prepare('UPDATE users SET email = ? WHERE id = ?');
            $upd->execute([$newEmail, $user_id]);
            $message = 'Profile updated.';
        } else {
            $message = 'Invalid email.';
        }
    }

    if (!empty($_POST['password'])) {
        $newPass = $_POST['password'];
        if (strlen($newPass) >= 6) {
            $hash = password_hash($newPass, PASSWORD_DEFAULT);
            $upd = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $upd->execute([$hash, $user_id]);
            $message = ($message ? $message . ' ' : '') . 'Password updated.';
        } else {
            $message = 'Password must be at least 6 characters.';
        }
    }

    // Refresh user data
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Your Profile</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    .profile-card { max-width: 700px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 8px; }
    .field { margin-bottom: 12px; }
    .submit { padding: 8px 12px; }
  </style>
</head>
<body>
  <div class="profile-card">
    <h1>Profile</h1>
    <?php if ($message): ?>
      <div style="color:green;margin-bottom:10px"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Member since:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>

    <h2>Update profile</h2>
    <form method="post">
      <div class="field">
        <label>New email</label><br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
      </div>
      <div class="field">
        <label>New password (leave blank to keep)</label><br>
        <input type="password" name="password">
      </div>
      <button type="submit" class="submit">Save changes</button>
    </form>

    <p><a href="logout.php">Log out</a></p>
    <p><a href="../my.html">Back to site</a></p>
  </div>
</body>
</html>
