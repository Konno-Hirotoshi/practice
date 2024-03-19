<?php

namespace App\Domain\Users\Validator;

use App\Base\BaseValidator;
use App\Base\CustomException;
use App\Domain\Users\User;
use App\Domain\Users\Interface\Validator;
use App\Storage\Users\Query as Users;
use App\Service\AuthenticationService;

class EditPassword extends BaseValidator implements Validator
{
    /** @var string 現在のパスワード */
    private readonly string $currentPassword;

    /** @var string 新しいパスワード（再入力） */
    private readonly string $retypePassword;

    /**
     * コンストラクタ
     */
    public function __construct(
        private Users $users,
        private AuthenticationService $authenticationService,
    ) {
    }

    /**
     * バリデーション
     *
     * @param User $user
     */
    public function validate(User $user)
    {
        // 現在のパスワードが正しいか
        $curentPasswordError = $this->validateCurrentPassword($user->id, $this->currentPassword);
        if ($curentPasswordError) {
            return $this->setError('current_password', $curentPasswordError);
        }

        // 新しいパスワード（再入力）が一致するか
        if ($user->password !== $this->retypePassword) {
            return $this->setError('retype_password', 'not-equal');
        };

        $this->throwIfErrors();
    }

    /**
     * 追加情報をセットする
     *
     * @param string $currentPassword 現在のパスワード
     * @param string $retypePassword 新しいパスワード（再入力）
     */
    public function setAdditionalInfo(string $currentPassword, $retypePassword): void
    {
        $this->currentPassword = $currentPassword;
        $this->retypePassword = $retypePassword;
    }

    /**
     * バリデーションチェック: 現在のパスワード
     */
    private function validateCurrentPassword(int $id, string $password): string|false
    {
        try {
            $email = $this->users->getEmailById($id);
            $this->authenticationService->authenticate($email, $password);
        } catch (CustomException $e) {
            return match ($e->errors()['reason']) {
                'record_not_found' => 'not_equal',
                'empty' => 'not_equal',
                'locked' => 'locked',
                'failure' => 'not_equal',
            };
        }
        return false;
    }
}
