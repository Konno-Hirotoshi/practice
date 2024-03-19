<?php

namespace App\Domain\Orders\Validator;

use App\Base\BaseValidator;
use App\Base\CustomException;
use App\Domain\Orders\Order;
use App\Domain\Orders\Interface\Validator;
use App\Storage\Orders\Query as Orders;

/**
 * 取引承認フロー：取り消し
 */
class Cancel extends BaseValidator implements Validator
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
        // 承認全体のステータスチェック
        if (!$this->rule->isApprovalFlowInProgress($id)) {
            throw new CustomException('not_in_progress');
        }
    }
}
