<?php

namespace App\Domain\Users\Support;

use App\Base\BaseValidator;
use App\Domain\Users\User;
use App\Storage\Departments\Query as Departments;
use App\Storage\Roles\Query as Roles;
use App\Storage\Users\Query as Users;

/**
 * 利用者 - 編集時ビジネスルール
 */
class EditRule extends BaseValidator
{
    /**
     * コンストラクタ
     *
     * @param Users $users 利用者
     * @param Roles $roles 役割
     * @param Departments $departments 部署
     */
    public function __construct(
        private Users $users,
        private Roles $roles,
        private Departments $departments,
    ) {
    }

    /**
     * バリデーション
     *
     * @param User $user 利用者エンティティ
     */
    public function validate(User $user)
    {
        $this->validateEmail($user);
        $this->validateDepartmentId($user);
        $this->validateRoleId($user);
        $this->throwIfErrors();
    }

    /**
     * バリデーション：メールアドレス
     *
     * @param User $user 利用者エンティティ
     */
    private function validateEmail(User $user)
    {
        // メールアドレスがセットされているか
        if (!isset($user->email)) {
            return;
        }

        // 同じメールアドレスが使用されていないか
    }

    /**
     * バリデーション：部署ID
     *
     * @param User $user 利用者エンティティ
     */
    private function validateDepartmentId(User $user)
    {
        // 役割IDがセットされているか
        if (!isset($user->departmentId)) {
            return;
        }

        // 部署IDが存在するか
        if (!$this->departments->exists($user->departmentId)) {
            $this->setError('departmentId', 'not_found');
        };
    }

    /**
     * バリデーション：役割ID
     *
     * @param User $user 利用者エンティティ
     */
    private function validateRoleId(User $user)
    {
        // 役割IDがセットされているか
        if (!isset($user->roleId)) {
            return;
        }

        // 役割IDが存在するか
        if (!$this->roles->exists($user->roleId)) {
            $this->setError('roleId', 'not_found');
        }
    }
}
