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
        DB::table('permissions')->insertOrIgnore([
            [
                'id' => 1,
                'label' => '利用者/Read',
                'backend' => '{"users": ["index", "show"]}',
                'frontend' => '{}',
            ],
            [
                'id' => 2,
                'label' => '利用者/Write',
                'backend' => '{"users": ["create", "edit", "delete"]}',
                'frontend' => '{}',
            ],
            [
                'id' => 3,
                'label' => '権限/Read',
                'backend' => '{"roles": ["index", "show"]}',
                'frontend' => '{}',
            ],
            [
                'id' => 4,
                'label' => '権限/Write',
                'backend' => '{"roles": ["creste", "edit", "delete"]}',
                'frontend' => '{}',
            ]
        ]);
        DB::table('departments')->insertOrIgnore([
            'id' => 1,
            'full_name' => 'Information System Dept.',
            'path' => '1',
        ]);
        DB::table('employees')->insertOrIgnore([
            'id' => 1,
            'full_name' => 'user 01',
            'department_id' => 1,
            'email' => 'test',
        ]);
        DB::table('roles')->insertOrIgnore([
            'id' => 1,
            'name' => 'Administrator',
        ]);
        DB::table('users')->insertOrIgnore([
            'id' => 1,
            'employee_id' => 1,
            'role_id' => 1,
            'password' => '$2y$10$MkemFTIGcy5S8v5sljgLOeVtTZEK4i.2uzfBGbVyImHDQoFQmjYIG',
        ]);
    }
}