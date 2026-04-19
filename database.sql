-- SQL schema for Coffee Haven
-- Creates `coffee_haven` database, tables and some sample data.

CREATE DATABASE IF NOT EXISTS `coffee_haven` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `coffee_haven`;

-- Users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `is_admin` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock` INT NOT NULL DEFAULT 0,
  `image_path` VARCHAR(255),
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cart items (per-user temporary cart)
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `added_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders and items
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NULL,
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data (demo only)
-- NOTE: For real applications, generate password hashes with your application (e.g. PHP password_hash).
INSERT INTO `users` (`username`, `email`, `password_hash`, `is_admin`) VALUES
('admin', 'admin@example.com', SHA2('adminpass', 256), 1);

INSERT INTO `products` (`name`, `description`, `price`, `stock`, `image_path`) VALUES
('House Blend', 'Smooth medium roast with chocolate notes', 9.99, 50, 'PICTURES/house_blend.jpg'),
('Espresso Roast', 'Bold espresso roast for rich shots', 12.50, 30, 'PICTURES/espresso_roast.jpg'),
('Colombian Beans', 'Single origin Colombian beans', 11.00, 20, 'PICTURES/colombian.jpg');

-- Example cart item and order rows can be created by the application when needed.
