<?php

namespace App\Service\Users\Commands;

/**
 * 利用者削除
 */
readonly class DeleteCommand
{
    /** @var array 利用者ID */
    public array $deleteIds;

    /**
     * コンストラクタ
     */
    public function __construct(int|array $deleteIds)
    {
        $this->deleteIds = is_array($deleteIds) ? $deleteIds : [$deleteIds];
    }
}
