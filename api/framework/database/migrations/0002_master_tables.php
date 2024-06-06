<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private $up = <<<SQL
        CREATE TABLE `departments` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `full_name` VARCHAR(60) NOT NULL,
            `path` VARCHAR(60) NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        );
        
        CREATE TABLE `users` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `full_name` VARCHAR(60) NOT NULL,
            `email` VARCHAR(60) NOT NULL,
            `department_id` INT NOT NULL,
            `role_id` INT NOT NULL,
            `password` VARCHAR(60) NOT NULL,
            `note` VARCHAR(255) NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        );
    SQL;

    private $down = <<<SQL
        DROP TABLE IF EXISTS `users`;
        DROP TABLE IF EXISTS `departments`;
    SQL;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement($this->up);

        // 初期データ投入 (ローカル環境のみ)
        if (App::isLocal()) {
            // 部門
            DB::table('departments')->insert([
                'id' => 1,
                'full_name' => 'Information System Dept.',
                'path' => '1',
            ]);
            // 利用者
            DB::table('users')->insert([
                'id' => 1,
                'full_name' => 'user 01',
                'email' => 'test',
                'department_id' => 1,
                'role_id' => 1,
                'password' => '$2y$10$MkemFTIGcy5S8v5sljgLOeVtTZEK4i.2uzfBGbVyImHDQoFQmjYIG',
                'note' => '',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement($this->down);
    }
};
