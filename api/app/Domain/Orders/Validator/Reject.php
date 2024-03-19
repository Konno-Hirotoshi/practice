<?php

namespace App\Domain\Orders\Validator;

use App\Base\BaseValidator;
use App\Base\CustomException;
use App\Domain\Orders\Order;
use App\Domain\Orders\Interface\Validator;
use App\Storage\Orders\Query as Orders;

/**
 * 取引承認フロー：却下
 */
class Reject extends BaseValidator implements Validator
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
        if (!$this->rule->isApprovalFlowInProgress($id)) {
            throw new CustomException('not_in_progress');
        }

        // 承認フローのうち、現在承認・却下のアクション待ちの行を取得する
        $approvalFlow = $this->rule->getCurrentApprovalFlow($id);

        // シーケンス番号チェック
        if ($approvalFlow->sequence_no !== $sequenceNo) {
            throw new CustomException('wrong_sequence_no');
        }

        // 指定シーケンス番号の承認者チェック
        if ($approvalFlow->user_id !== $approvalUserId) {
            throw new CustomException('deny_user');
        }
    }
}
