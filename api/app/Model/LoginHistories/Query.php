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
     * @param int $date
     * @return bool
     */
    public function getRecentBlockCount(string $email, int $date): bool
    {
        $blockCount = DB::table('login_histories')
            ->where('date', '>', date('Y-m-d H:i:s', $date))
            ->where('email', $email)
            ->where('result', self::RESULT_BLOCK)
            ->count();
        return $blockCount;
    }
}
