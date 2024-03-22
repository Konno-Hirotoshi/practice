<?php

namespace App\Domain\Orders;

use App\Base\CustomException;
use App\Domain\Orders\Interface\Storage;
use App\Domain\Orders\Interface\Validator;

/**
 * 取引
 */
readonly class Order
{
    /** 承認ステータス：未承認 */
    const APPROVAL_STATUS_NONE = 0;

    /** 承認ステータス：承認済み */
    const APPROVAL_STATUS_APPROVE = 1;

    /** 承認ステータス：却下 */
    const APPROVAL_STATUS_REJECT = 2;

    /** 承認ステータス：申請中  */
    const APPROVAL_STATUS_APPLY = 4;

    /** 承認ステータス：申請中 (一次承認済み) */
    const APPROVAL_STATUS_IN_PROGRESS = 5;

    /** 承認ステータス：取り消し */
    const APPROVAL_STATUS_CANCEL = 9;

    /** 承認ステータス */
    const APPROVAL_STATUS = [
        self::APPROVAL_STATUS_NONE,
        self::APPROVAL_STATUS_APPROVE,
        self::APPROVAL_STATUS_REJECT,
        self::APPROVAL_STATUS_APPLY,
        self::APPROVAL_STATUS_IN_PROGRESS,
        self::APPROVAL_STATUS_CANCEL,
    ];

    /** @var int ID */
    public ?int $id;

    /** @var string 取引タイトル */
    public string $title;

    /** @var string 取引内容 */
    public string $body;

    /** @var int 承認ステータス */
    public int $approvalStatus;

    /** @var array<ApprovalFlow> 承認フロー */
    public array $approvalFlows;

    /** @var string 最終更新日時 */
    public string $updatedAt;

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

        if (isset($this->approvalStatus)) {
            // 承認ステータスがリストに含まれていること
            if (!in_array($this->approvalStatus, self::APPROVAL_STATUS)) {
                $validationErrors['sequenceNo'] = 'enum';
            }
        }

        if (isset($this->approvalFlows)) {
            $prevSequenceNo = 0;
            foreach ($this->approvalFlows as $approvalFlow) {
                // 型が正しいこと
                if (!$approvalFlow instanceof ApprovalFlow) {
                    $validationErrors['approvalFlow'] = 'format';
                    break;
                }
                // シーケンス番号が昇順であること
                if ($approvalFlow->sequenceNo <= $prevSequenceNo) {
                    $validationErrors['approvalFlow'] = 'sequenceNo:order';
                    break;
                }
                $prevSequenceNo = $approvalFlow->sequenceNo;
            }
        }

        return $validationErrors;
    }

    /**
     * 承認フロー：申請
     */
    public function apply()
    {
    }

    /**
     * 承認フロー：承認
     */
    public function approve(int $sequenceNo, int $approvalUserId)
    {
        // 承認全体のステータスチェック
        if (!$this->isApprovalFlowInProgress()) {
            throw new CustomException('not_in_progress');
        }

        // シーケンス番号チェック
        if (!$this->isCurrentSequenceNo($sequenceNo)) {
            throw new CustomException('wrong_sequence_no');
        }

        // 指定シーケンス番号の承認者チェック
        if (!$this->isCurrentApprovalUserId($approvalUserId)) {
            throw new CustomException('deny_user');
        }

        return new Order([
            'id' => $this->id,
            'approvalStatus' => $this->getNextApprovalStatus($sequenceNo),
            'approvalFlows' => [
                new ApprovalFlow([
                    'orderId' => $this->id,
                    'sequenceNo' => $sequenceNo,
                    'approvalUserId' => $approvalUserId,
                    'approvalStatus' => ApprovalFlow::APPROVAL_STATUS_APPROVE,
                    'approvalDate' => date('Y-m-d H:i:s'),
                ]),
            ],
        ]);
    }

    /**
     * 承認フロー：却下
     */
    public function reject(int $sequenceNo, int $approvalUserId)
    {
        // 承認全体のステータスチェック
        if (!$this->isApprovalFlowInProgress()) {
            throw new CustomException('not_in_progress');
        }

        // シーケンス番号チェック
        if (!$this->isCurrentSequenceNo($sequenceNo)) {
            throw new CustomException('wrong_sequence_no');
        }

        // 指定シーケンス番号の承認者チェック
        if (!$this->isCurrentApprovalUserId($approvalUserId)) {
            throw new CustomException('deny_user');
        }

        return new Order([
            'id' => $this->id,
            'approvalStatus' => self::APPROVAL_STATUS_REJECT,
            'approvalFlows' => [
                new ApprovalFlow([
                    'orderId' => $this->id,
                    'sequenceNo' => $sequenceNo,
                    'approvalUserId' => $approvalUserId,
                    'approvalStatus' => ApprovalFlow::APPROVAL_STATUS_REJECT,
                    'approvalDate' => date('Y-m-d H:i:s'),
                ]),
            ],
        ]);
    }

    /**
     * 承認フローの指定行が承認された場合の承認ステータス
     */
    private function getNextApprovalStatus(int $sequenceNo)
    {
        return $this->isFinalSequenceNo($sequenceNo)
            ? self::APPROVAL_STATUS_APPROVE
            : self::APPROVAL_STATUS_IN_PROGRESS;
    }

    /**
     * 編集可能な状態か
     */
    public function isEditable()
    {
        return in_array($this->approvalStatus, [
            self::APPROVAL_STATUS_REJECT,
            self::APPROVAL_STATUS_NONE,
            self::APPROVAL_STATUS_CANCEL,
        ]);
    }

    /**
     * 承認フローが進行中か
     */
    public function isApprovalFlowInProgress()
    {
        return in_array($this->approvalStatus, [
            self::APPROVAL_STATUS_APPLY,
            self::APPROVAL_STATUS_IN_PROGRESS,
        ]);
    }

    /**
     * 承認フローの一番最後のシーケンス番号か
     */
    public function isFinalSequenceNo($sequenceNo)
    {
        return count($this->approvalFlows) === $sequenceNo;
    }

    /**
     * 現在承認・却下を待っている行のシーケンス番号か
     */
    public function isCurrentSequenceNo($sequenceNo)
    {
        $current = $this->getCurrentApprovalFlow();
        if ($current === null) {
            return false;
        }
        return $current->sequenceNo === $sequenceNo;;
    }

    /**
     * 現在承認・却下を待っている行の承認者か
     */
    public function isCurrentApprovalUserId($approvalUserId)
    {
        $current = $this->getCurrentApprovalFlow();
        if ($current === null) {
            return false;
        }
        return $current->approvalUserId === $approvalUserId;;
    }

    /**
     * 承認フローの中で承認・却下をしていない行を取得する
     */
    private function getCurrentApprovalFlow()
    {
        foreach ($this->approvalFlows as $approvalFLow) {
            if ($approvalFLow->approvalStatus === ApprovalFlow::APPROVAL_STATUS_NONE) {
                return $approvalFLow;
            }
        }
        return null;
    }

    /**
     * エンティティを検証して保存する
     * 
     * @param Validator $validator バリデータークラス
     * @param Storage $storage コマンドクラス
     * @return mixed
     */
    public function save(Validator $validator, Storage $storage): mixed
    {
        $validator->validate($this);
        return $storage->save($this, $validator::class);
    }
}
