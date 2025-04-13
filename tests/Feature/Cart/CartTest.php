<?php

declare(strict_types=1);

namespace Tests\Feature\Cart;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_user_can_view_their_cart()
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)->get(route('cart.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('cart.index');
    }

    public function test_user_can_add_product_to_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);
        
        $response = $this->actingAs($user)
            ->post(route('cart.add', ['product' => $product->id]), [
                'quantity' => 2
            ]);
        
        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }

    public function test_user_cannot_add_more_items_than_available_stock()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);
        
        $response = $this->actingAs($user)
            ->post(route('cart.add', ['product' => $product->id]), [
                'quantity' => 10
            ]);
        
        $response->assertSessionHasErrors('quantity');
        $this->assertDatabaseMissing('cart_items', [
            'product_id' => $product->id,
            'quantity' => 10
        ]);
    }

    public function test_user_can_update_cart_item_quantity()
    {
        $this->withoutExceptionHandling();
        
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);
        
        // Use the service directly
        $cartService = app(\App\Services\CartService::class);
        $cartService->updateItemQuantity($cart, $cartItem, 3);
        
        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 3
        ]);
    }

    public function test_user_can_remove_item_from_cart()
    {
        $this->withoutExceptionHandling();
        
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);
        
        $cartItemId = $cartItem->id;
        
        // Use the service directly
        $cartService = app(\App\Services\CartService::class);
        $cartService->removeItem($cart, $cartItem);
        
        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItemId
        ]);
    }

    public function test_user_can_clear_their_cart()
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id
        ]);
        
        $response = $this->actingAs($user)
            ->delete(route('cart.clear', ['cart' => $cart->id]));
        
        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseMissing('cart_items', ['cart_id' => $cart->id]);
    }

    public function test_cart_total_is_calculated_correctly()
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product1 = Product::factory()->create(['price' => 1999]); // $19.99 in cents
        $product2 = Product::factory()->create(['price' => 2999]); // $29.99 in cents
        
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
            'quantity' => 2
        ]);
        
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 1
        ]);
        
        $this->assertEquals(6997, $cart->total); // (19.99 * 2) + 29.99 = 69.97
    }
} 