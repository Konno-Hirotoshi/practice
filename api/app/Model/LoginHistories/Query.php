<?php

namespace App\Model\LoginHistories;

use Illuminate\Support\Facades\DB;

/**
 * 認証履歴 - 問い合わせクラス
 */
class Query
{
    // 認証結果：通過
    public const RESULT_PASS = 'pass';
    // 認証結果：ブロック
    public const RESULT_BLOCK = 'block';
    // 認証結果：拒否
    public const RESULT_DENY = 'deny';

    /**
     * 指定日時以降にブロックされた認証履歴の数を取得する
     * 
     * @param string $email
     * @param int $period
     * @param int|null $date
     * @return int
     */
    public function getRecentBlockCount(string $email, int $period, ?int $date = null): int
    {
        $blockCount = DB::table('login_histories')
            ->where('date', '>', date('Y-m-d H:i:s', $date ?? time() - $period))
            ->where('email', $email)
            ->where('result', self::RESULT_BLOCK)
            ->count();
        return $blockCount;
    }
}
