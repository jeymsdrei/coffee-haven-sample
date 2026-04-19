<?php
// Redirect root to the main homepage
// When visiting http://localhost/coffee_haven/ this file ensures
// users see `my.html` rather than the login page.
header('Location: /coffee_haven/my.html');
exit;
?>

