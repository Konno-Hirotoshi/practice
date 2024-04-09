<?php

namespace App\Domain\Orders;

use App\Base\BaseUseCase;
use App\Storage\Orders\Command as Orders;

/**
 * 取引 - ユースケースクラス
 */
class OrderUseCase extends BaseUseCase
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
     * 新規作成
     *
     * @param array $inputData 入力データ
     * @return int
     */
    public function create(array $inputData)
    {
        $order = new Order($inputData);

        return $this->orders->save(
            order: $order,
            context: __METHOD__,
        );
    }

    /**
     * 編集
     *
     * @param int $id 取引ID
     * @param array $inputData 入力パラメータ
     * @return void
     */
    public function edit($id, array $inputData): void
    {
        $order = $this->orders
            ->getEntity($id, $inputData['updated_at'] ?? null, context: __METHOD__)
            ->edit($inputData);

        $this->orders->save($order, context: __METHOD__);
    }

    /**
     * 承認フロー：申請
     *
     * @param int $id 取引ID
     * @param ?string $updatedAt 最終更新日時
     * @return void
     */
    public function apply(int $id, $updatedAt = null)
    {
        $order = $this->orders
            ->getEntity($id, $updatedAt, context: __METHOD__)
            ->apply();

        $this->orders->save($order, context: __METHOD__);
    }

    /**
     * 承認フロー：承認
     *
     * @param int $id 取引ID
     * @param int $sequenceNo シーケンス番号
     * @param int $approvalUserId 承認者ID
     * @param ?string $updatedAt 最終更新日時
     */
    public function approve(int $id, int $sequenceNo, int $approvalUserId, ?string $updatedAt = null)
    {
        $order = $this->orders
            ->getEntity($id, $updatedAt, context: __METHOD__)
            ->approve(
                sequenceNo: $sequenceNo,
                approvalUserId: $approvalUserId,
            );

        $this->orders->save($order, context: __METHOD__);
    }
    /**
     * 承認フロー：却下
     *
     * @param int $id 取引ID
     * @param int $sequenceNo シーケンス番号
     * @param int $approvalUserId 承認者ID
     * @param ?string $updatedAt 最終更新日時
     */
    public function reject(int $id, int $sequenceNo, int $approvalUserId, ?string $updatedAt = null)
    {
        $order = $this->orders
            ->getEntity($id, $updatedAt, context: __METHOD__)
            ->reject(
                sequenceNo: $sequenceNo,
                approvalUserId: $approvalUserId,
            );

        $this->orders->save($order, context: __METHOD__);
    }

    /**
     * 承認フロー：取消
     */
    public function cancel(int $id, $updatedAt = null)
    {
        $order = $this->orders
            ->getEntity($id, $updatedAt, context: __METHOD__)
            ->cancel();

        $this->orders->save($order, context: __METHOD__);
    }

    /**
     * 削除
     *
     * @param int $id 取引ID
     * @param string $updatedAt 最終更新日時
     * @return void
     */
    public function delete(int $id, $updatedAt = null)
    {
        $order = $this->orders
            ->getEntity($id, $updatedAt, context: __METHOD__);

        $this->orders->save($order, context: __METHOD__);
    }
}
