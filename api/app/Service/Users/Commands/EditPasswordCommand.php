<?php

namespace App\Service\Users\Commands;

use App\Service\Users\Support\Validation;

/**
 * 利用者パスワード更新
 */
readonly class EditPasswordCommand
{
    /** @var int 利用者ID */
    public int $id;

    /** @var string パスワード */
    public string $password;

    /**
     * コンストラクタ
     */
    public function __construct(
        int $id,
        string $currentPassword,
        string $newPassword,
        string $retypePassword,
        private Validation $validation,
    ) {
        $this->validation
            ->validateCurrentPassword($currentPassword, $id)
            ->validatePassword($newPassword)
            ->validateRetypePassword($retypePassword, $newPassword)
            ->throwIfErrors();

        $this->id = $id;
        $this->password = $newPassword;
    }
}
