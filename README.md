# Laravel E-Commerce Application

## Overview

This is a full-featured e-commerce application built with Laravel. It provides a complete shopping experience with product browsing, cart management, user authentication, order processing, and an admin dashboard.

## Architecture

### Database Structure

The application uses SQLite as its database and includes the following tables:

1. **users** - Stores user account information
   - id, name, email, password, is_admin, created_at, updated_at

2. **categories** - Product categories
   - id, name, description, created_at, updated_at

3. **products** - Product information
   - id, name, description, price, stock, image, category_id, created_at, updated_at

4. **carts** - Shopping carts
   - id, user_id, completed_at, created_at, updated_at

5. **cart_items** - Items in shopping carts
   - id, cart_id, product_id, quantity, created_at, updated_at

### Models and Relationships

1. **User Model**
   - Has one cart

2. **Category Model**
   - Has many products

3. **Product Model**
   - Belongs to a category
   - Has many cart items

4. **Cart Model**
   - Belongs to a user
   - Has many cart items
   - Has a total attribute
   - Uses completed_at to track order completion

5. **CartItem Model**
   - Belongs to a cart
   - Belongs to a product
   - Has a subtotal attribute

### Controllers

1. **ProductController**
   - index: Display all products with search, filtering, and sorting
   - show: Display a single product
   - create: Show product creation form
   - store: Save a new product
   - edit: Show product edit form
   - update: Update a product
   - destroy: Delete a product

2. **CategoryController**
   - index: Display all categories
   - show: Display products in a category
   - create: Show category creation form
   - store: Save a new category
   - edit: Show category edit form
   - update: Update a category
   - destroy: Delete a category

3. **CartController**
   - index: Display user's cart
   - addToCart: Add a product to cart
   - updateQuantity: Update cart item quantity
   - removeFromCart: Remove item from cart
   - clearCart: Remove all items from cart
   - checkout: Process order and reduce product stock

4. **OrderController**
   - index: Display user's order history
   - show: Display details of a specific order
   - adminIndex: Display all orders (admin only)
   - adminShow: Display order details (admin only)

5. **AdminController**
   - index: Admin dashboard with statistics
   - products: Manage products
   - categories: Manage categories
   - orders: View and manage orders
   - users: Manage user accounts

### Views

1. **Layout**
   - app.blade.php: Main layout template with navigation
   - admin/layouts/app.blade.php: Admin layout template

2. **Products**
   - index.blade.php: Product listing page with search and filters
   - show.blade.php: Product detail page
   - create.blade.php: Product creation form
   - edit.blade.php: Product edit form
   - _form.blade.php: Reusable product form

3. **Categories**
   - index.blade.php: Category listing page
   - show.blade.php: Category detail page with products
   - create.blade.php: Category creation form
   - edit.blade.php: Category edit form

4. **Cart**
   - index.blade.php: Shopping cart page with checkout functionality

5. **Orders**
   - index.blade.php: User's order history
   - show.blade.php: Order details for user
   - admin/orders/index.blade.php: All orders (admin)
   - admin/orders/show.blade.php: Order details with customer info (admin)

6. **Admin Dashboard**
   - dashboard.blade.php: Statistics and recent products
   - products.blade.php: Product management
   - categories.blade.php: Category management
   - orders.blade.php: Order management

### Authentication

- Uses Laravel's built-in authentication system
- Supports user registration and login
- Includes admin role for product/category management
- Role-based access control via middleware

### Styling

- Uses Tailwind CSS for styling
- Custom CSS classes defined in resources/css/app.css
- Responsive design for mobile and desktop

## Setup Instructions

1. Clone the repository
2. Install dependencies:
   ```
   composer install
   npm install
   ```
3. Create .env file:
   ```
   cp .env.example .env
   ```
4. Generate application key:
   ```
   php artisan key:generate
   ```
5. Configure database in .env:
   ```
   DB_CONNECTION=sqlite
   ```
6. Create the database file:
   ```
   touch database/database.sqlite
   ```
7. Run migrations:
   ```
   php artisan migrate
   ```
8. Seed the database:
   ```
   php artisan db:seed
   ```
9. Create storage link:
   ```
   php artisan storage:link
   ```
10. Compile assets:
    ```
    npm run dev
    ```
11. Start the server:
    ```
    php artisan serve
    ```

## Features

### Shopping Experience
- Product browsing with search functionality
- Advanced filtering and sorting options
- Category-based product filtering
- Product detail pages with images and description

### Cart and Checkout
- Shopping cart with quantity adjustment
- Checkout process with stock reduction
- Order confirmation and history

### User Management
- User registration and authentication
- Profile management
- Order history viewing
- Responsive design for all devices

### Admin Features
- Comprehensive admin dashboard
- Product management (CRUD operations)
- Category management (CRUD operations)
- Order management and tracking
- User management
- Inventory management with stock control

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
