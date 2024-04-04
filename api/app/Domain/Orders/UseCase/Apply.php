<?php

namespace App\Domain\Orders\UseCase;

use App\Base\BaseUseCase;
use App\Storage\Orders\Command as Orders;

/**
 * 取引承認フロー: 申請
 */
class Apply extends BaseUseCase
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
     * @param ?string $updatedAt 最終更新日時
     * @return void
     */
    public function invoke(int $id, $updatedAt = null)
    {
        // 01. Restore Entity
        $currentOrder = $this->orders->getEntity($id, $updatedAt, context: __CLASS__);

        // 02. Invoke Use Case
        $order = $currentOrder->apply();

        // 03. Validate Entity
        // (NOP)

        // 04. Store Entity
        $this->orders->save($order, context: __CLASS__);
    }
}
