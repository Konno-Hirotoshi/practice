<?php

namespace App\Service\Orders\Commands;

/**
 * 取引承認フロー開始DTO
 */
readonly class StartApprovalFlow
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
