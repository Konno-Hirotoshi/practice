<?php

namespace App\Domain\Users\UseCase;

use App\Base\BaseUseCase;
use App\Domain\Users\User;
use App\Storage\Departments\Query as Departments;
use App\Storage\Roles\Query as Roles;
use App\Storage\Users\Command as Users;

/**
 * 利用者データ編集
 */
class Edit extends BaseUseCase
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
     * @param int $id 役割ID
     * @param array $inputData 入力パラメータ
     * @return void
     */
    public function invoke($id, array $inputData): void
    {
        // 01. Restore Entity
        $updatedAt = $inputData['updated_at'] ?? null;
        $currentUser = $this->users->getEntity($id, $updatedAt, context: __CLASS__);
        
        // 02. Invoke Use Case
        $user = $currentUser->edit($inputData);

        // 03. Validate Entity
        $this->validate($user);

        // 04. Store Entity
        $this->users->save($user, context: __CLASS__);
    }

    /**
     * バリデーション
     *
     * @param User $user 利用者エンティティ
     */
    private function validate(User $user)
    {
        $this->validateDepartmentId($user);
        $this->validateRoleId($user);
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
