<?php

namespace App\Storage\Sessions;

use Illuminate\Support\Facades\DB;

/**
 * セッション - 問い合わせクラス
 */
class Query
{
    /**
     * セッションを取得する
     * 
     * @param string $key
     * @return ?object
     */
    public function get(string $key): ?object
    {
        return DB::table('sessions')
            ->where('key', $key)
            ->first([
                'user_id',
                'department_id',
                'role_id',
                'expired_at',
            ]);
    }
}
