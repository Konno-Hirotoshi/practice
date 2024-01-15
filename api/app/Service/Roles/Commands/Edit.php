<?php

namespace App\Service\Roles\Commands;

/**
 * 役割編集DTO
 */
readonly class Edit
{
    /**
     * コンストラクタ
     */
    public function __construct(
        public int $id,
        public ?string $name,
        public ?string $note,
        public ?array $permissionIds,
        public ?string $updatedAt,
    ) {
    }
}
