<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // User can have a role only once
            $table->unique(['user_id', 'role_id']);
        });

        // Migrate existing admin users
        $adminUsers = DB::table('users')->where('is_admin', true)->get();
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $userRoleId = DB::table('roles')->where('name', 'user')->value('id');
        
        foreach ($adminUsers as $user) {
            DB::table('user_roles')->insert([
                'user_id' => $user->id,
                'role_id' => $adminRoleId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // Assign the user role to all users
        $allUsers = DB::table('users')->get();
        foreach ($allUsers as $user) {
            DB::table('user_roles')->insert([
                'user_id' => $user->id,
                'role_id' => $userRoleId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
