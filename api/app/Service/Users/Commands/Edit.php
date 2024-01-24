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
        public ?string $fullName,
        public ?string $email,
        public ?int $departmentId,
        public ?int $roleId,
        public ?string $password,
        public ?string $note,
        public ?string $updatedAt,
    ) {
    }
}
