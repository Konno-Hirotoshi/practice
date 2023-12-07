<?php

namespace App\Model\Orders;

use App\Base\CustomException;
use App\Base\SearchOption;
use Illuminate\Support\Facades\DB;

/**
 * 取引 - 問い合わせクラス
 */
class Query
{
    // 承認ステータス：未承認
    const APPROVAL_STATUS_NONE = 0;
    // 承認ステータス：承認済み
    const APPROVAL_STATUS_APPROVE = 1;
    // 承認ステータス：却下
    const APPROVAL_STATUS_REJECT = 2;
    // 承認ステータス：申請中 
    const APPROVAL_STATUS_APPLY = 4;
    // 承認ステータス：申請中 (一次承認済み)
    const APPROVAL_STATUS_IN_PROGRESS = 5;
    // 承認ステータス：取り消し
    const APPROVAL_STATUS_CANCEL = 9;

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

        return $order;
    }

    /**
     * 承認フローを取得する
     * 
     * @param int $id
     * @return object
     */
    public function getApprovalFlows(int $id): object
    {
        $approvalFlows = DB::table('order_approval_flows')
            ->where('order_id', $id)
            ->orderBy('sequence_no')
            ->get([
                'sequence_no',
                'user_id',
                'approval_date',
                'approval_status',
            ]);
        return $approvalFlows;
    }

    /**
     * 承認ステータスを取得する
     * 
     * @param int $id
     * @return int
     */
    public function getApprovalStatus(int $id): int
    {
        $order = DB::table('orders')
            ->where('id', $id)
            ->first([
                'approval_status',
            ]);

        if ($order === null) {
            throw new CustomException('record_not_found');
        }

        return $order->approval_status;
    }
}
