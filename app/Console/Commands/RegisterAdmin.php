<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class RegisterAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:register {name} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        // Check if email is already taken
        if (User::where('email', $email)->exists()) {
            $this->error("User with email '{$email}' already exists!");
            
            if ($this->confirm('Do you want to make this user an admin?', true)) {
                $user = User::where('email', $email)->first();
                $this->assignAdminRole($user);
                return;
            }
            
            return 1;
        }

        // Create the user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        // Assign admin role
        $this->assignAdminRole($user);
        
        $this->info("Admin user '{$name}' created successfully!");
        return 0;
    }
    
    /**
     * Assign admin role to a user
     */
    protected function assignAdminRole(User $user)
    {
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();
        
        if (!$adminRole) {
            $this->error("Admin role not found!");
            return;
        }
        
        // Assign admin role
        $user->roles()->syncWithoutDetaching([$adminRole->id]);
        
        // Also ensure they have the user role
        if ($userRole) {
            $user->roles()->syncWithoutDetaching([$userRole->id]);
        }
        
        $this->info("Admin role assigned to user '{$user->name}'!");
    }
}
