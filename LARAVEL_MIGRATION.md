# Coffee Haven - Laravel Migration with SQLite

## Overview

Coffee Haven has been successfully converted from a traditional PHP/MySQL application to a modern Laravel-style architecture using SQLite database.

## Key Changes

### Database
- **Previous**: MySQL database (`coffee_haven` database)
- **New**: SQLite database (`coffee_haven.db` in `storage/database/`)
- **Benefits**: 
  - File-based database (easier deployment)
  - No separate database server required
  - Lightweight and performant

### Architecture
- **Previous**: Object-oriented PHP with PDO connections spread across files
- **New**: Laravel-style MVC architecture with:
  - Models in `app/Models/` (User, Product, CartItem, Order, OrderItem)
  - Controllers in `app/Http/Controllers/` (AuthController, ProductController)
  - Routes in `routes/web.php`
  - Views in `resources/views/` (Blade template-style)

### Directory Structure

```
coffee_haven/
├── app/
│   ├── Models/           # Data models
│   ├── Http/
│   │   └── Controllers/  # Application logic
│   ├── Database.php      # Database initialization
│   └── Router.php        # Routing engine
├── config/               # Configuration files
├── database/             # Database files
├── resources/
│   └── views/            # Blade templates
├── routes/
│   └── web.php          # Application routes
├── storage/
│   └── database/        # SQLite database file
├── public/
│   ├── css/             # Stylesheets
│   └── js/              # JavaScript files
└── index.php            # Application entry point
```

## Database Schema

The SQLite database contains the following tables:

### users
- id (INTEGER PRIMARY KEY)
- username (VARCHAR UNIQUE)
- email (VARCHAR UNIQUE)
- password (VARCHAR)
- is_admin (INTEGER, default 0)
- created_at (DATETIME)

### products
- id (INTEGER PRIMARY KEY)
- name (VARCHAR)
- description (TEXT)
- price (DECIMAL)
- stock (INTEGER)
- image_path (VARCHAR)
- created_at (DATETIME)

### cart_items
- id (INTEGER PRIMARY KEY)
- user_id (INTEGER FK → users.id)
- product_id (INTEGER FK → products.id)
- quantity (INTEGER)
- created_at (DATETIME)

### orders
- id (INTEGER PRIMARY KEY)
- user_id (INTEGER FK → users.id, nullable)
- total_amount (DECIMAL)
- status (VARCHAR)
- created_at (DATETIME)

### order_items
- id (INTEGER PRIMARY KEY)
- order_id (INTEGER FK → orders.id)
- product_id (INTEGER FK → products.id)
- quantity (INTEGER)
- unit_price (DECIMAL)

## Models

### User
- Manages user authentication and profiles
- Methods: find(), all(), where(), create(), save()

### Product
- Manages product catalog
- Methods: find(), all(), create(), save(), delete()

### CartItem
- Manages shopping cart items
- Methods: where(), create(), delete(), update()

### Order
- Manages customer orders
- Methods: find(), all(), where(), create(), save()

### OrderItem
- Manages individual items in orders
- Methods: where(), create()

## Controllers

### AuthController
- `showLogin()` - Display login form
- `login()` - Process login
- `showRegister()` - Display registration form
- `register()` - Process registration
- `logout()` - Log out user
- `profile()` - Display user profile

### ProductController
- `index()` - Display all products
- `show($id)` - Display product details
- `addToCart()` - Add product to cart
- `viewCart()` - Display shopping cart
- `removeFromCart()` - Remove item from cart
- `checkout()` - Process order
- `viewOrders()` - Display user's orders

## Routes

### Public Routes
- `GET /` - Home page
- `GET /products` - Products listing
- `GET /products/{id}` - Product details

### Authentication Routes
- `GET /login` - Login form
- `POST /login` - Process login
- `GET /register` - Registration form
- `POST /register` - Process registration
- `GET /logout` - Logout
- `GET /profile` - User profile

### Shopping Routes (Authenticated)
- `POST /cart/add` - Add to cart
- `GET /cart` - View cart
- `POST /cart/remove/{id}` - Remove from cart
- `POST /checkout` - Place order
- `GET /orders` - View orders

## Sample Data

The application automatically seeds initial data:

**Admin User**
- Username: admin
- Email: admin@example.com
- Password: adminpass

**Products**
1. House Blend - $9.99
2. Espresso Roast - $12.50
3. Colombian Beans - $11.00

## File-Based Architecture

The application uses a simple file-based routing system instead of a full Laravel framework:

1. **Entry Point**: `index.php`
   - Initializes session
   - Sets up autoloading
   - Initializes database
   - Routes requests

2. **Database Initialization**: `app/Database.php`
   - Creates SQLite database if needed
   - Creates all tables
   - Seeds initial data

3. **Routing**: `app/Router.php`
   - Matches URLs to controller methods
   - Supports parameterized routes

4. **Views**: Blade-style templates in `resources/views/`
   - Uses PHP syntax with HTML
   - Can be extended to use full Blade engine

## Running the Application

1. Access via browser: `http://localhost/coffee_haven/`
2. Database file will be automatically created at: `storage/database/coffee_haven.db`
3. Tables will be created on first request
4. Initial seed data will be inserted automatically

## Benefits of SQLite

- ✅ No database server required
- ✅ Entire database in a single file
- ✅ Perfect for small to medium applications
- ✅ Easy to backup (just copy the .db file)
- ✅ No configuration needed
- ✅ Suitable for development and deployment

## Migration from MySQL

If you had existing data in MySQL, you can:

1. Export MySQL data as CSV/SQL
2. Create migration scripts to import into SQLite
3. Use the provided models for data manipulation

## Future Enhancements

Consider upgrading to:
- Full Laravel framework
- PostgreSQL for larger databases
- Redis for caching
- Queue system for email notifications
- API endpoints for mobile apps

---

**Last Updated**: April 2026
**Database**: SQLite
**Architecture**: Laravel-style MVC
