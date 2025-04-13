<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user can view login page', function () {
    $response = $this->get(route('login'));
    
    $response->assertStatus(200);
    $response->assertViewIs('auth.login');
});

test('user can login with correct credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password')
    ]);
    
    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'password'
    ]);
    
    $response->assertRedirect(route('home'));
    $this->assertAuthenticated();
});

test('user cannot login with incorrect password', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password')
    ]);
    
    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'wrong-password'
    ]);
    
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('user cannot login with non-existent email', function () {
    $response = $this->post(route('login'), [
        'email' => 'nonexistent@example.com',
        'password' => 'password'
    ]);
    
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('user can logout', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->post(route('logout'));
    
    $response->assertRedirect(route('home'));
    $this->assertGuest();
}); 