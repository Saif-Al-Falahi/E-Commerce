<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles table exists and create admin role
        if (DB::getSchemaBuilder()->hasTable('roles')) {
            \App\Models\Role::firstOrCreate(['name' => 'admin']);
        }
    }
}
