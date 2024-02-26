<?php

namespace App\Service\Users\Commands;

use App\Service\Users\Support\Validation;

/**
 * 利用者編集
 */
readonly class EditCommand
{
    /** @var ?int 利用者ID */
    public ?int $id;

    /** @var ?string フルネーム */
    public ?string $fullName;

    /** @var ?string メールアドレス */
    public ?string $email;

    /** @var ?string 部署ID */
    public ?int $departmentId;

    /** @var ?string 役割ID */
    public ?int $roleId;

    /** @var ?string パスワード */
    public ?string $password;

    /** @var ?string 備考 */
    public ?string $note;

    /** @var ?string 最終更新日 */
    public ?string $updatedAt;

    /**
     * コンストラクタ
     */
    public function __construct(
        int $id,
        ?string $fullName = null,
        ?string $email = null,
        ?int $departmentId = null,
        ?int $roleId = null,
        ?string $password = null,
        ?string $note = null,
        ?string $updatedAt = null,
        private Validation $validation,
    ) {
        $this->validation
            ->validateFullName($fullName)
            ->validateEmail($email)
            ->validateDepartmentId($departmentId)
            ->validateRoleId($roleId)
            ->validatePassword($password)
            ->validateNote($note)
            ->throwIfErrors();

        $this->id = $id;
        $this->fullName = $fullName;
        $this->email = $email;
        $this->departmentId = $departmentId;
        $this->roleId = $roleId;
        $this->password = $password;
        $this->note = $note;
        $this->updatedAt = $updatedAt;
    }
}
