<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class CartService
{
    /**
     * Get the active cart for the user, creating one if it doesn't exist.
     */
    public function getUserCart(User $user): Cart
    {
        // Use the relationship defined in User model which already fetches active cart
        $cart = $user->cart;

        if (!$cart) {
            $cart = $this->createFreshCart($user);
        }

        return $cart;
    }

    /**
     * Create a new, empty cart for the user, deleting any previous incomplete carts.
     */
    public function createFreshCart(User $user): Cart
    {
        // Ensure atomicity: delete old and create new in a transaction
        return DB::transaction(function () use ($user) {
            Cart::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->delete();

            return Cart::create(['user_id' => $user->id]);
        });
    }

    /**
     * Add a product to the user's cart.
     *
     * @throws \InvalidArgumentException If quantity exceeds stock.
     */
    public function addItem(Cart $cart, Product $product, int $quantity): CartItem
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            if ($newQuantity > $product->stock) {
                throw new \InvalidArgumentException('Not enough stock available.');
            }
            $cartItem->update(['quantity' => $newQuantity]);
            return $cartItem->refresh(); // Return the updated item
        } else {
             if ($quantity > $product->stock) {
                throw new \InvalidArgumentException('Not enough stock available.');
            }
            return $cart->cartItems()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }
    }

    /**
     * Update the quantity of an item in the cart.
     *
     * @throws \InvalidArgumentException If quantity exceeds stock or is invalid.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If cart item does not belong to the cart.
     */
    public function updateItemQuantity(Cart $cart, int $cartItemId, int $quantity): CartItem
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }
        
        // Ensure the cart item belongs to the provided cart
        $cartItem = $cart->cartItems()->findOrFail($cartItemId);

        if ($quantity > $cartItem->product->stock) {
            throw new \InvalidArgumentException('Not enough stock available.');
        }

        $cartItem->update(['quantity' => $quantity]);
        return $cartItem;
    }

    /**
     * Remove an item from the cart.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If cart item does not belong to the cart.
     */
    public function removeItem(Cart $cart, int $cartItemId): void
    {
        $cartItem = $cart->cartItems()->findOrFail($cartItemId);
        $cartItem->delete();
    }

    /**
     * Clear all items from the cart.
     */
    public function clearCart(Cart $cart): void
    {
        $cart->cartItems()->delete();
        // Optionally reset coupon/discount here if needed
         if ($cart->coupon) {
            // Assuming a removeCoupon method exists in Cart model or service
             $cart->removeCoupon(); 
         }
    }
} 