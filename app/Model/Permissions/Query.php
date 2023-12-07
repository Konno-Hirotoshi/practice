<?php

namespace App\Model\Permissions;

use Illuminate\Support\Facades\DB;

/**
 * 権限 - 問い合わせクラス
 */
class Query
{
    /**
     * 指定IDが存在するか判定する
     * 
     * @param array|int $permissionIds
     * @return bool
     */
    public function exists(array|int $permissionIds): bool
    {
        $count = DB::table('permissions')
            ->whereIn('id', $permissionIds)
            ->count();
        return $count > 0;
    }
}
