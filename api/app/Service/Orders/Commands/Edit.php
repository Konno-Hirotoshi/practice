<?php

namespace App\Service\Orders\Commands;

/**
 * 取引編集DTO
 */
readonly class Edit
{
    /**
     * コンストラクタ
     */
    public function __construct(
        public int $id,
        public ?string $title,
        public ?string $body,
        public ?string $updatedAt,
    ) {
    }
}
