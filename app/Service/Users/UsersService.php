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
        string $email,
        string $password = 'default@',
        int $roleId = 0,
        string $note = '',
    ): int {
        $this->validation
            ->validateEmail($email)
            ->validatePassword($password)
            ->validateRoleId($roleId)
            ->validateNote($note)
            ->throwIfErrors();

        $create = new Create(
            email: $email,
            password: $password,
            roleId: $roleId,
            note: $note,
        );

        return $this->users->save($create);
    }

    /**
     * 編集
     */
    public function edit(
        int $id,
        ?string $email = null,
        ?int $roleId = null,
        ?string $password = null,
        ?string $note = null,
        ?string $updatedAt = null,
    ) {
        $this->validation
            ->validateEmail($email, $id)
            ->validatePassword($password)
            ->validateRoleId($roleId)
            ->validateNote($note)
            ->throwIfErrors();

        $edit = new Edit(
            id: $id,
            email: $email,
            roleId: $roleId,
            password: $password,
            note: $note,
            updatedAt: $updatedAt,
        );

        $this->users->save($edit);
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

        $editPassword = new EditPassword(
            id: $id,
            password: $newPassword,
        );

        $this->users->save($editPassword);
    }

    /**
     * 削除
     */
    public function delete(int|array $deleteIds)
    {
        if (!is_array($deleteIds)) {
            $deleteIds = [$deleteIds];
        }

        $this->validation
            ->validateDeleteIds($deleteIds)
            ->throwIfErrors();

        $delete = new Delete(
            deleteIds: $deleteIds,
        );

        $this->users->save($delete);
    }
}
