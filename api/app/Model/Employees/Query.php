<?php

namespace App\Model\Employees;

use Illuminate\Support\Facades\DB;

/**
 * 従業員 - 問い合わせクラス
 */
class Query
{
    /**
     * 全権限ロールID
     */
    public const SUPER_ROLE_ID = 1;

    /**
     * 従業員が存在するか
     * 
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        $count = DB::table('employees')
            ->where('id', $id)
            ->count();
        return $count > 0;
    }
}
