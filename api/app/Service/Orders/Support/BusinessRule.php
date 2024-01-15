<?php

namespace App\Service\Orders\Support;

use App\Base\BaseValidator;
use App\Model\Orders\Query as Orders;

/**
 * 取引 - ビジネスルール
 */
class BusinessRule extends BaseValidator
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Orders $orders,
    ) {
    }

    /**
     * 承認フローの現在のシーケンス番号を取得する
     */
    public function getCurrentApprovalFlow(int $id): ?object
    {
        $approvalFlows = $this->orders->getApprovalFlows($id);
        foreach ($approvalFlows as $index => $row) {
            if ($row->approval_status != Orders::APPROVAL_STATUS_NONE) {
                continue;
            }
            $row->isFinal = ($index +1 === count($approvalFlows));
            return $row;
        }
        return null;
    }

    /**
     * 最終承認かどうか
     */
    public function getNewStatus($isFinalApproval)
    {
        return $isFinalApproval ? Orders::APPROVAL_STATUS_APPROVE : Orders::APPROVAL_STATUS_IN_PROGRESS;
    }

    public function isEditable(int $id)
    {
        $status = $this->orders->getApprovalStatus($id);

        return $status == Orders::APPROVAL_STATUS_REJECT
            || $status == Orders::APPROVAL_STATUS_NONE
            || $status == Orders::APPROVAL_STATUS_CANCEL;
    }

    public function isApprovalFlowInProgress(int $id)
    {
        $status = $this->orders->getApprovalStatus($id);
        return $status == Orders::APPROVAL_STATUS_APPLY
            || $status == Orders::APPROVAL_STATUS_IN_PROGRESS;
    }
}
