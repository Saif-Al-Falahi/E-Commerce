<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class AdminAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var ?User $user */
        $user = Auth::user();
        
        if (!$user || !$user->hasRole('admin')) {
            return redirect()->route('home')->with('error', 'Access denied. You do not have permission to access this area.');
        }

        return $next($request);
    }
} 