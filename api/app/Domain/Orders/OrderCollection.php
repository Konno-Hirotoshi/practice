<?php

namespace App\Domain\Orders;

use App\Base\SearchOption;
use App\Storage\Orders\Query as Orders;

/**
 * 取引コレクション
 */
class OrderCollection
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Orders $orders,
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

        return $this->orders->search($option);
    }

    /**
     * 詳細情報取得
     */
    public function get(int $id)
    {
        return $this->orders->get($id);
    }

    /**
     * 承認フローを作成する
     */
    public function makeApprovalFlow($id)
    {
        return [1, 10, 20];
    }
}
