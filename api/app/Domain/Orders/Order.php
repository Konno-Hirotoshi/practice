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

    /** @var int ID */
    public ?int $id;

    /** @var string 取引タイトル */
    public string $title;

    /** @var string 取引内容 */
    public string $body;

    /** @var int 承認ステータス */
    public int $approvalStatus;

    /** @var array 承認フロー */
    public array $approvalFlows;

    /** @var string 最終更新日時 */
    public string $updatedAt;

    /**
     * コンストラクタ
     *
     * @param array $inputData 入力パラメータq
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

        return $validationErrors;
    }


    public function isEditable()
    {
        return in_array($this->approvalStatus, [
            self::APPROVAL_STATUS_REJECT,
            self::APPROVAL_STATUS_NONE,
            self::APPROVAL_STATUS_CANCEL,
        ]);
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
