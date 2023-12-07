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
        DB::table('roles')->insertOrIgnore([
            'id' => 1,
            'name' => 'Administrator',
        ]);
        DB::table('users')->insertOrIgnore([
            'id' => 1,
            'email' => 'test',
            'password' => '$2y$10$MkemFTIGcy5S8v5sljgLOeVtTZEK4i.2uzfBGbVyImHDQoFQmjYIG',
            'role_id' => 1,
        ]);
        DB::table('permissions')->insertOrIgnore([
            [
                'id' => 1,
                'name' => '利用者/Read',
                'backend' => '{"users": ["index", "show"]}',
                'frontend' => '{}',
                'created_at' => '2023-03-09 15:42:35',
                'updated_at' => '2023-03-10 12:03:39'
            ],
            [
                'id' => 2,
                'name' => '利用者/Write',
                'backend' => '{"users": ["create", "edit", "delete"]}',
                'frontend' => '{}',
                'created_at' => '2023-03-09 15:43:08',
                'updated_at' => '2023-03-10 12:03:39'
            ],
            [
                'id' => 3,
                'name' => '権限/Read',
                'backend' => '{"roles": ["index", "show"]}',
                'frontend' => '{}',
                'created_at' => '2023-03-09 15:43:16',
                'updated_at' => '2023-03-10 12:03:39'
            ],
            [
                'id' => 4,
                'name' => '権限/Write',
                'backend' => '{"roles": ["creste", "edit", "delete"]}',
                'frontend' => '{}',
                'created_at' => '2023-03-09 15:43:22',
                'updated_at' => '2023-03-10 12:03:51'
            ]
        ]);
    }
}