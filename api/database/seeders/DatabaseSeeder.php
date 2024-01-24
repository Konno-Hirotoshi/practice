<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (DB::table('roles')->count() === 0) {
            $this->base();
        }
    }

    private function base()
    {
        DB::table('departments')->insertOrIgnore([
            'id' => 1,
            'full_name' => 'Information System Dept.',
            'path' => '1',
        ]);
        DB::table('roles')->insertOrIgnore([
            'id' => 1,
            'name' => 'Administrator',
        ]);
        DB::table('users')->insertOrIgnore([
            'id' => 1,
            'full_name' => 'user 01',
            'email' => 'test',
            'department_id' => 1,
            'role_id' => 1,
            'password' => '$2y$10$MkemFTIGcy5S8v5sljgLOeVtTZEK4i.2uzfBGbVyImHDQoFQmjYIG',
        ]);
    }
}
