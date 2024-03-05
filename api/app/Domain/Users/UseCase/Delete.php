<?php

namespace App\Domain\Users\UseCase;

use App\Base\BaseUseCase;
use App\Domain\Users\User;
use App\Domain\Users\Interface\Validator;
use App\Storage\Users\Query as Users;

class Delete extends BaseUseCase implements Validator
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Users $users,
    ) {
    }

    /**
     * バリデーション
     *
     * @param User $user
     */
    public function validate(User $user)
    {
    }
}
