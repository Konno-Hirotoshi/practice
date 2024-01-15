<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private $up = <<<SQL
        CREATE TABLE `permissions` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `label` VARCHAR(30) NOT NULL,
            `backend` JSON NOT NULL,
            `frontend` JSON NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        );

        CREATE TABLE `roles` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(30) NOT NULL,
            `note` VARCHAR(255) NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        );

        CREATE TABLE `roles_permissions` (
            `role_id` INT NOT NULL AUTO_INCREMENT,
            `permission_id` INT NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`role_id`,`permission_id`)
        );

        CREATE TABLE `users` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `employee_id` INT NOT NULL,
            `role_id` INT NOT NULL,
            `password` VARCHAR(60) NOT NULL,
            `note` VARCHAR(255) NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        );

        CREATE TABLE `login_histories` (
            `date` DATETIME(6) NOT NULL,
            `email` VARCHAR(60) NOT NULL,
            `result` ENUM('pass','block','deny') NOT NULL,
            PRIMARY KEY (`date`,`email`)
        );

        CREATE TABLE `sessions` (
            `key` VARCHAR(60) NOT NULL,
            `employee_id` INT NOT NULL,
            `department_id` INT NOT NULL,
            `role_id` INT NOT NULL,
            `expired_at` DATETIME NOT NULL,
            PRIMARY KEY (`key`)
        ) ENGINE=MEMORY;
    SQL;

    private $down = <<<SQL
        DROP TABLE IF EXISTS `sessions`;
        DROP TABLE IF EXISTS `login_histories`;
        DROP TABLE IF EXISTS `users`;
        DROP TABLE IF EXISTS `roles_permissions`;
        DROP TABLE IF EXISTS `roles`;
        DROP TABLE IF EXISTS `permissions`;
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