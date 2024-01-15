<?php

namespace App\Model\Sessions;

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
     * @return object|null
     */
    public function get(string $key): ?object
    {
        return DB::table('sessions')
            ->where('key', $key)
            ->first([
                'employee_id',
                'department_id',
                'role_id',
                'expired_at',
            ]);
    }
}
