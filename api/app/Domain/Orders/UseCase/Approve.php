<?php

namespace App\Domain\Orders\UseCase;

use App\Base\BaseUseCase;
use App\Storage\Orders\Command as Orders;

/**
 * 取引承認フロー：承認
 */
class Approve extends BaseUseCase
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
     * ユースケース実行
     *
     * @param int $id 取引ID
     * @param int $sequenceNo シーケンス番号
     * @param int $approvalUserId 承認者ID
     * @param ?string $updatedAt 最終更新日時
     */
    public function invoke(int $id, int $sequenceNo, int $approvalUserId, ?string $updatedAt = null)
    {
        // 01. Restore Entity
        $currentOrder = $this->orders->getEntity($id, $updatedAt, context: __CLASS__);

        // 02. Invoke Use Case
        $order = $currentOrder->approve(
            sequenceNo: $sequenceNo,
            approvalUserId: $approvalUserId,
        );

        // 03. Validate Entity
        // (NOP)

        // 04. Store Entity
        $this->orders->save($order, context: __CLASS__);
    }
}
