<?php

namespace App\Domain\Roles\Dto;

/**
 * 役割 - 新規作成時DTO
 */
readonly class CreateDto
{
    /**
     * @param string $name 名称
     * @param string $note 備考
     * @param array $permissionIds 権限IDのリスト
     */
    public function __construct(
        public string $name,
        public string $note = '',
        public array $permissionIds = [],
    ) {
    }

    public function getData()
    {
        return (array)$this;
    }
}
