<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

final class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_purchase',
        'max_uses',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_uses' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the carts that belong to the coupon.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the users who have used this coupon.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('cart_id', 'discount_amount', 'used_at')
            ->withTimestamps();
    }

    /**
     * Check if the coupon is valid for use by a specific user.
     */
    public function isValidForUser(User $user): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && now()->gt($this->expires_at)) {
            return false;
        }

        // Check if user has already used this coupon in any completed order
        $hasUsedCoupon = $this->users()
            ->where('user_id', $user->id)
            ->exists();

        if ($hasUsedCoupon) {
            return false;
        }

        // Check if user has this coupon in an active cart
        $hasActiveCart = Cart::where('user_id', $user->id)
            ->where('coupon_id', $this->id)
            ->whereNull('completed_at')
            ->exists();

        if ($hasActiveCart) {
            return false;
        }

        if ($this->max_uses !== null) {
            $usedCount = $this->users()->count();
            if ($usedCount >= $this->max_uses) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate the discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < (float)$this->min_purchase) {
            return 0.0;
        }

        return match($this->type) {
            'fixed' => (float)$this->value,
            'percentage' => $subtotal * ((float)$this->value / 100),
            default => 0.0,
        };
    }

    /**
     * Get the total number of times this coupon has been used.
     */
    public function getTimesUsedAttribute(): int
    {
        return $this->users()
            ->whereHas('carts', function ($query) {
                $query->whereNotNull('completed_at');
            })
            ->count();
    }
}
