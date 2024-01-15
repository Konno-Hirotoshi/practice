<?php

namespace App\Service\Roles\Commands;

/**
 * 役割作成DTO
 */
readonly class Create
{
    /**
     * コンストラクタ
     */
    public function __construct(
        public string $name,
        public string $note,
        public array $permissionIds,
    ) {
    }
}
