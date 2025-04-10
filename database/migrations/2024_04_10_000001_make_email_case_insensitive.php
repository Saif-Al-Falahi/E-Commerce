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
        if (DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, we need to recreate the table with case-insensitive collation
            Schema::table('users', function (Blueprint $table) {
                $table->string('email')->collation('NOCASE')->change();
            });
        } else {
            // For MySQL/MariaDB
            DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // For SQLite, revert to default collation
            Schema::table('users', function (Blueprint $table) {
                $table->string('email')->collation('BINARY')->change();
            });
        } else {
            // For MySQL/MariaDB
            DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin');
        }
    }
}; 