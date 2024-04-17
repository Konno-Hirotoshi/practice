<?php

namespace App\Domain\Users\Dto;

/**
 * 利用者 - 編集時DTO
 */
readonly class EditDto
{
    /**
     * @param string $fullName フルネーム
     * @param string $email メールアドレス
     * @param int $departmentId 部署ID
     * @param int $roleId 役割ID
     * @param string $password パスワード
     * @param string $note 備考
     * @param string 最終更新日時
     */
    public function __construct(
        public ?string $fullName = null,
        public ?string $email = null,
        public ?int $departmentId = null,
        public ?int $roleId = null,
        public ?string $password = null,
        public ?string $note =  null,
        public ?string $updatedAt = null,
    ) {
    }

    public function getData()
    {
        return array_filter((array)$this, fn($value) => isset($value));
    }
}
