<?php

namespace App\Domain\Users;

use App\Base\SearchOption;
use App\Storage\Users\Query as Users;

/**
 * 利用者コレクション
 */
class UserCollection
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
