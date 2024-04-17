<?php

namespace App\Domain\Users\Dto;

/**
 * 利用者 - 新規作成時DTO
 */
readonly class CreateDto
{
    /**
     * @param string $fullName フルネーム
     * @param string $email メールアドレス
     * @param int $departmentId 部署ID
     * @param int $roleId 役割ID
     * @param string $password パスワード
     * @param string $note 備考
     */
    public function __construct(
        public string $fullName,
        public string $email,
        public int $departmentId = 1,
        public int $roleId = 1,
        public string $password = 'Default@1234',
        public string $note = '',
    ) {
    }

    public function getData()
    {
        return (array)$this;
    }
}
