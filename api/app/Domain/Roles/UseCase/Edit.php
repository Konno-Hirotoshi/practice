<?php

namespace App\Domain\Roles\UseCase;

use App\Base\BaseUseCase;
use App\Domain\Roles\Role;
use App\Domain\Roles\Interface\Validator;
use App\Storage\Permissions\Query as Permissions;
use App\Storage\Roles\Query as Roles;

class Edit extends BaseUseCase implements Validator
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Roles $roles,
        private Permissions $permissions,
    ) {
    }

    /**
     * バリデーション
     *
     * @param Role $role
     */
    public function validate(Role $role)
    {
        // 同名称の役割が存在するか
        $existsDepartmentId = $this->roles->existsNameOnUpdate($role->name, $role->id);
        if (!$existsDepartmentId) {
            $this->setError('name', 'exists');
        };

        // 権限IDが存在するか
        $existsRoleId = $this->permissions->exists($role->permissionIds);
        if (!$existsRoleId) {
            return $this->setError('permissionIds', 'not_found');
        }
        $this->throwIfErrors();
    }
}
