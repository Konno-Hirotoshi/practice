<?php

namespace App\Domain\Users\UseCase;

use App\Base\BaseUseCase;
use App\Domain\Users\User;
use App\Storage\Users\Command as Users;
use App\Storage\Roles\Query as Roles;
use App\Storage\Departments\Query as Departments;

/**
 * 利用者登録
 */
class Create extends BaseUseCase
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
     * ユースケース実行
     *
     * @param array $inputData 入力データ
     * @return int
     */
    public function invoke(array $inputData)
    {
        // 01. Create Entity
        $user = new User($inputData);

        // 02. Validate Entity
        $this->validate($user);

        // 02. Store Entity
        $userId = $this->users->save($user, context: __CLASS__);

        // 03. Return ID
        return $userId;
    }

    /**
     * バリデーション
     *
     * @param User $user 利用者エンティティ
     */
    private function validate(User $user)
    {
        $this->validateFullName($user);
        $this->validateEmail($user);
        $this->validateDepartmentId($user);
        $this->validateRoleId($user);
        $this->throwIfErrors();
    }

    /**
     * バリデーション：氏名
     *
     * @param User $user 利用者エンティティ
     */
    private function validateFullName(User $user)
    {
        // 氏名がセットされているか
        if (!isset($user->fullName)) {
            return $this->setError('fullName', 'unset');
        }
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
            return $this->setError('email', 'unset');
        }
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
            return $this->setError('departmentId', 'unset');
        }

        // 部署IDが存在するか
        $existsDepartmentId = $this->departments->exists($user->departmentId);
        if (!$existsDepartmentId) {
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
        $existsRoleId = $this->roles->exists($user->roleId);
        if (!$existsRoleId) {
            $this->setError('roleId', 'not_found');
        }
    }
}
