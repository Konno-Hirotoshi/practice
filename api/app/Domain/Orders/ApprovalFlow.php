<?php

namespace App\Domain\Orders;

use App\Base\CustomException;

/**
 * 取引承認フロー
 */
readonly class ApprovalFlow
{
    /** 承認ステータス：未承認 */
    const APPROVAL_STATUS_NONE = 0;

    /** 承認ステータス：承認済み */
    const APPROVAL_STATUS_APPROVE = 1;

    /** 承認ステータス：却下 */
    const APPROVAL_STATUS_REJECT = 2;

    /** 承認ステータス：取り消し */
    const APPROVAL_STATUS_CANCEL = 9;

    /** 承認ステータス */
    const APPROVAL_STATUS = [
        self::APPROVAL_STATUS_NONE,
        self::APPROVAL_STATUS_APPROVE,
        self::APPROVAL_STATUS_REJECT,
        self::APPROVAL_STATUS_CANCEL,
    ];

    /** @var string 取引ID */
    public int $orderId;

    /** @var int シーケンス番号 */
    public int $sequenceNo;

    /** @var string 承認者 */
    public int $approvalUserId;

    /** @var string 承認ステータス */
    public int $approvalStatus;

    /** @var string 承認日時 */
    public ?string $approvalDate;

    /**
     * コンストラクタ
     *
     * @param array $inputData 入力パラメータ
     */
    public function __construct(array $inputData)
    {
        foreach ($inputData as $key => $value) {
            $this->{$key} = $value;
        }

        if ($validationErrors = $this->validate()) {
            throw new CustomException($validationErrors);
        }
    }

    /**
     * エンティティの妥当性を検証する
     */
    private function validate(): array
    {
        $validationErrors = [];

        // シーケンス番号が自然数であること
        if ($this->sequenceNo <= 0) {
            $validationErrors['sequenceNo'] = 'format';
        }

        // 承認ステータスがリストに含まれていること
        if (!in_array($this->approvalStatus, self::APPROVAL_STATUS)) {
            $validationErrors['sequenceNo'] = 'enum';
        }

        return $validationErrors;
    }
}
