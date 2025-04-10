<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class OrderController extends Controller
{
    /**
     * Display a list of completed orders for the authenticated user
     */
    public function index(): View
    {
        $orders = Cart::where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->with(['cartItems.product', 'coupon']) // Eager load relationships
            ->latest('completed_at')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }
    
    /**
     * Show details of a specific order
     */
    public function show(Cart $cart): View|RedirectResponse
    {
        if (!$cart->completed_at) {
            return redirect()->route('orders.index')
                ->with('error', 'The requested order was not found.');
        }
        
        if (!Auth::user()->is_admin && $cart->user_id !== Auth::id()) {
            return redirect()->route('orders.index')
                ->with('error', 'You are not authorized to view this order.');
        }

        // Eager load relationships
        $cart->load(['cartItems.product', 'coupon']);
        
        return view('orders.show', compact('cart'));
    }
    
    /**
     * Display all orders for admin
     */
    public function adminIndex(): View
    {
        $orders = Cart::whereNotNull('completed_at')
            ->with(['user', 'cartItems.product', 'coupon'])
            ->latest('completed_at')
            ->paginate(20);
        
        return view('admin.orders.index', compact('orders'));
    }
    
    /**
     * Show order details for admin
     */
    public function adminShow(Cart $cart): View|RedirectResponse
    {
        if (!$cart->completed_at) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'The requested order was not found.');
        }

        // Eager load relationships
        $cart->load(['user', 'cartItems.product', 'coupon']);
        
        return view('admin.orders.show', compact('cart'));
    }
}
