<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\User;
use App\Http\Requests\UpdateProductStockRequest;
use App\Services\ProductService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Throwable;

final class AdminController extends Controller
{
    private ProductService $productService;
    private UserService $userService;

    public function __construct(ProductService $productService, UserService $userService)
    {
        // Middleware is applied in the routes file
        $this->productService = $productService;
        $this->userService = $userService;
    }

    /**
     * Show the admin dashboard.
     */
    public function index(): View
    {
        $stats = [
            'products_count' => Product::count(),
            'categories_count' => Category::count(),
            'users_count' => User::count(),
            'orders_count' => Cart::whereNotNull('completed_at')->count(),
        ];

        $recent_products = Product::latest()->take(5)->get();
        $low_stock_products = Product::where('stock', '<=', 10)->orderBy('stock')->get();

        return view('admin.dashboard', compact('stats', 'recent_products', 'low_stock_products'));
    }

    /**
     * Show the product management page.
     */
    public function products(): View
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('admin.products', compact('products'));
    }

    /**
     * Show the category management page.
     */
    public function categories(): View
    {
        $categories = Category::withCount('products')->latest()->paginate(10);
        return view('admin.categories', compact('categories'));
    }

    /**
     * Show the user management page.
     */
    public function users(): View
    {
        $users = User::with('roles')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Update product stock.
     */
    public function updateStock(UpdateProductStockRequest $request, Product $product): RedirectResponse
    {
        $newStock = (int) $request->validated('stock');

        try {
            $this->productService->updateStock($product, $newStock);
            return redirect()->back()->with('success', 'Product stock updated successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (Throwable $e) {
            Log::error("Error updating product stock: {$e->getMessage()}", ['exception' => $e]);
            return redirect()->back()->with('error', 'Could not update product stock. Please try again.');
        }
    }

    /**
     * Toggle admin status for a user.
     */
    public function toggleAdmin(Request $request, User $user): RedirectResponse
    {
        /** @var User $adminUser */
        $adminUser = $request->user('admin');
        
        try {
            $message = $this->userService->toggleAdminRole($user, $adminUser);
            return redirect()->back()->with('success', "User admin status updated successfully. {$message}");
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error("Error toggling admin role: {$e->getMessage()}", ['exception' => $e]);
            return redirect()->back()->with('error', 'Could not update user admin status. Please try again.');
        }
    }

    /**
     * Display admin orders page.
     */
    public function orders(): View
    {
        $orders = Cart::whereNotNull('completed_at')
                      ->with(['user', 'cartItems.product'])
                      ->latest()
                      ->paginate(10);
        
        return view('admin.orders.index', compact('orders'));
    }
} 