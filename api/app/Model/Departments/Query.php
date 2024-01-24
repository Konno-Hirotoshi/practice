<?php

namespace App\Model\Departments;

use Illuminate\Support\Facades\DB;

/**
 * 部署 - 問い合わせクラス
 */
class Query
{
    /**
     * 部署が存在するか
     * 
     * @param array|int $ids
     * @return bool
     */
    public function exists(array|int $ids): bool
    {
        $count = DB::table('departments')
            ->whereIn('id', (array)$ids)
            ->count();
        return $count > 0;
    }
}
