<?php

namespace App\Controller;

use App\Domain\Orders\OrderCollection;
use App\Domain\Orders\UseCase\Apply;
use App\Domain\Orders\UseCase\Approve;
use App\Domain\Orders\UseCase\Cancel;
use App\Domain\Orders\UseCase\Create;
use App\Domain\Orders\UseCase\Delete;
use App\Domain\Orders\UseCase\Edit;
use App\Domain\Orders\UseCase\Reject;
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
        $results = $this->orderCollection->search(...$inputData);

        // 03. Return Response
        return $results;
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
        return (array)$order;
    }

    /**
     * 新規作成
     */
    public function create(Create $useCase)
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            'title' => ['required', 'string'],
            'body' => ['required', 'string'],
        ]);

        // 02. Invoke Use Case
        $orderId = $useCase->invoke($inputData);

        // 03. Return Response
        return ['id' => $orderId];
    }

    /**
     * 編集
     */
    public function edit(int $id, Edit $useCase)
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            'title' => ['filled', 'string'],
            'body' => ['filled', 'string'],
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]);

        // 02. Invoke Use Case
        $useCase->invoke($id, $inputData);

        // 03. Return Response
        return ['succeed' => true];
    }

    /**
     * 承認フロー: 開始
     */
    public function apply(int $id, Apply $useCase)
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]);

        // 02. Invoke Use Case
        $useCase->invoke($id, ...$inputData);

        // 03. Return Response
        return ['succeed' => true];
    }

    /**
     * 承認フロー: 承認
     */
    public function approve(int $id, Approve $useCase)
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            'sequenceNo' => ['required', 'integer'],
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]) + [
            'approvalUserId' => $this->request->user()->id,
        ];

        // 02. Invoke Use Case
        $useCase->invoke($id, ...$inputData);

        // 03. Return Response
        return ['succeed' => true];
    }

    /**
     * 承認フロー: 却下
     */
    public function reject(int $id, Reject $useCase)
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            'sequenceNo' => ['required', 'integer'],
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]) + [
            'approvalUserId' => $this->request->user()->id,
        ];

        // 02. Invoke Use Case
        $useCase->invoke($id, ...$inputData);

        // 03. Return Response
        return ['succeed' => true];
    }

    /**
     * 承認フロー: 取消
     */
    public function cancel(int $id, Cancel $useCase)
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]);

        // 02. Invoke Use Case
        $useCase->invoke($id, ...$inputData);

        // 03. Return Response
        return ['succeed' => true];
    }

    /**
     * 削除
     */
    public function delete(int $id, Delete $useCase)
    {
        // 01. Validate Request
        // (NOP)

        // 02. Invoke Use Case
        $useCase->invoke($id);

        // 03. Return Response
        return ['succeed' => true];
    }
}
