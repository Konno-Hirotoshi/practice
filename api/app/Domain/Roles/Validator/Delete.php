<?php

namespace App\Domain\Roles\Validator;

use App\Base\BaseValidator;
use App\Base\CustomException;
use App\Domain\Roles\Role;
use App\Domain\Roles\Interface\Validator;
use App\Storage\Roles\Query as Roles;
use App\Storage\Users\Query as Users;

class Delete extends BaseValidator implements Validator
{
    /**
     * コンストラクタ
     *
     * @param Roles $roles 役割
     * @param Users $users 利用者
     */
    public function __construct(
        private Roles $roles,
        private Users $users,
    ) {
    }

    /**
     * バリデーション
     *
     * @param Role $role 役割エンティティ
     */
    public function validate(Role $role)
    {
        // 全権限ロールが含まれているか
        $isIncludedSuperRole = $this->isIncludedSuperRole($role->id);
        if ($isIncludedSuperRole) {
            throw new CustomException('super_role');
        }

        // ユーザーに割り当てられているか
        $isAssignToUsers = $this->isAssignToUsers($role->id);
        if ($isAssignToUsers) {
            throw new CustomException('role_assigned');
        }
    }

    /**
     * 全権限ロールが含まれているか
     * 
     * @param int $roleId
     * @return bool
     */
    private function isIncludedSuperRole(int $roleId): bool
    {
        return $roleId === Roles::SUPER_ROLE_ID;
    }

    /**
     * ユーザーに割り当てられているか
     * 
     * @param int $roleId
     * @return bool
     */
    private function isAssignToUsers(int $roleId): bool
    {
        $assignedUserCount = $this->users->getCountByRoleId($roleId);
        return $assignedUserCount > 0;
    }
}
