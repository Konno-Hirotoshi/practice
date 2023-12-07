<?php

namespace App\Service\Orders\Commands;

/**
 * 取引作承認フロー取消DTO
 */
readonly class Cancel
{
    /**
     * コンストラクタ
     */
    public function __construct(
        public int $id,
        public ?string $updatedAt,
    ) {
    }
}
