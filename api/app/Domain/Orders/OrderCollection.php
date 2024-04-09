<?php

namespace App\Domain\Orders;

use App\Base\SearchOption;
use App\Storage\Orders\Query as Orders;

/**
 * 取引 - コレクションクラス
 */
class OrderCollection
{
    /**
     * コンストラクタ
     *
     * @param Orders $orders 取引
     */
    public function __construct(private Orders $orders)
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

        return $this->orders->search($option);
    }

    /**
     * 詳細情報を取得する
     *
     * @param int $id 取引ID
     * @return object
     */
    public function get(int $id)
    {
        return $this->orders->get($id);
    }
}
