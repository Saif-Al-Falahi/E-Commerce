<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

final class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(): View
    {
        $products = Product::with('category')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Update product stock.
     */
    public function updateStock(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $product->update(['stock' => $request->stock]);

        return redirect()->back()->with('success', 'Product stock updated successfully.');
    }

    /**
     * Show low stock products.
     */
    public function lowStock(): View
    {
        $products = Product::where('stock', '<', 10)->with('category')->paginate(10);
        return view('admin.products.low-stock', compact('products'));
    }
} 