<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user can view registration page', function () {
    $response = $this->get(route('register'));
    $response->assertStatus(200);
    $response->assertViewIs('auth.register');
});

test('user can register with valid data', function () {
    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password'
    ]);
    
    $response->assertRedirect(route('home'));
    $this->assertAuthenticated();
    
    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com'
    ]);
});

test('user cannot register with existing email', function () {
    User::factory()->create([
        'email' => 'test@example.com'
    ]);
    
    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password'
    ]);
    
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('user cannot register with mismatched passwords', function () {
    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'different-password'
    ]);
    
    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('user cannot register with invalid email format', function () {
    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'invalid-email',
        'password' => 'password',
        'password_confirmation' => 'password'
    ]);
    
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
}); 