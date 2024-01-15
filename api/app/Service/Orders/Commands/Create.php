<?php

namespace App\Service\Orders\Commands;

/**
 * 取引作成DTO
 */
readonly class Create
{
    /**
     * コンストラクタ
     */
    public function __construct(
        public string $title,
        public string $body,
    ) {
    }
}
