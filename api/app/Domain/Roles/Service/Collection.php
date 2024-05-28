<?php

namespace App\Domain\Roles\Service;

use App\Base\SearchOption;
use App\Storage\Roles\Query as Roles;

/**
 * 役割 - コレクションクラス
 */
class Collection
{
    /**
     * コンストラクタ
     */
    public function __construct(private Roles $roles)
    {
    }

    /**
     * 全体を検索する
     *
     * @param array $search 検索条件
     * @param array $sort 並び替え条件
     * @param int $page ページ
     * @param int $perPage 1ページあたりの表示件数
     * @return array
     */
    public function search(array $search = [], array $sort = [], int $page = 1, int $perPage = 3): array
    {
        $option = SearchOption::create($search, $sort, $page, $perPage, [
            'id' => 'value',
        ]);

        return $this->roles->search($option);
    }

    /**
     * 詳細情報を取得する
     *
     * @param int $id 役割ID
     * @return object
     */
    public function get(int $id)
    {
        return $this->roles->get($id);
    }
}
