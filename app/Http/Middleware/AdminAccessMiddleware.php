<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated using admin guard or has admin role
        if (!Auth::guard('admin')->check() && (!Auth::check() || !$this->checkUserIsAdmin())) {
            return redirect()->route('home')->with('error', 'Access denied. You do not have permission to access this area.');
        }

        return $next($request);
    }
    
    /**
     * Check if the authenticated user has admin role
     */
    protected function checkUserIsAdmin(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user && $user->hasRole('admin');
    }
} 