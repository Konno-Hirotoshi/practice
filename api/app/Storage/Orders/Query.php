<?php

namespace App\Storage\Orders;

use App\Base\CustomException;
use App\Base\SearchOption;
use App\Domain\Orders\ApprovalFlow;
use App\Domain\Orders\Order;
use App\Domain\Orders\OrderCollection;
use App\Domain\Orders\UseCase\Edit;
use App\Domain\Orders\UseCase\Apply;
use App\Domain\Orders\UseCase\Approve;
use App\Domain\Orders\UseCase\Reject;
use App\Domain\Orders\UseCase\Cancel;
use App\Domain\Orders\UseCase\Delete;
use Illuminate\Support\Facades\DB;

/**
 * 取引 - 問い合わせクラス
 */
class Query
{
    /**
     * 検索する
     *
     * @param SearchOption $option
     * @return array
     */
    public function search(SearchOption $option): array
    {
        $results =  DB::table('orders')
            ->select([
                'id',
                'title',
                'body',
                'approval_status',
            ])
            ->exSearch($option);

        return $results;
    }

    /**
     * 1件取得する
     * 
     * @param int $id
     * @return object
     */
    public function get(int $id): object
    {
        $order = DB::table('orders')
            ->where('id', $id)
            ->first([
                'title',
                'body',
                'approval_status',
                'updated_at',
            ]);

        if ($order === null) {
            throw new CustomException('record_not_found');
        }

        $order->approval_flows = DB::table('order_approval_flows')
            ->where('order_id', $id)
            ->orderBy('sequence_no')
            ->get([
                'sequence_no',
                'approval_user_id',
                'approval_status',
                'approval_date',
            ]);

        return $order;
    }

    /**
     * 承認フローを取得する
     * 
     * @param int $id
     * @return \Illuminate\Support\Collection
     */
    public function getApprovalFlow(int $id): \Illuminate\Support\Collection
    {
        $approvalFlowList = DB::table('order_approval_flows')
            ->where('order_id', $id)
            ->orderBy('sequence_no')
            ->get([
                'sequence_no',
                'approval_user_id',
                'approval_date',
                'approval_status',
            ]);
        return $approvalFlowList;
    }

    /**
     * エンティティを取得する
     *
     * @param int $id 取引ID
     * @return Order
     */
    public function getEntity(int $id, ?string $updatedAt = null, ?string $context = null): Order
    {
        // contextに応じたカラムのみ取得する
        $dto = DB::table('orders')
            ->where('id', $id)
            ->first(match ($context) {
                OrderCollection::class => [
                    'id',
                    'title',
                    'body',
                    'approval_status',
                    'updated_at'
                ],
                'edit' => ['id', 'approval_status', 'updated_at'],
                'apply' => ['id', 'approval_status', 'updated_at'],
                'approve' => ['id', 'approval_status', 'updated_at'],
                'reject' => ['id', 'approval_status', 'updated_at'],
                'cancel' => ['id', 'updated_at'],
                'delete' => ['id', 'updated_at'],
            });

        // レコードが存在しなければエラーとする
        if ($dto === null) {
            throw new CustomException('record_not_found');
        }

        // 最終更新日時に差異があればエラーとする
        if ($updatedAt !== null && $updatedAt !== $dto->updated_at) {
            throw new CustomException('conflict');
        }

        // 承認フローを必要とするcontextなら承認フローを取得する
        $approvalFlowEntitites = null;
        if (in_array($context, [OrderCollection::class, 'apply', 'approve', 'reject'])) {
            $approvalFlowEntitites = $this->getApprovalFlowEntities($id);
        }

        return new Order($this->convert($dto, $approvalFlowEntitites));
    }

    /**
     * 承認フローエンティティを取得する
     *
     * @param int $id 取引ID
     * @return array<ApprovalFlow>
     */
    private function getApprovalFlowEntities(int $id): array
    {
        $approvalFlowEntitites = $this->getApprovalFlow($id)->map(function ($dto) {
            return new ApprovalFlow([
                'sequenceNo' => $dto->sequence_no,
                'approvalUserId' => $dto->approval_user_id,
                'approvalDate' => $dto->approval_date,
                'approvalStatus' => $dto->approval_status,
            ]);
        });

        return $approvalFlowEntitites->toArray();
    }

    /**
     * 取得データをエンティティのコンストラクタの入力形式に変換する
     *
     * @param object $dto　取得データDTO
     * @param ?array $approvalFlowEntitites 承認フロー
     * @return array
     */
    private function convert(object $dto, ?array $approvalFlowEntitites = null)
    {
        $mapping = [
            'id' => 'id',
            'title' => 'title',
            'body' => 'body',
            'approval_status' => 'approvalStatus',
            'updated_at' => 'updatedAt',
        ];

        $inputData = [];
        foreach ((array)$dto as $key => $value) {
            $inputData[$mapping[$key]] = $value;
        }

        if ($approvalFlowEntitites) {
            $inputData['approvalFlows'] = $approvalFlowEntitites;
        }

        return $inputData;
    }
}
