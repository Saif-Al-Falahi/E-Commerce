<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Storage;

test('product listing page is accessible', function () {
    $response = $this->get(route('products.index'));
    $response->assertStatus(200);
    $response->assertViewIs('products.index');
});

test('product details page is accessible', function () {
    $product = Product::factory()->create();
    
    $response = $this->get(route('products.show', $product));
    $response->assertStatus(200);
    $response->assertViewIs('products.show');
    $response->assertViewHas('product');
});

test('admin can create a product', function () {
    // Create admin role if it doesn't exist
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    
    // Create admin user
    $admin = User::factory()->create();
    $admin->roles()->attach($adminRole);
    
    // Create category for the product
    $category = Category::factory()->create();
    
    $response = $this->actingAs($admin)
        ->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 9999, // $99.99 in cents
            'stock' => 10,
            'category_id' => $category->id,
            'is_active' => true
        ]);
    
    $response->assertRedirect(route('admin.products.index'));
    $this->assertDatabaseHas('products', [
        'name' => 'Test Product',
        'price' => 9999,
        'stock' => 10,
        'category_id' => $category->id
    ]);
});

test('admin can update a product', function () {
    // Create admin role if it doesn't exist
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    
    // Create admin user
    $admin = User::factory()->create();
    $admin->roles()->attach($adminRole);
    
    // Create category and product
    $category = Category::factory()->create();
    $product = Product::factory()->create(['category_id' => $category->id]);
    
    $response = $this->actingAs($admin)
        ->patch(route('admin.products.update', $product), [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price' => 19999, // $199.99 in cents
            'stock' => 20,
            'category_id' => $category->id,
            'is_active' => true
        ]);
    
    $response->assertRedirect(route('admin.products.index'));
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Product',
        'price' => 19999,
        'stock' => 20,
        'category_id' => $category->id
    ]);
});

test('admin can delete a product', function () {
    // Create admin role if it doesn't exist
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    
    // Create admin user
    $admin = User::factory()->create();
    $admin->roles()->attach($adminRole);
    
    // Create product
    $product = Product::factory()->create();
    
    $response = $this->actingAs($admin)
        ->delete(route('admin.products.destroy', $product));
    
    $response->assertRedirect(route('admin.products.index'));
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

test('non-admin cannot create a product', function () {
    // Create regular user
    $user = User::factory()->create();
    
    // Create category for the product
    $category = Category::factory()->create();
    
    $response = $this->actingAs($user)
        ->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 1999,
            'stock' => 10,
            'category_id' => $category->id,
            'is_active' => true
        ]);
    
    $response->assertRedirect(route('home'));
    $this->assertDatabaseMissing('products', ['name' => 'Test Product']);
});

test('admin can view product creation page', function () {
    // Create admin role if it doesn't exist
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    
    // Create admin user
    $admin = User::factory()->create();
    $admin->roles()->attach($adminRole);
    
    $response = $this->actingAs($admin)
        ->get(route('admin.products.create'));
    
    $response->assertOk();
    $response->assertViewIs('products.create');
});

test('non-admin cannot access admin product management', function () {
    // Create regular user
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->get(route('admin.products.index'));
    
    $response->assertRedirect(route('home'));
});

test('product formatted price is correct', function () {
    $product = Product::factory()->create([
        'price' => 1999,
    ]);
    
    $this->assertEquals('AED1999.00', $product->formatted_price);
}); 