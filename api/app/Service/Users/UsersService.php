<?php

namespace App\Service\Users;

use App\Base\SearchOption;
use App\Model\Users\Command as Users;
use App\Service\Users\Commands\DeleteCommand;
use App\Service\Users\Commands\EditCommand;
use App\Service\Users\Commands\EditPasswordCommand;
use App\Service\Users\Entity\NewUser;
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
        $user = new NewUser(
            fullName: $fullName,
            email: $email,
            departmentId: $departmentId,
            roleId: $roleId,
            password: $password,
            note: $note,
            validation: $this->validation,
        );
        return $this->users->save($user);
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
        $dto = new EditCommand(
            id: $id,
            fullName: $fullName,
            email: $email,
            departmentId: $departmentId,
            roleId: $roleId,
            password: $password,
            note: $note,
            updatedAt: $updatedAt,
            validation: $this->validation,
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
        $dto = new EditPasswordCommand(
            id: $id,
            currentPassword: $currentPassword,
            newPassword: $newPassword,
            retypePassword: $retypePassword,
            validation: $this->validation,
        );
        $this->users->save($dto);
    }

    /**
     * 削除
     */
    public function delete(int|array $deleteIds)
    {
        $dto = new DeleteCommand(
            deleteIds: $deleteIds,
        );

        $this->users->save($dto);
    }
}
