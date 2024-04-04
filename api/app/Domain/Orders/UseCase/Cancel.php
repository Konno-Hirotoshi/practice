<?php

namespace App\Domain\Orders\UseCase;

use App\Base\BaseUseCase;
use App\Storage\Orders\Command as Orders;

/**
 * 取引承認フロー：取り消し
 */
class Cancel extends BaseUseCase
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
     */
    public function invoke(int $id, $updatedAt = null)
    {
        // 01. Restore Entity
        $currentOrder = $this->orders->getEntity($id, $updatedAt, context: __CLASS__);

        // 02. Invoke Use Case
        $order = $currentOrder->cancel();

        // 03. Validate Entity
        // (NOP)

        // 04. Store Entity
        $this->orders->save($order, context: __CLASS__);
    }
}
