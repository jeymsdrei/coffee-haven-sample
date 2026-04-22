<?php

use App\Router;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

$router = new Router();

// Public routes
$router->get('/', function() {
    $controller = new ProductController();
    return $controller->index();
});

// Auth routes
$router->get('/login', function() {
    $controller = new AuthController();
    return $controller->showLogin();
});

$router->post('/login', function() {
    $controller = new AuthController();
    return $controller->login();
});

$router->get('/register', function() {
    $controller = new AuthController();
    return $controller->showRegister();
});

$router->post('/register', function() {
    $controller = new AuthController();
    return $controller->register();
});

$router->get('/logout', function() {
    $controller = new AuthController();
    return $controller->logout();
});

$router->get('/profile', function() {
    $controller = new AuthController();
    return $controller->profile();
});

// Product routes
$router->get('/products', function() {
    $controller = new ProductController();
    return $controller->index();
});

$router->get('/products/{id}', function($id) {
    $controller = new ProductController();
    return $controller->show($id);
});

$router->post('/cart/add', function() {
    if ($_POST['product_id'] ?? null) {
        $controller = new ProductController();
        return $controller->addToCart($_POST['product_id']);
    }
});

$router->get('/cart', function() {
    $controller = new ProductController();
    return $controller->viewCart();
});

$router->post('/cart/remove/{id}', function($id) {
    $controller = new ProductController();
    return $controller->removeFromCart($id);
});

$router->post('/checkout', function() {
    $controller = new ProductController();
    return $controller->checkout();
});

$router->get('/orders', function() {
    $controller = new ProductController();
    return $controller->viewOrders();
});

return $router;
