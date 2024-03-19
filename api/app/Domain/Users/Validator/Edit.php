<?php

namespace App\Domain\Users\Validator;

use App\Base\BaseValidator;
use App\Domain\Users\User;
use App\Domain\Users\Interface\Validator;
use App\Storage\Departments\Query as Departments;
use App\Storage\Roles\Query as Roles;

/**
 * 利用者データ編集
 */
class Edit extends BaseValidator implements Validator
{
    /**
     * コンストラクタ
     *
     * @param Roles $roles 役割
     * @param Departments $departments 部署
     */
    public function __construct(
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
