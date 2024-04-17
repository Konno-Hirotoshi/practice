<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private $up = <<<SQL
        CREATE TABLE `sessions` (
            `key` VARCHAR(60) NOT NULL,
            `user_id` INT NOT NULL,
            `department_id` INT NOT NULL,
            `role_id` INT NOT NULL,
            `expired_at` DATETIME NOT NULL,
            PRIMARY KEY (`key`)
        ) ENGINE=MEMORY;
        
        CREATE TABLE `login_histories` (
            `date` DATETIME(6) NOT NULL,
            `email` VARCHAR(60) NOT NULL,
            `result` ENUM('pass','block','deny') NOT NULL,
            PRIMARY KEY (`date`,`email`)
        );
    SQL;

    private $down = <<<SQL
        DROP TABLE IF EXISTS `login_histories`;
        DROP TABLE IF EXISTS `sessions`;
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
