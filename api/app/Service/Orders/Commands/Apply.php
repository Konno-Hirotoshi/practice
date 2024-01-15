<?php

namespace App\Service\Orders\Commands;

/**
 * 取引承認フロー: 申請DTO
 */
readonly class Apply
{
    /**
     * コンストラクタ
     */
    public function __construct(
        public int $id,
        public array $approvalFlows,
        public ?string $updatedAt,
    ) {
    }
}
