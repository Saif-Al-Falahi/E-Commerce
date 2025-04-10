<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Login;
use App\Models\User;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // When a user is logged in via the web guard, check if they have an admin role
        // and authenticate them with the admin guard as well
        Auth::resolved(function ($auth) {
            // The provider method requires a driver name and a callback
            $auth->provider('users', function ($app) {
                return new EloquentUserProvider($app->make(Hasher::class), User::class);
            });
            
            $auth->extend('admin', function ($app, $name, array $config) use ($auth) {
                return $auth->createSessionDriver($name, $config);
            });
        });
        
        // Listen for login events
        $this->app['events']->listen(Login::class, function ($event) {
            if ($event->guard === 'web') {
                $user = $event->user;
                
                // If the user has an admin role, log them in with the admin guard too
                if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                    Auth::guard('admin')->login($user);
                }
            }
        });
    }
}
 