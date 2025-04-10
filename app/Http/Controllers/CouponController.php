<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class CouponController extends Controller
{
    /**
     * Apply a coupon to the cart.
     */
    public function apply(Request $request): RedirectResponse
    {
        // If code is empty, return with error
        if (empty($request->code)) {
            return redirect()->route('cart.index')
                ->with('error', 'Please enter a coupon code.');
        }

        $request->validate([
            'code' => ['string', 'exists:coupons,code'],
        ]);

        $cart = Auth::user()->cart;
        if (!$cart) {
            return redirect()->route('cart.index')
                ->with('error', 'No active cart found.');
        }

        // Check if cart already has a coupon
        if ($cart->coupon_id) {
            return redirect()->route('cart.index')
                ->with('error', 'A coupon is already applied to this cart.');
        }

        $coupon = Coupon::where('code', $request->code)->first();

        // Check if coupon is active
        if (!$coupon->is_active) {
            return redirect()->route('cart.index')
                ->with('error', 'This coupon is not active.');
        }

        // Check if coupon has started
        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            return redirect()->route('cart.index')
                ->with('error', 'This coupon is not valid yet. It starts at ' . $coupon->starts_at->format('M d, Y H:i A'));
        }

        // Check if coupon has expired
        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return redirect()->route('cart.index')
                ->with('error', 'This coupon has expired.');
        }

        // Check if user has already used this coupon
        if ($coupon->users()->where('user_id', Auth::id())->exists()) {
            return redirect()->route('cart.index')
                ->with('error', 'You have already used this coupon.');
        }

        // Check if maximum global uses reached
        if ($coupon->max_uses !== null && $coupon->users()->count() >= $coupon->max_uses) {
            return redirect()->route('cart.index')
                ->with('error', 'This coupon has reached its maximum usage limit.');
        }

        // Check minimum purchase requirement
        if ((float)$cart->subtotal < (float)$coupon->min_purchase) {
            $formattedMinPurchase = number_format((float)$coupon->min_purchase, 2);
            return redirect()->route('cart.index')
                ->with('error', "Your cart total must be at least \${$formattedMinPurchase} to use this coupon.");
        }

        // Try to apply the coupon
        if ($cart->applyCoupon($coupon)) {
            $discountAmount = (float)$cart->discount_amount;
            $formattedDiscount = number_format($discountAmount, 2);
            return redirect()->route('cart.index')
                ->with('success', "Coupon applied successfully! You saved \${$formattedDiscount}.");
        }

        return redirect()->route('cart.index')
            ->with('error', 'This coupon cannot be applied to your cart.');
    }

    /**
     * Remove the coupon from the cart.
     */
    public function remove(Cart $cart): RedirectResponse
    {
        if (!$cart->coupon_id) {
            return redirect()->route('cart.index')
                ->with('error', 'No coupon applied to this cart.');
        }

        $cart->removeCoupon();

        return redirect()->route('cart.index')
            ->with('success', 'Coupon removed successfully.');
    }
}
