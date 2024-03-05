<?php

namespace App\Domain\Users\UseCase;

use App\Base\BaseUseCase;
use App\Domain\Users\User;
use App\Domain\Users\Interface\Validator;
use App\Storage\Departments\Query as Departments;
use App\Storage\Roles\Query as Roles;

class Edit extends BaseUseCase implements Validator
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Roles $roles,
        private Departments $departments,
    ) {
    }

    /**
     * バリデーション
     *
     * @param User $user
     */
    public function validate(User $user)
    {
        // 部署IDが存在するか
        $existsDepartmentId = $this->departments->exists($user->departmentId);
        if (!$existsDepartmentId) {
            $this->setError('departmentId', 'not_found');
        };

        // 権限IDが存在するか
        $existsRoleId = $this->roles->exists($user->roleId);
        if (!$existsRoleId) {
            $this->setError('roleId', 'not_found');
        }

        $this->throwIfErrors();
    }
}
