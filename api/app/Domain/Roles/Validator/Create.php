<?php

namespace App\Domain\Roles\Validator;

use App\Base\BaseValidator;
use App\Domain\Roles\Role;
use App\Domain\Roles\Interface\Validator;
use App\Storage\Permissions\Query as Permissions;
use App\Storage\Roles\Query as Roles;

/**
 * 役割作成
 */
class Create extends BaseValidator implements Validator
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
    }
}
