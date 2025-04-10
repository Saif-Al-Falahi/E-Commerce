<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

final class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'coupon_id',
        'discount_amount',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'subtotal',
        'total',
        'formatted_subtotal',
        'formatted_total',
        'formatted_discount',
        'is_completed',
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cart items for the cart.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the coupon applied to the cart.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the is_completed attribute.
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->completed_at !== null;
    }

    /**
     * Calculate the subtotal price of the cart (before discount).
     */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->cartItems->sum(function ($item) {
            return (float) $item->subtotal;
        });
    }

    /**
     * Calculate the total price of the cart (after discount).
     */
    public function getTotalAttribute(): float
    {
        $subtotal = (float) $this->subtotal;
        $discount = (float) ($this->discount_amount ?? 0);
        return max(0, $subtotal - $discount);
    }

    /**
     * Get the formatted subtotal for the cart.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return $this->formatCurrency((float) $this->subtotal);
    }

    /**
     * Get the formatted total for the cart.
     */
    public function getFormattedTotalAttribute(): string
    {
        return $this->formatCurrency((float) $this->total);
    }

    /**
     * Get the formatted discount amount for the cart.
     */
    public function getFormattedDiscountAttribute(): string
    {
        return $this->formatCurrency((float) ($this->discount_amount ?? 0));
    }

    private function formatCurrency(float $amount): string
    {
        $currency = config('ecommerce.currency', ['symbol' => 'AED', 'position' => 'before']);
        $formattedAmount = number_format($amount, 2, '.', '');
        
        return $currency['position'] === 'before' 
            ? "{$currency['symbol']}{$formattedAmount}"
            : "{$formattedAmount}{$currency['symbol']}";
    }

    /**
     * Apply a coupon to the cart.
     */
    public function applyCoupon(Coupon $coupon): bool
    {
        if ($this->is_completed || !$coupon->isValidForUser($this->user)) {
            return false;
        }

        $discount = $coupon->calculateDiscount($this->subtotal);
        if ($discount <= 0) {
            return false;
        }

        DB::beginTransaction();
        try {
            // Apply coupon to cart
            $this->coupon()->associate($coupon);
            $this->discount_amount = $discount;
            $this->save();

            // Record coupon usage
            $coupon->users()->attach($this->user_id, [
                'cart_id' => $this->id,
                'discount_amount' => $discount,
                'used_at' => now(),
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Remove the applied coupon from the cart.
     */
    public function removeCoupon(): void
    {
        if ($this->coupon) {
            // Begin transaction
            DB::beginTransaction();
            try {
                // Remove coupon usage record
                $this->coupon->users()->wherePivot('cart_id', $this->id)->detach($this->user_id);

                // Remove coupon from cart
                $this->coupon()->dissociate();
                $this->discount_amount = 0;
                $this->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }
        }
    }
} 