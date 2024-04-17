<?php

namespace App\Domain\Roles;

use App\Base\BaseUseCase;
use App\Storage\Permissions\Query as Permissions;
use App\Storage\Roles\Command as Roles;
use App\Storage\Users\Query as Users;

/**
 * 役割 - ユースケースクラス
 */
class RoleUseCase extends BaseUseCase
{
    /**
     * コンストラクタ
     *
     * @param Roles $roles 役割
     * @param Permissions $permissions 権限
     * @param Users $users 利用者
     */
    public function __construct(
        private Roles $roles,
        private Permissions $permissions,
        private Users $users,
    ) {
    }

    /**
     * 新規作成
     *
     * @param array $inputData 入力データ
     * @return int
     */
    public function create(array $inputData)
    {
        $role = new Role($inputData + [
            // 備考 デフォルト値
            'note' => '',
        ]);

        // 同名称の役割が存在するか
        $existsDepartmentId = $this->roles->existsName($role->name);
        if ($existsDepartmentId) {
            $this->setError('name', 'exists');
        };

        // 権限IDが存在するか
        $existsRoleId = $this->permissions->exists($role->permissionIds);
        if (!$existsRoleId) {
            return $this->setError('permissionIds', 'not_found');
        }

        $this->throwIfErrors();

        return $this->roles->save(
            role: $role,
            context: __METHOD__,
        );
    }

    /**
     * 編集
     *
     * @param int $id 役割ID
     * @param array $inputData 入力パラメータ
     * @return void
     */
    public function edit($id, array $inputData): void
    {
        $role = $this->roles
            ->getEntity($id, $inputData['updated_at'] ?? null, context: __METHOD__)
            ->edit($inputData);

        if (isset($role->name)) {
            // 同名称の役割が存在するか
            $existsDepartmentId = $this->roles->existsNameOnUpdate($role->name, $role->id);
            if ($existsDepartmentId) {
                $this->setError('name', 'exists');
            };
        }

        if (isset($role->permissionIds)) {
            // 権限IDが存在するか
            $existsRoleId = !$role->permissionIds || $this->permissions->exists($role->permissionIds);
            if (!$existsRoleId) {
                $this->setError('permissionIds', 'not_found');
            }
        }
        $this->throwIfErrors();
        
        $this->roles->save($role, context: __METHOD__);
    }

    /**
     * 削除
     *
     * @param int $id 役割ID
     * @param string $updatedAt 最終更新日時
     * @return void
     */
    public function delete(int $id, $updatedAt = null)
    {
        $role = $this->roles
            ->getEntity($id, $updatedAt, context: __METHOD__)
            ->delete();

        // 権限が利用者に割り当てられているか
        if ($this->isAssignToUsers($role->id)) {
            $this->throw('role_assigned');
        }

        $this->roles->save($role, context: __METHOD__);
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
