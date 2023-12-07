<?php

namespace App\Service\Orders\Support;

use App\Base\BaseValidator;
use App\Model\Orders\Query as Orders;

/**
 * 取引バリデーション
 */
class Validation extends BaseValidator
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Orders $orders,
    ) {
    }

    /**
     * バリデーションチェック: タイトル
     */
    public function validateTitle(?string $title): self
    {
        return $this;
    }

    /**
     * バリデーションチェック: 本文
     */
    public function validateBody(?string $body): self
    {
        return $this;
    }

    /**
     * バリデーションチェック: 削除IDリスト
     */
    public function validateDeleteIds(array $deleteIds): self
    {
        return $this;
    }
}
