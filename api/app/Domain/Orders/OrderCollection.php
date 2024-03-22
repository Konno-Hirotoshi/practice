<?php

namespace App\Domain\Orders;

use App\Base\SearchOption;
use App\Storage\Orders\Query as Orders;

/**
 * 取引コレクション
 */
class OrderCollection
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Orders $orders,
    ) {
    }

    /**
     * 全体を検索する
     */
    public function search(array $search = [], array $sort = [], int $page = 1, int $perPage = 3): array
    {
        $option = SearchOption::create($search, $sort, $page, $perPage, [
            'id' => 'value',
        ]);

        return $this->orders->search($option);
    }

    /**
     * 詳細情報を取得する
     */
    public function get(int $id)
    {
        return $this->orders->get($id);
    }

    /**
     * エンティティを取得する
     */
    public function getEntity(int $id, bool $withBody = false, bool $withApprovalStatus = false, bool $withApprovalFlows = false)
    {
        $inputData = ['id' => $id];
        if ($withBody) {
            $inputData += (array)$this->orders->get($id);
        }
        if ($withApprovalFlows) {
            $inputData['approvalStatus'] = $this->orders->getApprovalStatus($id);
            $inputData['approvalFlows'] =  $this->orders->getApprovalFlows($id)->map(function ($row) use ($id) {
                return new ApprovalFlow([
                    'orderId' => $id,
                    'sequenceNo' => $row->sequence_no,
                    'approvalUserId' => $row->approval_user_id,
                    'approvalDate' => $row->approval_date,
                    'approvalStatus' => $row->approval_status,
                ]);
            })->toArray();
        }
        return new Order($inputData);
    }

    /**
     * 承認フローを作成する
     */
    public function makeApprovalFlow($id)
    {
        return [
            new ApprovalFlow(['orderId' => $id, 'sequenceNo' => 1, 'approvalUserId' => 1, 'approvalStatus' => ApprovalFlow::APPROVAL_STATUS_NONE]),
            new ApprovalFlow(['orderId' => $id, 'sequenceNo' => 2, 'approvalUserId' => 1, 'approvalStatus' => ApprovalFlow::APPROVAL_STATUS_NONE]),
            new ApprovalFlow(['orderId' => $id, 'sequenceNo' => 3, 'approvalUserId' => 1, 'approvalStatus' => ApprovalFlow::APPROVAL_STATUS_NONE]),
        ];
    }
}
