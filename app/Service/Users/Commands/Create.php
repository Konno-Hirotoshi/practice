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
        public string $email,
        public string $password,
        public int $roleId,
        public string $note,
    ) {
    }
}
