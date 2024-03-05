<?php

namespace App\Service;

use App\Base\SearchOption;
use App\Storage\Roles\Command as Roles;

/**
 * 役割 - サービスクラス
 */
class RolesService
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Roles $roles,
    ) {
    }

    /**
     * 一覧検索
     */
    public function search(array $search = [], array $sort = [], int $page = 1, int $perPage = 3): array
    {
        $option = SearchOption::create($search, $sort, $page, $perPage, [
            'id' => 'value',
        ]);

        return $this->roles->search($option);
    }

    /**
     * 詳細情報取得
     */
    public function get(int $id)
    {
        return $this->roles->get($id);
    }
}
