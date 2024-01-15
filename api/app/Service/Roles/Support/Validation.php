<?php

namespace App\Service\Roles\Support;

use App\Model\Users\Query as Users;
use App\Model\Roles\Query as Roles;
use App\Model\Permissions\Query as Permissions;
use App\Base\BaseValidator;

/**
 * 役割バリデーション
 */
class Validation extends BaseValidator
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Roles $roles,
        private Users $users,
        private Permissions $permissions
    ) {
    }

    /**
     * バリデーションチェック: 名称
     */
    public function validateName(?string $name, ?int $selfId = null): self
    {
        // 未入力ならチェック対象外
        if ($name === null) {
            return $this;
        }

        // 同名称の役割が存在するか
        $existsName = ($selfId === null)
            ? $this->roles->existsName($name)
            : $this->roles->existsNameOnUpdate($name, $selfId);
        if ($existsName) {
            return $this->setError('name', 'exists');
        };

        return $this;
    }

    /**
     * バリデーションチェック: 備考
     */
    public function validateNote(?string $note): self
    {
        // 未入力ならチェック対象外
        if ($note === null) {
            return $this;
        }
        return $this;
    }

    /**
     * バリデーションチェック: 権限IDリスト
     */
    public function validatePermissionIds(?array $permissionIds): self
    {
        // 未入力ならチェック対象外
        if ($permissionIds === null) {
            return $this;
        }

        // 存在する権限IDか
        $existsPermissionIds = $this->permissions->exists($permissionIds);
        if (!$existsPermissionIds) {
            return $this->setError('permissionIds', 'not_found');
        }

        return $this;
    }

    /**
     * バリデーションチェック: 削除IDリスト
     */
    public function validateDeleteIds(array $deleteIds): self
    {
        // 全権限ロールが含まれているか
        $isIncludedSuperRole = $this->isIncludedSuperRole($deleteIds);
        if ($isIncludedSuperRole) {
            return $this->setError('deleteIds', 'super_role');
        }

        // ユーザーに割り当てられているか
        $isAssignToUsers = $this->isAssignToUsers($deleteIds);
        if ($isAssignToUsers) {
            return $this->setError('deleteIds', 'role_assigned');
        }

        return $this;
    }

    /**
     * 全権限ロールが含まれているか
     * 
     * @param array $roleIds
     * @return bool
     */
    private function isIncludedSuperRole(array $roleIds): bool
    {
        return in_array(Roles::SUPER_ROLE_ID, $roleIds);
    }

    /**
     * ユーザーに割り当てられているか
     * 
     * @param array $roleIds
     * @return bool
     */
    private function isAssignToUsers(array $roleIds): bool
    {
        $assignedUserCount = $this->users->getCountByRoleId($roleIds);
        return $assignedUserCount > 0;
    }
}
