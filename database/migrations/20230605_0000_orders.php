<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private $up = <<<SQL
        CREATE TABLE `orders` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `title` varchar(60) NOT,
            `body` varchar(255) NOT NULL DEFAULT '',
            `approval_status` smallint NOT NULL,
            `owner_id` int NOT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ;
        CREATE TABLE `order_approval_flows` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `order_id` int NOT NULL,
            `sequence_no` smallint NOT NULL,
            `user_id` int NOT NULL,
            `approval_status` int NOT NULL,
            `approval_date` datetime DEFAULT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        );
        CREATE TABLE `order_approval_histories` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `order_id` int NOT NULL,
            `user_id` int NOT NULL,
            `approval_status` int NOT NULL,
            `approval_date` datetime NOT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        );
    SQL;

    private $down = <<<SQL
        DROP TABLE IF EXISTS `orders`;
        DROP TABLE IF EXISTS `order_approval_flows`;
        DROP TABLE IF EXISTS `order_approval_histories`;
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