<?php

namespace App\Domain\Orders\Enum;

/**
 * 取引 - 承認ステータス
 */
class ApprovalStatus
{
    /** 承認ステータス：未承認 */
    public const NONE = 0;

    /** 承認ステータス：承認済み */
    public const APPROVE = 1;

    /** 承認ステータス：却下 */
    public const REJECT = 2;

    /** 承認ステータス：申請中  */
    public const APPLY = 4;

    /** 承認ステータス：申請中 (一次承認済み) */
    public const IN_PROGRESS = 5;

    /** 承認ステータス：取り消し */
    public const CANCEL = 9;

    /**
     * 全体のリスト
     *
     * @return array
     */
    public static function all()
    {
        return [
            self::NONE,
            self::APPROVE,
            self::REJECT,
            self::APPLY,
            self::IN_PROGRESS,
            self::CANCEL,
        ];
    }
}
