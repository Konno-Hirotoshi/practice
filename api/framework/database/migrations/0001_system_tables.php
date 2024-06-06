<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private $up = <<<SQL
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
            PRIMARY KEY (`role_id`,`permission_id`)
        );
    SQL;

    private $down = <<<SQL
        DROP TABLE IF EXISTS `roles_permissions`;
        DROP TABLE IF EXISTS `roles`;
    SQL;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement($this->up);

        // 初期データ投入 (ローカル環境のみ)
        if (App::isLocal()) {
            // 役割
            DB::table('roles')->insert([
                'id' => 1,
                'name' => 'Administrator',
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
