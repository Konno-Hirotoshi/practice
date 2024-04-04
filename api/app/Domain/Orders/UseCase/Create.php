<?php

namespace App\Domain\Orders\UseCase;

use App\Base\BaseUseCase;
use App\Domain\Orders\Order;
use App\Storage\Orders\Command as Orders;

/**
 * 取引新規作成
 */
class Create extends BaseUseCase
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
     * ユースケース実行
     *
     * @param array $inputData 入力データ
     * @return int
     */
    public function invoke(array $inputData)
    {
        // 01. Create Entity
        $order = new Order($inputData);

        // 02. Validate Entity
        // (NOP)

        // 03. Store Entity
        $orderId = $this->orders->save(
            order: $order,
            context: __CLASS__,
        );

        // 04. Return ID
        return $orderId;
    }
}
