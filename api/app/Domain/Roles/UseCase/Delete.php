<?php

namespace App\Domain\Roles\UseCase;

use App\Base\BaseUseCase;
use App\Base\CustomException;
use App\Domain\Roles\Role;
use App\Storage\Roles\Command as Roles;
use App\Storage\Users\Query as Users;

class Delete extends BaseUseCase
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
     * ユースケース実行
     *
     * @param int $id 取引ID
     * @param string $updatedAt 最終更新日時
     * @return void
     */
    public function invoke(int $id, $updatedAt = null)
    {
        // 01. Restore Entity
        $role = $this->roles->getEntity($id, $updatedAt, context: __CLASS__);

        // 02. Invoke Use Case
        // (NOP)

        // 03. Validate Entity
        $this->validate($role);

        // 04. Store Entity
        $this->roles->save($role, context: __CLASS__);
    }

    /**
     * バリデーション
     *
     * @param Role $role 役割エンティティ
     */
    private function validate(Role $role)
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
