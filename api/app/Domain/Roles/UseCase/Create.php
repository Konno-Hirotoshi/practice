<?php

namespace App\Domain\Roles\UseCase;

use App\Base\BaseUseCase;
use App\Domain\Roles\Role;
use App\Storage\Permissions\Query as Permissions;
use App\Storage\Roles\Command as Roles;

/**
 * 役割作成
 */
class Create extends BaseUseCase
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
     * @param array $inputData 入力データ
     * @return int
     */
    public function invoke(array $inputData)
    {
        // 01. Create Entity
        $role = new Role($inputData);

        // 02. Validate Entity
        $this->validate($role);

        // 02. Store Entity
        $roleId = $this->roles->save($role, context: __CLASS__);

        // 03. Return ID
        return $roleId;
    }

    /**
     * バリデーション
     *
     * @param Role $role 役割エンティティ
     */
    private function validate(Role $role)
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
