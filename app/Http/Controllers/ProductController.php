<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;

class ProductController
{
    public function index()
    {
        $products = Product::all();
        return view('products/index', ['products' => $products]);
    }

    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return redirect('/products');
        }
        return view('products/show', ['product' => $product]);
    }

    public function addToCart($productId)
    {
        if (!isset($_SESSION['user_id'])) {
            return redirect('/login');
        }

        $product = Product::find($productId);
        if (!$product) {
            return redirect('/products');
        }

        $quantity = $_POST['quantity'] ?? 1;
        
        // Check if item already in cart
        $existingItems = CartItem::where(['user_id' => $_SESSION['user_id'], 'product_id' => $productId]);
        
        if (!empty($existingItems)) {
            $item = $existingItems[0];
            $item->update(['quantity' => $item->quantity + $quantity]);
        } else {
            CartItem::create([
                'user_id' => $_SESSION['user_id'],
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        return redirect('/cart')->withSuccess('Product added to cart');
    }

    public function viewCart()
    {
        if (!isset($_SESSION['user_id'])) {
            return redirect('/login');
        }

        $cartItems = CartItem::where('user_id', $_SESSION['user_id']);
        $total = 0;

        foreach ($cartItems as $item) {
            $product = Product::find($item->product_id);
            $item->product = $product;
            $total += $product->price * $item->quantity;
        }

        return view('cart', ['cartItems' => $cartItems, 'total' => $total]);
    }

    public function removeFromCart($cartItemId)
    {
        if (!isset($_SESSION['user_id'])) {
            return redirect('/login');
        }

        $item = CartItem::find($cartItemId);
        if ($item && $item->user_id == $_SESSION['user_id']) {
            $item->delete();
        }

        return redirect('/cart');
    }

    public function checkout()
    {
        if (!isset($_SESSION['user_id'])) {
            return redirect('/login');
        }

        $cartItems = CartItem::where('user_id', $_SESSION['user_id']);
        
        if (empty($cartItems)) {
            return redirect('/cart')->withErrors(['Cart is empty']);
        }

        $total = 0;
        foreach ($cartItems as $item) {
            $product = Product::find($item->product_id);
            $total += $product->price * $item->quantity;
        }

        // Create order
        $order = Order::create([
            'user_id' => $_SESSION['user_id'],
            'total_amount' => $total,
            'status' => 'pending'
        ]);

        // Create order items
        foreach ($cartItems as $item) {
            $product = Product::find($item->product_id);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $product->price
            ]);
        }

        // Clear cart
        foreach ($cartItems as $item) {
            $item->delete();
        }

        return redirect('/orders')->withSuccess('Order placed successfully');
    }

    public function viewOrders()
    {
        if (!isset($_SESSION['user_id'])) {
            return redirect('/login');
        }

        $orders = Order::where('user_id', $_SESSION['user_id']);
        
        foreach ($orders as $order) {
            $order->items = OrderItem::where('order_id', $order->id);
            foreach ($order->items as $item) {
                $item->product = Product::find($item->product_id);
            }
        }

        return view('orders', ['orders' => $orders]);
    }
}
