<?php

namespace App\Service\Roles\Commands;

/**
 * 役割削除DTO
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