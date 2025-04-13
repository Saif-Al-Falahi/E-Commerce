<?php

declare(strict_types=1);

namespace Tests\Feature\Traits;

use App\Models\Role;
use App\Models\User;

trait HasRoles
{
    protected function assignRole(User $user, string $roleName): void
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        $user->roles()->sync([$role->id]);
    }

    protected function createUserWithRole(string $roleName): User
    {
        $user = User::factory()->create();
        $this->assignRole($user, $roleName);
        return $user;
    }
} 