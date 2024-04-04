<?php

namespace App\Domain\Roles\UseCase;

use App\Base\BaseUseCase;
use App\Domain\Roles\Role;
use App\Storage\Permissions\Query as Permissions;
use App\Storage\Roles\Command as Roles;

class Edit extends BaseUseCase
{
    /**
     * コンストラクタ
     * 
     * @param Roles $roles 役割
     * @param Permissions $permissions 権限
     */
    public function __construct(
        private Roles $roles,
        private Permissions $permissions,
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
        $currentRole = $this->roles->getEntity($id, $updatedAt, context: __CLASS__);
        
        // 02. Invoke Use Case
        $role = $currentRole->edit($inputData);

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
                return $this->setError('permissionIds', 'not_found');
            }
        }
        $this->throwIfErrors();
    }
}
