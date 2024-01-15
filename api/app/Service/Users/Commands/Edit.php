<?php

namespace App\Service\Users\Commands;

/**
 * 利用者編集DTO
 */
readonly class Edit
{
    /**
     * コンストラクタ
     */
    public function __construct(
        public int $id,
        public ?string $password,
        public ?int $roleId,
        public ?string $note,
        public ?string $updatedAt,
    ) {
    }
}
