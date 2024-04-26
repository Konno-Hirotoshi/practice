<?php

namespace App\Domain\Roles\Dto;

/**
 * 役割 - 編集時DTO
 */
readonly class EditDto
{
    /**
     * @param ?string $name 名称
     * @param ?string $note 備考
     * @param ?array $permissionIds 権限IDのリスト
     * @param ?string 最終更新日時
     */
    public function __construct(
        public ?string $name = null,
        public ?string $note = null,
        public ?array $permissionIds = null,
        public ?string $updatedAt = null,
    ) {
    }

    public function getData()
    {
        return array_filter((array)$this, fn($value) => isset($value));
    }
}
