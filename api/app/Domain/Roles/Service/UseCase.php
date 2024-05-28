<?php

namespace App\Domain\Roles\Service;

use App\Base\BaseUseCase;
use App\Domain\Roles\Dto\CreateDto;
use App\Domain\Roles\Dto\EditDto;
use App\Domain\Roles\Role;
use App\Storage\Permissions\Query as Permissions;
use App\Storage\Roles\Command as Roles;
use App\Storage\Users\Query as Users;

/**
 * 役割 - ユースケースクラス
 */
class UseCase extends BaseUseCase
{
    /**
     * コンストラクタ
     *
     * @param Roles $roles 役割
     * @param Permissions $permissions 権限
     * @param Users $users 利用者
     * @param ?int $id 役割ID
     * @param ?string $updatedAt 最終更新日時
     */
    public function __construct(
        private Roles $roles,
        private Permissions $permissions,
        private Users $users,
        private ?int $id = null,
        private ?string $updatedAt = null,
    ) {
    }

    /**
     * 対象を指定する
     *
     * @param int $id 役割ID
     * @param ?string $updatedAt 最終更新日時
     * @return self
     */
    public function target(int $id, ?string $updatedAt = null): self
    {
        $this->id = $id;
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * 新規作成
     *
     * @param CreateDto $dto 入力データ
     * @return int
     */
    public function create(CreateDto $dto): int
    {
        $role = Role::create($dto);

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

        return $this->roles->save($role, context: __METHOD__);
    }

    /**
     * 編集
     *
     * @param EditDto $dto 入力データ
     * @return void
     */
    public function edit(EditDto $dto): void
    {
        $role = $this->roles
            ->getEntity($this->id, $this->updatedAt, context: __METHOD__)
            ->edit($dto);

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
     * @return void
     */
    public function delete(): void
    {
        $role = $this->roles
            ->getEntity($this->id, $this->updatedAt, context: __METHOD__)
            ->delete();

        // 役割が利用者に割り当てられているか
        if ($this->isAssignToUsers($role->id)) {
            $this->setError('role_assign')->throw();
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
