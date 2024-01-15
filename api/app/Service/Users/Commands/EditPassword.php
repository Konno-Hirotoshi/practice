<?php

namespace App\Service\Users\Commands;

/**
 * 利用者パスワード編集DTO
 */
readonly class EditPassword
{
    /**
     * コンストラクタ
     */
    public function __construct(
        public int $id,
        public string $password,
    ) {
    }
}
