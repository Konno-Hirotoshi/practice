<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private $up = <<<SQL
        CREATE TABLE `orders` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(60) NOT NULL,
            `body` VARCHAR(255) NOT NULL DEFAULT '',
            `approval_status`  SMALLINT UNSIGNED NOT NULL,
            `owner_user_id` INT NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        );
        CREATE TABLE `order_approval_flows` (
            `order_id` INT NOT NULL,
            `sequence_no` SMALLINT UNSIGNED NOT NULL,
            `approval_user_id` INT NOT NULL,
            `approval_status` SMALLINT UNSIGNED NOT NULL,
            `approval_date` DATETIME DEFAULT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`order_id`, `sequence_no`)
        );
        CREATE TABLE `order_approval_histories` (
            `order_id` INT NOT NULL,
            `approval_user_id` INT NOT NULL,
            `approval_status` INT UNSIGNED NOT NULL,
            `approval_date` DATETIME NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`order_id`, `sequence_no`)
        );
    SQL;

    private $down = <<<SQL
        DROP TABLE IF EXISTS `order_approval_histories`;
        DROP TABLE IF EXISTS `order_approval_flows`;
        DROP TABLE IF EXISTS `orders`;
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
