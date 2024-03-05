<?php

namespace App\Storage\LoginHistories;

use Illuminate\Support\Facades\DB;

/**
 * 認証履歴 - 操作クラス
 */
class Command extends Query
{
    /**
     * 認証成功レコード作成
     * 
     * @param string $email
     * @param int|null $date
     * @return void
     */
    public function createPass(string $email, int $date = null): void
    {
        DB::table('login_histories')->insert([
            'date' => date('Y-m-d H:i:s', $date),
            'email' => $email,
            'result' => self::RESULT_PASS,
        ]);
    }

    /**
     * 認証失敗レコード作成
     * 
     * @param string $email
     * @param int|null $date
     * @return void
     */
    public function createBlock(string $email, int $date = null): void
    {
        DB::table('login_histories')->insert([
            'date' => date('Y-m-d H:i:s', $date),
            'email' => $email,
            'result' => self::RESULT_BLOCK,
        ]);
    }

    /**
     * 認証拒否レコード作成
     * 
     * @param string $email
     * @param int|null $date
     * @return void
     */
    public function createDeny(string $email, int $date = null): void
    {
        DB::table('login_histories')->insert([
            'date' => date('Y-m-d H:i:s', $date),
            'email' => $email,
            'result' => self::RESULT_DENY,
        ]);
    }

    /**
     * 指定日時より前の認証履歴を削除
     * 
     * @param int $date
     * @return void
     */
    public function deleteOlder(int $date): void
    {
        DB::table('login_histories')
            ->where('date', '<', date('Y-m-d H:i:s', $date))
            ->delete();
    }
}
