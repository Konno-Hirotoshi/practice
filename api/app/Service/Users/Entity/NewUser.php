<?php

namespace App\Service\Users\Entity;

use App\Service\Users\Support\Validation;

/**
 * 新規利用者
 */
readonly class NewUser
{
    /** @var string フルネーム */
    public string $fullName;

    /** @var string メールアドレス */
    public string $email;

    /** @var string 部署ID */
    public int $departmentId;

    /** @var string 役割ID */
    public int $roleId;

    /** @var string パスワード */
    public string $password;

    /** @var string 備考 */
    public string $note;

    /**
     * コンストラクタ
     */
    public function __construct(
        string $fullName,
        string $email,
        int $departmentId,
        int $roleId,
        string $password,
        string $note,
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

        $this->fullName = $fullName;
        $this->email = $email;
        $this->departmentId = $departmentId;
        $this->roleId = $roleId;
        $this->password = $password;
        $this->note = $note;
    }
}
