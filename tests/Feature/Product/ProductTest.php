<?php

declare(strict_types=1);

namespace Tests\Feature\Product;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Feature\Traits\HasRoles;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    use HasRoles;

    public function test_product_listing_page_is_accessible(): void
    {
        $response = $this->get(route('products.index'));
        $response->assertOk();
    }

    public function test_product_details_page_is_accessible(): void
    {
        $product = Product::factory()->create();
        $response = $this->get(route('products.show', $product));
        $response->assertOk();
    }

    public function test_admin_can_create_a_product(): void
    {
        $admin = $this->createUserWithRole('admin');
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 1999,
            'stock' => 10,
            'category_id' => $category->id,
            'is_active' => true
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_admin_can_update_a_product(): void
    {
        $admin = $this->createUserWithRole('admin');
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->patch(route('admin.products.update', $product), [
            'name' => 'Updated Product',
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
            'category_id' => $product->category_id,
            'is_active' => true
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Updated Product']);
    }

    public function test_admin_can_delete_a_product(): void
    {
        $admin = $this->createUserWithRole('admin');
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_non_admin_cannot_create_a_product(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 1999,
            'stock' => 10,
            'category_id' => $category->id,
            'is_active' => true
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseMissing('products', ['name' => 'Test Product']);
    }

    public function test_admin_can_view_product_creation_page(): void
    {
        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.products.create'));

        $response->assertOk();
        $response->assertViewIs('products.create');
    }

    public function test_non_admin_cannot_access_admin_product_management(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.products.index'));

        $response->assertRedirect(route('home'));
    }

    public function test_product_formatted_price_is_correct(): void
    {
        $product = Product::factory()->create([
            'price' => 1999,
        ]);

        $this->assertEquals('AED1999.00', $product->formatted_price);
    }

    public function test_unauthenticated_user_cannot_access_admin_product_management(): void
    {
        $response = $this->get(route('admin.products.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_cannot_create_product(): void
    {
        $category = Category::factory()->create();
        
        $response = $this->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 1999,
            'stock' => 10,
            'category_id' => $category->id,
            'is_active' => true
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('products', ['name' => 'Test Product']);
    }

    public function test_unauthenticated_user_cannot_update_product(): void
    {
        $product = Product::factory()->create();
        
        $response = $this->patch(route('admin.products.update', $product), [
            'name' => 'Updated Product',
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
            'category_id' => $product->category_id,
            'is_active' => true
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('products', ['name' => 'Updated Product']);
    }

    public function test_unauthenticated_user_cannot_delete_product(): void
    {
        $product = Product::factory()->create();
        
        $response = $this->delete(route('admin.products.destroy', $product));
        
        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_non_admin_cannot_update_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->patch(route('admin.products.update', $product), [
            'name' => 'Updated Product',
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
            'category_id' => $product->category_id,
            'is_active' => true
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseMissing('products', ['name' => 'Updated Product']);
    }

    public function test_non_admin_cannot_delete_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_admin_cannot_update_nonexistent_product(): void
    {
        $admin = $this->createUserWithRole('admin');
        
        $response = $this->actingAs($admin)->patch(route('admin.products.update', 99999), [
            'name' => 'Updated Product',
            'description' => 'Test Description',
            'price' => 1999,
            'stock' => 10,
            'category_id' => 1,
            'is_active' => true
        ]);

        $response->assertNotFound();
    }

    public function test_admin_cannot_delete_nonexistent_product(): void
    {
        $admin = $this->createUserWithRole('admin');
        
        $response = $this->actingAs($admin)->delete(route('admin.products.destroy', 99999));
        
        $response->assertNotFound();
    }

    public function test_admin_cannot_create_product_with_invalid_data(): void
    {
        $admin = $this->createUserWithRole('admin');
        
        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => '', // Empty name
            'description' => '', // Empty description
            'price' => -100, // Negative price
            'stock' => -10, // Negative stock
            'category_id' => 99999, // Non-existent category
            'is_active' => true
        ]);

        $response->assertSessionHasErrors(['name', 'description', 'price', 'stock', 'category_id']);
        $this->assertDatabaseMissing('products', ['name' => '']);
    }
} 