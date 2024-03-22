<?php

namespace App\Controller;

use App\Domain\Orders\Order;
use App\Domain\Orders\OrderCollection;
use App\Domain\Orders\Validator\Apply;
use App\Domain\Orders\Validator\Approve;
use App\Domain\Orders\Validator\Cancel;
use App\Domain\Orders\Validator\Create;
use App\Domain\Orders\Validator\Delete;
use App\Domain\Orders\Validator\Edit;
use App\Domain\Orders\Validator\Reject;
use App\Storage\Orders\Command as Orders;
use Illuminate\Http\Request;

/**
 * 取引 - コントローラークラス
 */
class OrdersController
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Request $request,
        private Orders $orders,
        private OrderCollection $orderCollection,
    ) {
    }

    /**
     * 検索
     */
    public function search()
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            'search' => ['array'],
            'sort' => ['array'],
            'sort.*' => ['string'],
            'page' => ['integer'],
            'perPage' => ['integer', 'max:100'],
        ]);

        // 02. Invoke Use Case
        $orders = $this->orderCollection->search(...$inputData);

        // 03. Return Response
        return $orders;
    }

    /**
     * 詳細情報取得
     */
    public function show(int $id)
    {
        // 01. Validate Request
        // (NOP)

        // 02. Invoke Use Case
        $order = $this->orderCollection->get($id);

        // 03. Return Response
        return $order;
    }

    /**
     * 新規作成
     */
    public function create(Create $create)
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            'title' => ['required', 'string'],
            'body' => ['required', 'string'],
        ]);

        // 02. Invoke Use Case
        $order = new Order($inputData);
        $orderId = $order->save(
            validator: $create,
            storage: $this->orders,
        );

        // 03. Return Response
        return ['id' => $orderId];
    }

    /**
     * 編集
     */
    public function edit(int $id, Edit $edit)
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            'title' => ['filled', 'string'],
            'body' => ['filled', 'string'],
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]) + [
            'id' => $id,
        ];

        // 02. Invoke Use Case
        $order = new Order($inputData);
        $order->save(
            validator: $edit,
            storage: $this->orders,
        );

        // 03. Return Response
        return ['succeed' => true];
    }

    /**
     * 承認フロー: 開始
     */
    public function apply(int $id, Apply $apply)
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]) + [
            'id' => $id,
            'approvalStatus' => Order::APPROVAL_STATUS_APPLY,
            'approvalFlows' => $this->orderCollection->makeApprovalFlow($id),
        ];

        // 02. Invoke Use Case
        $order = new Order($inputData);
        $order->save(
            validator: $apply,
            storage: $this->orders,
        );

        // 03. Return Response
        return ['succeed' => true];
    }

    /**
     * 承認フロー: 承認
     */
    public function approve(int $id, Approve $approve)
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            'sequenceNo' => ['required', 'integer'],
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]);

        // 02. Invoke Use Case
        $currentOrder = $this->orderCollection->getEntity(
            id: $id,
            withApprovalFlows: true
        );
        $order = $currentOrder->approve(
            sequenceNo: $inputData['sequenceNo'],
            approvalUserId: $this->request->user()->id,
        );
        $order->save(
            validator: $approve,
            storage: $this->orders,
        );

        // 03. Return Response
        return ['succeed' => true];
    }

    /**
     * 承認フロー: 却下
     */
    public function reject(int $id, Reject $reject)
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            'sequenceNo' => ['required', 'integer'],
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]);

        // 02. Invoke Use Case
        $currentOrder = $this->orderCollection->getEntity(
            id: $id,
            withApprovalFlows: true
        );
        $order = $currentOrder->reject(
            sequenceNo: $inputData['sequenceNo'],
            approvalUserId: $this->request->user()->id,
        );
        $order->save(
            validator: $reject,
            storage: $this->orders,
        );

        // 03. Return Response
        return ['succeed' => true];
    }

    /**
     * 承認フロー: 取消
     */
    public function cancel(int $id, Cancel $cancel)
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]) + [
            'id' => $id,
            'approvalUserId' => $this->request->user()->id,
        ];

        // 02. Invoke Use Case
        $order = new Order($inputData);
        $order->save(
            validator: $cancel,
            storage: $this->orders,
        );

        // 03. Return Response
        return ['succeed' => true];
    }

    /**
     * 削除
     */
    public function delete(int $id, Delete $delete)
    {
        // 01. Validate Request
        $inputData = ['id' => $id];

        // 02. Invoke Use Case
        $order = new Order($inputData);
        $order->save(
            validator: $delete,
            storage: $this->orders,
        );

        // 03. Return Response
        return ['succeed' => true];
    }
}
