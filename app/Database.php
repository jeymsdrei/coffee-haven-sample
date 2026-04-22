<?php

namespace App;

class Database
{
    private static $pdo;

    public static function initialize()
    {
        $dbPath = __DIR__ . '/../storage/database/coffee_haven.db';
        
        // Create database file if it doesn't exist
        if (!file_exists($dbPath)) {
            touch($dbPath);
        }

        self::$pdo = new \PDO('sqlite:' . $dbPath);
        self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Create tables
        self::createTables();
        
        return self::$pdo;
    }

    public static function getConnection()
    {
        if (!self::$pdo) {
            self::initialize();
        }
        return self::$pdo;
    }

    private static function createTables()
    {
        $pdo = self::$pdo;

        // Users table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username VARCHAR(100) UNIQUE NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                is_admin INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Products table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) DEFAULT 0,
                stock INTEGER DEFAULT 0,
                image_path VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Cart items table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS cart_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                product_id INTEGER NOT NULL,
                quantity INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )
        ");

        // Orders table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                total_amount DECIMAL(10,2) DEFAULT 0,
                status VARCHAR(50) DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            )
        ");

        // Order items table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS order_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                product_id INTEGER NOT NULL,
                quantity INTEGER DEFAULT 1,
                unit_price DECIMAL(10,2) DEFAULT 0,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )
        ");

        // Seed sample data if no products exist
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($result['count'] == 0) {
            // Seed admin user
            $adminPassword = password_hash('adminpass', PASSWORD_DEFAULT);
            $pdo->exec("INSERT INTO users (username, email, password, is_admin) VALUES ('admin', 'admin@example.com', '$adminPassword', 1)");

            // Seed products
            $pdo->exec("INSERT INTO products (name, description, price, stock, image_path) VALUES ('House Blend', 'Smooth medium roast with chocolate notes', 9.99, 50, 'PICTURES/house_blend.jpg')");
            $pdo->exec("INSERT INTO products (name, description, price, stock, image_path) VALUES ('Espresso Roast', 'Bold espresso roast for rich shots', 12.50, 30, 'PICTURES/espresso_roast.jpg')");
            $pdo->exec("INSERT INTO products (name, description, price, stock, image_path) VALUES ('Colombian Beans', 'Single origin Colombian beans', 11.00, 20, 'PICTURES/colombian.jpg')");
        }
    }
}
