<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CartItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'subtotal',
        'formatted_subtotal',
    ];

    /**
     * Get the cart that owns the cart item.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product that owns the cart item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate the subtotal for this cart item.
     */
    public function getSubtotalAttribute(): float
    {
        if (!$this->product) {
            return 0.0;
        }
        return (float)($this->quantity * $this->product->price);
    }

    /**
     * Get the formatted subtotal for this cart item.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        $currency = config('ecommerce.currency', ['symbol' => 'AED', 'position' => 'before']);
        $formattedAmount = number_format($this->subtotal, 2, '.', '');
        
        return $currency['position'] === 'before' 
            ? "{$currency['symbol']}{$formattedAmount}"
            : "{$formattedAmount}{$currency['symbol']}";
    }
} 