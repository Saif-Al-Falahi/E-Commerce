<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\AdminAccessMiddleware;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;

// Register admin middleware directly
Route::aliasMiddleware('admin', AdminAccessMiddleware::class);

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Routes
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::patch('/cart/update/{cartItem}', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::delete('/cart/remove/{cartItem}', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::delete('/cart/clear/{cart}', [CartController::class, 'clearCart'])->name('cart.clear');
    Route::post('/cart/checkout/{cart}', [CartController::class, 'checkout'])->name('cart.checkout');
    
    // Order Routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{cart}', [OrderController::class, 'show'])->name('orders.show');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Coupon routes
    Route::post('/coupon/apply', [CouponController::class, 'apply'])->name('coupon.apply');
    Route::delete('/coupon/{cart}/remove', [CouponController::class, 'remove'])->name('coupon.remove');
});

// Admin Routes - Using middleware and admin guard
Route::middleware(['auth:admin', 'admin'])->prefix('admin')->group(function () {
    // Admin Dashboard
    Route::get('/', function () {
        return redirect()->route('admin.products.index');
    })->name('admin.dashboard');
    Route::get('/products', [AdminController::class, 'products'])->name('admin.products.index');
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories.index');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
    
    // Admin Orders
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders.index');
    Route::get('/orders/{cart}', [OrderController::class, 'adminShow'])->name('admin.orders.show');
    
    // Product Management
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::patch('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');

    // Category Management
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

    // Coupon Management
    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('admin.coupons.index');
    Route::get('/coupons/create', [AdminCouponController::class, 'create'])->name('admin.coupons.create');
    Route::post('/coupons', [AdminCouponController::class, 'store'])->name('admin.coupons.store');
    Route::get('/coupons/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('admin.coupons.edit');
    Route::put('/coupons/{coupon}', [AdminCouponController::class, 'update'])->name('admin.coupons.update');
    Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('admin.coupons.destroy');
});

// These routes need to come after the admin routes to avoid conflicts
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
