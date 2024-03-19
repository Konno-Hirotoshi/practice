<?php

namespace App\Domain\Users\Validator;

use App\Base\BaseValidator;
use App\Domain\Users\User;
use App\Domain\Users\Interface\Validator;
use App\Storage\Users\Query as Users;

/**
 * 利用者削除
 */
class Delete extends BaseValidator implements Validator
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
     * バリデーション
     *
     * @param User $user 利用者エンティティ
     */
    public function validate(User $user)
    {
    }
}
