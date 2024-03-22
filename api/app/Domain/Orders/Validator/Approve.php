<?php

namespace App\Domain\Orders\Validator;

use App\Base\BaseValidator;
use App\Domain\Orders\Order;
use App\Domain\Orders\Interface\Validator;
use App\Storage\Orders\Query as Orders;

/**
 * 取引承認フロー：承認
 */
class Approve extends BaseValidator implements Validator
{
    /**
     * コンストラクタ
     *
     * @param Orders $orders 取引
     */
    public function __construct(
        private Orders $orders,
    ) {
    }

    /**
     * バリデーション
     *
     * @param Order $Order 取引エンティティ
     */
    public function validate(Order $order)
    {
    }
}
