<?php

namespace App\Service\Users\Commands;

/**
 * 利用者削除DTO
 */
readonly class Delete
{
    /**
     * コンストラクタ
     */
    public function __construct(
        public int|array $deleteIds,
    ) {
    }
}