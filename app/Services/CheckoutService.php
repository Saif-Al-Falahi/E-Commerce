<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class CheckoutService
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Process the checkout for a given cart and user.
     *
     * @throws \DomainException If cart is empty, already completed, or stock issues occur.
     * @throws Throwable If a database or other unexpected error occurs.
     */
    public function processCheckout(Cart $cart, User $user): Cart
    {
        if ($cart->user_id !== $user->id) {
            // This check might be redundant if cart is fetched via user relationship,
            // but good for explicit validation if cart is passed directly.
            throw new \DomainException('Cart does not belong to the user.');
        }

        if ($cart->is_completed) {
            throw new \DomainException('This cart has already been checked out.');
        }

        if ($cart->cartItems->isEmpty()) {
            throw new \DomainException('Cannot checkout an empty cart.');
        }

        DB::beginTransaction();
        try {
            // Refresh and lock cart items and products
            $cart->load(['cartItems.product' => function ($query) {
                $query->lockForUpdate();
            }]);

            // Validate stock for all items *before* decrementing
            foreach ($cart->cartItems as $item) {
                if (!$item->product) {
                    // This case should ideally not happen due to FK constraints,
                    // but check defensively.
                    Log::error("Checkout Error: Product not found for CartItem ID {$item->id}");
                    throw new \DomainException("An item in your cart is no longer available.");
                }
                if ($item->product->stock < $item->quantity) {
                    throw new \DomainException("Insufficient stock for product: {$item->product->name}. Please update your cart.");
                }
            }

            // Decrement stock for all items
            foreach ($cart->cartItems as $item) {
                 // Re-fetch product within transaction to be safe, although locking helps
                 $product = Product::find($item->product_id);
                 if ($product) { // Check again in case product was deleted mid-transaction (unlikely but safe)
                     $product->decrement('stock', $item->quantity);
                 }
            }

            // Mark the current cart as completed
            $cart->update(['completed_at' => now()]);

            // Create a new empty cart for the user
            $this->cartService->createFreshCart($user);

            DB::commit();

            // Optionally: Dispatch events (e.g., OrderPlaced)
            // event(new OrderPlaced($cart));
            
            return $cart; // Return the completed cart (now representing the order)

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Checkout failed', [
                'user_id' => $user->id,
                'cart_id' => $cart->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw the exception to be handled by the controller or exception handler
            // You might want to throw a more specific custom exception here.
            if ($e instanceof \DomainException) {
                throw $e; // Re-throw domain exceptions directly
            }
            throw new \RuntimeException('An unexpected error occurred during checkout. Please try again.', 0, $e);
        }
    }
} 