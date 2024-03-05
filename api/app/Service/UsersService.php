<?php

namespace App\Service;

use App\Base\SearchOption;
use App\Storage\Users\Command as Users;

/**
 * 利用者サービス
 */
class UsersService
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Users $users,
    ) {
    }

    /**
     * 一覧検索
     */
    public function search(array $search = [], array $sort = [], int $page = 1, int $perPage = 3): array
    {
        $option = SearchOption::create($search, $sort, $page, $perPage, [
            'id' => 'value',
            'full_name' => 'like',
            'email' => 'like',
            'tags' => function ($key, $value, $query) {
                return $query;
            },
        ]);

        return $this->users->search($option);
    }

    /**
     * 詳細情報取得
     */
    public function get(int $id)
    {
        return $this->users->get($id);
    }
}
