<?php

namespace App\Base;

/**
 * カスタム検索
 */
class SearchOption
{
    /**
     * コンストラクタ
     */
    public function __construct(
        public readonly array $searchCondition,
        public readonly array $sortCondition,
        public readonly int $currentPage,
        public readonly int $perPage,
        public readonly array $searchRule,
    ) {
        if ($this->currentPage < 1 || $this->perPage < 1) {
            throw new CustomException('invalid_parameters');
        }
    }

    /**
     * 生成メソッド
     */
    public static function create(
        array $search,
        array $sort,
        int $page,
        int $perPage,
        array $rules
    ): self {
        $sort = $sort ?: [
            'id' => 'asc',
        ];
        return new self($search, $sort, $page, $perPage, $rules);
    }

    /**
     * コンストラクタで受け取ったパラメータを基にwhere, order by, limit, offset 句を生成してSQLを実行,
     * 実行結果を取得する
     */
    public function getResults(object $query)
    {
        // Search
        foreach ($this->searchRule ?? [] as $key => $callable) {
            $value = $this->searchCondition[$key] ?? '';
            if ($value !== '') {
                if (is_string($callable)) {
                    self::$callable($key, $value, $query, $this->searchCondition);
                } else {
                    $callable($key, $value, $query, $this->searchCondition);
                }
            }
        }
        // Sort
        foreach ($this->sortCondition as $column => $order) {
            if (!in_array($column, $query->columns) || !in_array($order, ['asc', 'desc'])) {
                throw new CustomException('invalid_sort_orders');
            }
            $query->orderBy($column, $order);
        }
        // Paginate
        $searcher = $query->paginate(
            page: $this->currentPage,
            perPage: $this->perPage,
        );
        return [
            'data' => $searcher->items(),
            'total' => $searcher->total(),
        ];
    }

    public function value($key, $value, $query)
    {
        return $query->where($key, $value);
    }

    public function like($key, $value, $query)
    {
        return $query->where($key, 'LIKE', '%' . $value . '%');
    }

    public function range($key, $value, $query)
    {
        $from = $value['from'] ?? null;
        $to = $value['to'] ?? null;
        if ($from === null || $to === null) {
            return $query;
        }
        return $query->whereBetween($key, $from, $to);
    }
}
