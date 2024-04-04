<?php

namespace App\Domain\Users\UseCase;

use App\Base\BaseUseCase;
use App\Domain\Users\User;
use App\Storage\Users\Command as Users;

/**
 * 利用者削除
 */
class Delete extends BaseUseCase
{
    /**
     * コンストラクタ
     *
     * @param Users $users 利用者
     */
    public function __construct(
        private Users $users,
    ) {
    }

    /**
     * ユースケース実行
     *
     * @param int $id 利用者ID
     * @param string $updatedAt 最終更新日時
     * @return void
     */
    public function invoke(int $id, $updatedAt = null)
    {
        // 01. Restore Entity
        $user = $this->users->getEntity($id, $updatedAt, context: __CLASS__);

        // 02. Invoke Use Case
        // (NOP)

        // 03. Validate Entity
        $this->validate($user);

        // 04. Store Entity
        $this->users->save($user, context: __CLASS__);
    }

    /**
     * バリデーション
     *
     * @param User $user 利用者エンティティ
     */
    private function validate(User $user)
    {
    }
}
