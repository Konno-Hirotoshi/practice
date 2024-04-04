<?php

namespace App\Domain\Orders\UseCase;

use App\Base\BaseUseCase;
use App\Storage\Orders\Command as Orders;

/**
 * 取引削除
 */
class Delete extends BaseUseCase
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
     * 削除
     *
     * @param int $id 取引ID
     * @param string $updatedAt 最終更新日時
     * @return void
     */
    public function invoke(int $id, $updatedAt = null)
    {
        // 01. Restore Entity
        $order = $this->orders->getEntity($id, $updatedAt, context: __CLASS__);

        // 02. Invoke Use Case
        // (NOP)

        // 03. Validate Entity
        // (NOP)

        // 04. Store Entity
        $this->orders->save($order, context: __CLASS__);
    }
}
