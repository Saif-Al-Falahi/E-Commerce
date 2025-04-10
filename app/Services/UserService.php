<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class UserService
{
    /**
     * Toggle the admin role for a given user.
     *
     * @throws \DomainException If trying to modify self or role not found.
     */
    public function toggleAdminRole(User $userToModify, User $performingUser): string
    {
        if ($userToModify->id === $performingUser->id) {
            throw new \DomainException('You cannot change your own admin status.');
        }

        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
             Log::error('Admin role not found in database during toggle attempt.');
             throw new \DomainException('Admin role configuration error.');
        }

        if ($userToModify->hasRole('admin')) {
            // Remove admin role
            $userToModify->roles()->detach($adminRole->id);
            // Ensure the user still has the basic 'user' role if needed
             $userRole = Role::where('name', 'user')->first();
             if ($userRole && !$userToModify->roles()->where('role_id', $userRole->id)->exists()) {
                 $userToModify->roles()->attach($userRole->id);
             }
            return 'Admin privileges removed.';
        } else {
            // Add admin role
            // Note: assignRole method in User model might be simpler if it handles finding the role
            $userToModify->roles()->syncWithoutDetaching([$adminRole->id]);
            return 'Admin privileges granted.';
        }
    }
    
    // Add other user-related business logic methods here
    // e.g., user registration with role assignment, profile updates etc.
} 