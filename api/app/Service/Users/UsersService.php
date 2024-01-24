<?php

namespace App\Service\Users;

use App\Base\SearchOption;
use App\Model\Users\Command as Users;
use App\Service\Users\Commands\Create;
use App\Service\Users\Commands\Delete;
use App\Service\Users\Commands\Edit;
use App\Service\Users\Commands\EditPassword;
use App\Service\Users\Support\Validation;

/**
 * 利用者サービス
 */
class UsersService
{
    /**
     * コンストラクタ
     */
    public function __construct(
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
            'full_name' => 'like',
            'email' => 'like',
            'tags' => function ($key, $value, $query) {
                return $query;
            },
        ]);

        return $this->users->search($option);
    }

    /**
     * 詳細情報取得
     */
    public function get(int $id)
    {
        return $this->users->get($id);
    }

    /**
     * 新規作成
     */
    public function create(
        string $fullName,
        string $email,
        int $departmentId,
        int $roleId = 0,
        string $password = 'default@',
        string $note = '',
    ): int {
        $this->validation
            ->validateDepartmentId($departmentId)
            ->validateRoleId($roleId)
            ->validatePassword($password)
            ->validateNote($note)
            ->throwIfErrors();

        $dto = new Create(
            fullName: $fullName,
            email: $email,
            departmentId: $departmentId,
            roleId: $roleId,
            password: $password,
            note: $note,
        );

        return $this->users->save($dto);
    }

    /**
     * 編集
     */
    public function edit(
        int $id,
        ?string $fullName = null,
        ?string $email = null,
        ?int $departmentId = null,
        ?int $roleId = null,
        ?string $password = null,
        ?string $note = null,
        ?string $updatedAt = null,
    ) {
        $this->validation
            ->validateDepartmentId($departmentId)
            ->validateRoleId($roleId)
            ->validatePassword($password)
            ->validateNote($note)
            ->throwIfErrors();

        $dto = new Edit(
            id: $id,
            fullName: $fullName,
            email: $email,
            departmentId: $departmentId,
            roleId: $roleId,
            password: $password,
            note: $note,
            updatedAt: $updatedAt,
        );

        $this->users->save($dto);
    }

    /**
     * 利用者パスワードを編集する
     */
    public function editPassword(
        int $id,
        string $currentPassword,
        string $newPassword,
        string $retypePassword,
    ): void {
        $this->validation
            ->validateCurrentPassword($currentPassword, $id)
            ->validatePassword($newPassword)
            ->validateRetypePassword($retypePassword, $newPassword)
            ->throwIfErrors();

        $dto = new EditPassword(
            id: $id,
            password: $newPassword,
        );

        $this->users->save($dto);
    }

    /**
     * 削除
     */
    public function delete(int|array $deleteIds)
    {
        if (!is_array($deleteIds)) {
            $deleteIds = [$deleteIds];
        }

        $dto = new Delete(
            deleteIds: $deleteIds,
        );

        $this->users->save($dto);
    }
}
