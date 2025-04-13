<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Cart;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    /**
     * Get the user's active cart.
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class)->whereNull('completed_at')->latest();
    }

    /**
     * Get all carts for the user (including completed ones).
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the coupons that the user has used.
     */
    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class)
            ->withPivot('cart_id', 'discount_amount', 'used_at')
            ->withTimestamps();
    }

    /**
     * Get the roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if the user is an admin.
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->syncWithoutDetaching($role);
        }
    }
}
