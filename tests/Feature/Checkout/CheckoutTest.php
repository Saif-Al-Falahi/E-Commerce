<?php

declare(strict_types=1);

namespace Tests\Feature\Checkout;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_user_can_complete_checkout_with_valid_cart()
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);
        
        $response = $this->actingAs($user)
            ->post("/cart/checkout/{$cart->id}");
        
        $response->assertRedirect(route('orders.show', ['cart' => $cart->id]));
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'completed_at' => now()
        ]);
    }

    public function test_user_cannot_checkout_empty_cart()
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)
            ->post(route('cart.checkout', $cart));
        
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
    }

    public function test_user_cannot_checkout_another_users_cart()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user2->id]);
        
        $response = $this->actingAs($user1)
            ->post(route('cart.checkout', $cart));
        
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
    }
} 