<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default roles if they don't exist
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if (!$adminRole) {
            $adminRoleId = DB::table('roles')->insertGetId([
                'name' => 'admin',
                'description' => 'Administrator with full access',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $adminRoleId = $adminRole->id;
        }

        $userRole = DB::table('roles')->where('name', 'user')->first();
        if (!$userRole) {
            $userRoleId = DB::table('roles')->insertGetId([
                'name' => 'user',
                'description' => 'Regular user with limited access',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $userRoleId = $userRole->id;
        }

        // Create admin user if doesn't exist
        $admin = DB::table('users')->where('email', 'admin@example.com')->first();
        if (!$admin) {
            $adminId = DB::table('users')->insertGetId([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign admin role
            DB::table('user_roles')->insert([
                'user_id' => $adminId,
                'role_id' => $adminRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create regular user if doesn't exist
        $user = DB::table('users')->where('email', 'user@example.com')->first();
        if (!$user) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign user role
            DB::table('user_roles')->insert([
                'user_id' => $userId,
                'role_id' => $userRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create basic categories if they don't exist
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and accessories'],
            ['name' => 'Clothing', 'description' => 'Fashion and apparel'],
            ['name' => 'Books', 'description' => 'Books and publications'],
            ['name' => 'Home & Garden', 'description' => 'Home decor and garden supplies'],
        ];

        foreach ($categories as $category) {
            if (!DB::table('categories')->where('name', $category['name'])->exists()) {
                DB::table('categories')->insert([
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Run the product seeder
        $this->call([
            ProductSeeder::class,
        ]);
    }
}
