<?php

namespace App\Domain\Orders\Validator;

use App\Base\BaseValidator;
use App\Base\CustomException;
use App\Domain\Orders\Order;
use App\Domain\Orders\Interface\Validator;
use App\Storage\Orders\Query as Orders;

/**
 * 取引承認申請
 */
class Apply extends BaseValidator implements Validator
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
        // 編集可能であること (承認フローが進行中や承認済みでは無い)
        if (!$this->isEditable($order->id)) {
            throw new CustomException('not_editable');
        }
    }

    /**
     * 編集可能かどうか
     */
    private function isEditable(int $id)
    {
        $status = $this->orders->getApprovalStatus($id);

        $currentOrder = new Order(['status' => $status]);

        return $currentOrder->isEditable();
    }
}
