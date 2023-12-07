<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private $up = <<<SQL
        CREATE TABLE `login_histories` (
            `date` datetime(6) NOT NULL,
            `email` varchar(60) NOT NULL,
            `result` enum('pass','block','deny') NOT NULL,
            PRIMARY KEY (`date`,`email`)
        );

        CREATE TABLE `permissions` (
            `id` int NOT NULL AUTO_INCREMENT,
            `name` varchar(30) NOT NULL DEFAULT '',
            `backend` json NOT NULL,
            `frontend` json NOT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        );

        CREATE TABLE `roles` (
            `id` int NOT NULL AUTO_INCREMENT,
            `name` varchar(30) NOT NULL,
            `note` varchar(255) NOT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        );

        CREATE TABLE `roles_permissions` (
            `role_id` int NOT NULL AUTO_INCREMENT,
            `permission_id` int NOT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`role_id`,`permission_id`)
        );

        CREATE TABLE `sessions` (
            `key` varchar(60) NOT NULL,
            `user_id` int NOT NULL,
            `role_id` int NOT NULL,
            `expired_at` datetime NOT NULL,
            PRIMARY KEY (`key`)
        ) ENGINE=MEMORY;

        CREATE TABLE `users` (
            `id` int NOT NULL AUTO_INCREMENT,
            `email` varchar(60) NOT NULL,
            `password` varchar(60) NOT NULL,
            `role_id` int NOT NULL,
            `note` varchar(255) NOT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        );
    SQL;

    private $down = <<<SQL
        DROP TABLE IF EXISTS `login_histories`;
        DROP TABLE IF EXISTS `permissions`;
        DROP TABLE IF EXISTS `roles`;
        DROP TABLE IF EXISTS `roles_permissions`;
        DROP TABLE IF EXISTS `sessions`;
        DROP TABLE IF EXISTS `users`;
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