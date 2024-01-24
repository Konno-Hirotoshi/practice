<?php

namespace App\Service\Roles;

use App\Base\CustomException;
use App\Base\SearchOption;
use App\Model\Users\Query as Users;
use App\Model\Roles\Command as Roles;
use App\Service\Roles\Commands\Create;
use App\Service\Roles\Commands\Delete;
use App\Service\Roles\Commands\Edit;
use App\Service\Roles\Support\Validation;

/**
 * 役割 - サービスクラス
 */
class RolesService
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Roles $roles,
        private Users $users,
        private Validation $validation,
    ) {
    }

    /**
     * 一覧検索
     */
    public function search(array $search = [], array $sort = [], int $page = 1, int $perPage = 3): array
    {
        $option = SearchOption::create($search, $sort, $page, $perPage, [
            'id' => 'value',
        ]);

        return $this->roles->search($option);
    }

    /**
     * 詳細情報取得
     */
    public function get(int $id)
    {
        return $this->roles->get($id);
    }

    /**
     * 新規作成
     */
    public function create(
        string $name,
        string $note = '',
        array $permissionIds = [],
    ): int {
        $this->validation
            ->validateName($name)
            ->validateNote($note)
            ->validatePermissionIds($permissionIds)
            ->throwIfErrors();

        $dto = new Create(
            name: $name,
            note: $note,
            permissionIds: $permissionIds,
        );

        return $this->roles->save($dto);
    }

    /**
     * 編集
     */
    public function edit(
        int $id,
        ?string $name = null,
        ?string $note = null,
        ?array $permissionIds = null,
        ?string $updatedAt = null,
    ) {
        $this->validation
            ->validateName($name, $id)
            ->validateNote($note)
            ->validatePermissionIds($permissionIds)
            ->throwIfErrors();

        $dto = new Edit(
            id: $id,
            name: $name,
            note: $note,
            permissionIds: $permissionIds,
            updatedAt: $updatedAt,
        );

        $this->roles->save($dto);
    }

    /**
     * 削除
     */
    public function delete(int|array $deleteIds)
    {
        if (!is_array($deleteIds)) {
            $deleteIds = [$deleteIds];
        }

        // 全権限ロールが含まれているか
        $isIncludedSuperRole = $this->isIncludedSuperRole($deleteIds);
        if ($isIncludedSuperRole) {
            throw new CustomException('super_role');
        }

        // ユーザーに割り当てられているか
        $isAssignToUsers = $this->isAssignToUsers($deleteIds);
        if ($isAssignToUsers) {
            throw new CustomException('role_assigned');
        }

        $dto = new Delete(
            deleteIds: $deleteIds,
        );

        $this->roles->save($dto);
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
