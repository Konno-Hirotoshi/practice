<?php

namespace App\Service\Orders\Commands;

/**
 * 取引作承認フロー承認DTO
 */
readonly class Approve
{
    /**
     * コンストラクタ
     */
    public function __construct(
        public int $id,
        public int $sequenceNo,
        public int $approvalDate,
        public int $newStatus,
        public ?string $updatedAt,
    ) {
    }
}
