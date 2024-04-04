<?php

namespace App\Domain\Users\UseCase;

use App\Base\BaseUseCase;
use App\Base\CustomException;
use App\Domain\Users\User;
use App\Storage\Users\Command as Users;
use App\Service\AuthenticationService;

class EditPassword extends BaseUseCase
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
     * ユースケース実行
     *
     * @param int $id 役割ID
     * @param string $password 新しいパスワード
     * @param string $currentPassword 現在のパスワード
     * @param string $retypePassword 新しいパスワード(再入力)
     * 
     * @return void
     */
    public function invoke($id, string $password, string $currentPassword, string $retypePassword): void
    {
        $this->currentPassword = $currentPassword;
        $this->retypePassword = $retypePassword;

        // 01. Restore Entity
        $updatedAt = $inputData['updated_at'] ?? null;
        $currentUser = $this->users->getEntity($id, $updatedAt, context: __CLASS__);
        
        // 02. Invoke Use Case
        $user = $currentUser->editPassword($id, $password);

        // 03. Validate Entity
        $this->validate($user);

        // 04. Store Entity
        $this->users->save($user, context: __CLASS__);
    }

    /**
     * バリデーション
     *
     * @param User $user
     */
    private function validate(User $user)
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
