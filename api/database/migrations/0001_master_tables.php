<?php

use Illuminate\Database\Migrations\Migration;
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement($this->down);
    }
};
