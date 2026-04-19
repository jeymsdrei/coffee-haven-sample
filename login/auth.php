<?php
session_start();

function require_login() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.html');
        exit;
    }
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

?>
