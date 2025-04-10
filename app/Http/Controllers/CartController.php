<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User; // Import User model
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartQuantityRequest;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request; // Keep Request for Auth::user()
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable; // Import Throwable
use Illuminate\Database\Eloquent\ModelNotFoundException;


final class CartController extends Controller
{
    private CartService $cartService;
    private CheckoutService $checkoutService;

    public function __construct(CartService $cartService, CheckoutService $checkoutService)
    {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
    }

    /**
     * Display the user's active cart.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $cart = $this->cartService->getUserCart($user);
        return view('cart.index', compact('cart'));
    }

    /**
     * Add a product to the user's active cart.
     */
    public function addToCart(AddToCartRequest $request, Product $product): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user(); // Get authenticated user from request
        $cart = $this->cartService->getUserCart($user);
        $quantity = (int) $request->validated('quantity');

        try {
            $this->cartService->addItem($cart, $product, $quantity);
            return redirect()->route('cart.index')
                ->with('success', 'Product added to cart successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (Throwable $e) {
            Log::error("Error adding item to cart: {$e->getMessage()}", ['exception' => $e]);
            return redirect()->back()->with('error', 'Could not add item to cart. Please try again.');
        }
    }

    /**
     * Update the quantity of a cart item in the user's active cart.
     */
    public function updateQuantity(UpdateCartQuantityRequest $request, int $cartItemId): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $cart = $this->cartService->getUserCart($user);
        $quantity = (int) $request->validated('quantity');

        try {
             // Use cartItemId directly passed from route binding (assuming route is {cartItem})
             $this->cartService->updateItemQuantity($cart, $cartItemId, $quantity);
             return redirect()->route('cart.index')
                ->with('success', 'Cart updated successfully.');
        } catch (ModelNotFoundException $e) {
             Log::warning("Attempted to update quantity for non-existent or unauthorized cart item", ['cart_item_id' => $cartItemId, 'user_id' => $user->id]);
             return redirect()->route('cart.index')->with('error', 'Cart item not found or access denied.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        } catch (Throwable $e) {
             Log::error("Error updating cart item quantity: {$e->getMessage()}", ['exception' => $e]);
             return redirect()->route('cart.index')->with('error', 'Could not update cart item. Please try again.');
        }
    }

    /**
     * Remove a product from the user's active cart.
     */
    public function removeFromCart(Request $request, int $cartItemId): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $cart = $this->cartService->getUserCart($user);

        try {
             // Add authorization check here before deleting
             $item = $cart->cartItems()->findOrFail($cartItemId);
             $this->cartService->removeItem($cart, $cartItemId); // removeItem already does findOrFail
             return redirect()->route('cart.index')
                ->with('success', 'Product removed from cart successfully.');
        } catch (ModelNotFoundException $e) {
             Log::warning("Attempted to remove non-existent or unauthorized cart item", ['cart_item_id' => $cartItemId, 'user_id' => $user->id]);
             return redirect()->route('cart.index')->with('error', 'Cart item not found or access denied.');
        } catch (Throwable $e) {
             Log::error("Error removing cart item: {$e->getMessage()}", ['exception' => $e]);
             return redirect()->route('cart.index')->with('error', 'Could not remove item from cart. Please try again.');
        }
    }

    /**
     * Clear the user's active cart.
     */
    public function clearCart(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $cart = $this->cartService->getUserCart($user);

        try {
             $this->cartService->clearCart($cart);
             return redirect()->route('cart.index')
                ->with('success', 'Cart cleared successfully.');
        } catch (Throwable $e) {
            Log::error("Error clearing cart: {$e->getMessage()}", ['exception' => $e]);
            return redirect()->route('cart.index')->with('error', 'Could not clear the cart. Please try again.');
        }
    }

    /**
     * Handle the checkout process for the user's active cart.
     */
    public function checkout(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $cart = $this->cartService->getUserCart($user);

        try {
            $completedOrder = $this->checkoutService->processCheckout($cart, $user);
            
            // Attempt to redirect to the completed order details page
            try {
                return redirect()->route('orders.show', $completedOrder)
                    ->with('success', 'Order completed successfully! Thank you for your purchase.');
            } catch (Throwable $e) {
                Log::warning('Checkout successful but failed to redirect to orders.show', [
                    'cart_id' => $completedOrder->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                // Fallback redirect to cart index (which will show the new empty cart)
                return redirect()->route('cart.index')
                    ->with('success', 'Order completed successfully! Thank you for your purchase.');
            }
            
        } catch (\DomainException $e) {
             // Handle specific business logic errors (stock, empty cart, etc.)
             return redirect()->route('cart.index')->with('error', $e->getMessage());
        } catch (Throwable $e) {
             // Handle unexpected errors (database, etc.)
             Log::error('Checkout process failed', ['exception' => $e, 'user_id' => $user->id, 'cart_id' => $cart->id]);
            return redirect()->route('cart.index')
                ->with('error', 'An unexpected error occurred while processing your order. Please try again.');
        }
    }
    
    // Note: The private createCart method is removed as this logic is now in CartService
} 