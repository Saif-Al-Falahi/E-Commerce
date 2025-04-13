# Laravel E-Commerce Application

## Overview

This is a full-featured e-commerce application built with Laravel. It provides a complete shopping experience with product browsing, cart management, user authentication, order processing, coupon management, and an admin dashboard.

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

6. **coupons** - Discount coupons
   - id, code, type, value, min_purchase, max_discount, starts_at, expires_at, usage_limit, times_used, is_active, created_at, updated_at

7. **cart_coupon** - Pivot table for cart-coupon relationship
   - cart_id, coupon_id, created_at, updated_at

### Models and Relationships

1. **User Model**
   - Has one cart
   - Has many orders

2. **Category Model**
   - Has many products

3. **Product Model**
   - Belongs to a category
   - Has many cart items
   - Has formatted price attribute

4. **Cart Model**
   - Belongs to a user
   - Has many cart items
   - Belongs to many coupons
   - Has a total attribute
   - Uses completed_at to track order completion

5. **CartItem Model**
   - Belongs to a cart
   - Belongs to a product
   - Has a subtotal attribute

6. **Coupon Model**
   - Belongs to many carts
   - Has validation rules for usage

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
   - applyCoupon: Apply a coupon to the cart
   - removeCoupon: Remove a coupon from the cart
   - checkout: Process order and reduce product stock

4. **OrderController**
   - index: Display user's order history
   - show: Display details of a specific order
   - adminIndex: Display all orders (admin only)
   - adminShow: Display order details (admin only)

5. **CouponController**
   - index: Display all coupons (admin only)
   - create: Show coupon creation form (admin only)
   - store: Save a new coupon (admin only)
   - edit: Show coupon edit form (admin only)
   - update: Update a coupon (admin only)
   - destroy: Delete a coupon (admin only)

6. **AdminController**
   - index: Admin dashboard with statistics
   - products: Manage products
   - categories: Manage categories
   - orders: View and manage orders
   - users: Manage user accounts
   - coupons: Manage discount coupons

### Services

1. **CartService**
   - getActiveCart: Get or create user's active cart
   - addItem: Add a product to cart
   - updateItemQuantity: Update cart item quantity
   - removeItem: Remove item from cart
   - clearCart: Remove all items from cart
   - applyCoupon: Apply a coupon to the cart
   - removeCoupon: Remove a coupon from the cart
   - calculateTotal: Calculate cart total with discounts

2. **OrderService**
   - createOrder: Create a new order from cart
   - processOrder: Process order and reduce product stock

3. **CouponService**
   - validateCoupon: Validate coupon code and conditions
   - applyDiscount: Apply discount to cart total

### Middleware

1. **AdminAccessMiddleware**
   - Ensures only users with admin role can access admin routes
   - Redirects non-admin users to home page with error message

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

6. **Coupons**
   - admin/coupons/index.blade.php: Coupon listing page (admin)
   - admin/coupons/create.blade.php: Coupon creation form (admin)
   - admin/coupons/edit.blade.php: Coupon edit form (admin)
   - admin/coupons/form.blade.php: Reusable coupon form (admin)

7. **Admin Dashboard**
   - dashboard.blade.php: Statistics and recent products
   - products.blade.php: Product management
   - categories.blade.php: Category management
   - orders.blade.php: Order management
   - coupons.blade.php: Coupon management

### Authentication

- Uses Laravel's built-in authentication system
- Supports user registration and login
- Includes admin role for product/category/coupon management
- Role-based access control via middleware
- Secure password hashing and validation

### Security Features

- CSRF protection for all forms
- XSS prevention through proper escaping
- Input validation using Form Requests
- Role-based authorization
- Secure password handling
- Protection against SQL injection via Eloquent ORM
- Middleware for route protection

### Testing

The application includes comprehensive tests:

1. **Feature Tests**
   - Product tests: CRUD operations, authorization, validation
   - Cart tests: Adding, updating, removing items, applying coupons
   - Order tests: Creating orders, processing payments
   - Authentication tests: Login, registration, password reset
   - Authorization tests: Role-based access control

2. **Unit Tests**
   - Model tests: Relationships, attributes, scopes
   - Service tests: Business logic, calculations
   - Helper tests: Utility functions

### Styling

- Uses Tailwind CSS for styling
- Custom CSS classes defined in resources/css/app.css
- Responsive design for mobile and desktop
- Modern UI components for enhanced user experience

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
12. Run tests:
    ```
    php artisan test
    ```

## Features

### Shopping Experience
- Product browsing with search functionality
- Advanced filtering and sorting options
- Category-based product filtering
- Product detail pages with images and description
- Responsive design for all devices

### Cart and Checkout
- Shopping cart with quantity adjustment
- Coupon application for discounts
- Checkout process with stock reduction
- Order confirmation and history
- Secure payment processing

### User Management
- User registration and authentication
- Profile management
- Order history viewing
- Responsive design for all devices

### Coupon Management
- Create and manage discount coupons
- Set coupon types (percentage or fixed amount)
- Define usage limits and expiration dates
- Apply coupons to shopping carts
- Track coupon usage

### Admin Features
- Comprehensive admin dashboard
- Product management (CRUD operations)
- Category management (CRUD operations)
- Order management and tracking
- User management
- Coupon management
- Inventory management with stock control
- Sales reporting and analytics

## License

This project is open-sourced software licensed under the Saif License (just a joke (for legal purposes))
