<?php

namespace App\Domain\Roles\Validator;

use App\Base\BaseValidator;
use App\Domain\Roles\Role;
use App\Domain\Roles\Interface\Validator;
use App\Storage\Permissions\Query as Permissions;
use App\Storage\Roles\Query as Roles;

class Edit extends BaseValidator implements Validator
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
     * バリデーション
     *
     * @param Role $role 役割エンティティ
     */
    public function validate(Role $role)
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
