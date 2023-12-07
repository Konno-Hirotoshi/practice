<?php

namespace App\Service\Orders\Commands;

/**
 * 取引削除DTO
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