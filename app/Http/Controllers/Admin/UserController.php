<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class UserController extends BaseController
{
    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Toggle admin status for a user.
     */
    public function toggleAdmin(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot change your own admin status.');
        }

        if ($user->hasRole('admin')) {
            $user->roles()->detach(Role::where('name', 'admin')->first()->id);
            $message = 'Admin privileges removed.';
        } else {
            $user->assignRole('admin');
            $message = 'Admin privileges granted.';
        }

        return redirect()->back()->with('success', 'User admin status updated successfully. ' . $message);
    }
} 