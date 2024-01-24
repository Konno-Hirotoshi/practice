<?php

namespace App\Service\Users\Commands;

/**
 * 利用者作成DTO
 */
readonly class Create
{
    /**
     * コンストラクタ
     */
    public function __construct(
        public string $fullName,
        public string $email,
        public int $departmentId,
        public int $roleId,
        public string $password,
        public string $note,
    ) {
    }
}
