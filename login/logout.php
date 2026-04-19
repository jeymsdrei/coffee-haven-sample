<?php
session_start();
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}
session_destroy();

// Instead of redirecting to the login page, instruct the client to clear
// the lightweight client-side user and return to the shop. This prevents
// showing the login form after logout and updates the header to show
// the Sign Up button (the header script reads `coffeeHavenCurrentUser`).
// We output a small HTML payload that runs client-side JS.
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Logged out</title>
    <meta http-equiv="refresh" content="1;url=../my.html">
</head>
<body>
    <script>
        try {
            // remove the client-side current user so the header will switch to Sign Up
            localStorage.removeItem('coffeeHavenCurrentUser');
            // also remove any lightweight users if you'd like (optional)
            // localStorage.removeItem('coffeeHavenUsers');
        } catch(e) { /* ignore */ }
        // Navigate back to the shop (no login page)
        window.location.href = '/coffee_haven/my.html';
    </script>
</body>
</html>
<?php
exit;

?>
