<?php

namespace App\Domain\Orders;

use App\Base\SearchOption;
use App\Storage\Orders\Command as Orders;

/**
 * 取引コレクション
 */
class OrderCollection
{
    /**
     * コンストラクタ
     */
    public function __construct(private Orders $orders)
    {
    }

    /**
     * 全体を検索する
     */
    public function search(array $search = [], array $sort = [], int $page = 1, int $perPage = 3): array
    {
        $option = SearchOption::create($search, $sort, $page, $perPage, [
            'id' => 'value',
        ]);

        return $this->orders->search($option);
    }

    /**
     * 詳細情報を取得する
     */
    public function get(int $id)
    {
        return $this->orders->getEntity($id, context: __CLASS__);
    }
}
