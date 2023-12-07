<?php

namespace App\Service\Orders\Commands;

/**
 * 取引作承認フロー却下DTO
 */
readonly class Reject
{
    /**
     * コンストラクタ
     */
    public function __construct(
        public int $id,
        public int $sequenceNo,
        public int $approvalDate,
        public ?string $updatedAt,
    ) {
    }
}
