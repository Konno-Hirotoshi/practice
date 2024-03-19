<?php

namespace App\Domain\Orders\Validator;

use App\Base\BaseValidator;
use App\Base\CustomException;
use App\Domain\Orders\Order;
use App\Domain\Orders\Interface\Validator;
use App\Storage\Orders\Query as Orders;

/**
 * 取引承認フロー：承認
 */
class Approve extends BaseValidator implements Validator
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
     * バリデーション
     *
     * @param Order $Order 取引エンティティ
     */
    public function validate(Order $order)
    {
        // 承認全体のステータスチェック
        if (!$this->rule->isApprovalFlowInProgress($order->id)) {
            throw new CustomException('not_in_progress');
        }

        // 承認フローのうち、現在承認・却下のアクション待ちの行を取得する
        $approvalFlow = $this->rule->getCurrentApprovalFlow($order->id);

        // シーケンス番号チェック
        if ($approvalFlow->sequence_no !== $sequenceNo) {
            throw new CustomException('wrong_sequence_no');
        }

        // 指定シーケンス番号の承認者チェック
        if ($approvalFlow->user_id !== $approvalUserId) {
            throw new CustomException('deny_user');
        }
        // newStatus: $this->rule->getNewStatus($approvalFlow->isFinal)
    }

    private function isEditable(int $id)
    {
        $status = $this->orders->getApprovalStatus($id);

        return $status == Orders::APPROVAL_STATUS_REJECT
            || $status == Orders::APPROVAL_STATUS_NONE
            || $status == Orders::APPROVAL_STATUS_CANCEL;
    }
}
