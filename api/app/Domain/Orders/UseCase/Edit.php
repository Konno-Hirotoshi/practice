<?php

namespace App\Domain\Orders\UseCase;

use App\Base\BaseUseCase;
use App\Domain\Orders\Order;
use App\Storage\Orders\Command as Orders;

/**
 * 取引データ編集
 */
class Edit extends BaseUseCase
{
    /**
     * コンストラクタ
     *
     * @param Orders $orders 取引ストレージクラス
     */
    public function __construct(private Orders $orders)
    {
    }

    /**
     * ユースケース実行
     *
     * @param int $id 取引ID
     * @param array $inputData 入力パラメータ
     * @return void
     */
    public function invoke($id, array $inputData): void
    {
        // 01. Restore Entity
        $updatedAt = $inputData['updated_at'] ?? null;
        $currentOrder = $this->orders->getEntity($id, $updatedAt, context: __CLASS__);
        
        // 02. Invoke Use Case
        $order = $currentOrder->edit($inputData);

        // 03. Validate Entity
        // (NOP)

        // 04. Store Entity
        $this->orders->save($order, context: __CLASS__);
    }
}
