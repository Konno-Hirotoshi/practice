<?php

namespace App\Service\Users\Support;

use App\Base\CustomException;
use App\Base\BaseValidator;
use App\Model\Users\Query as Users;
use App\Model\Roles\Query as Roles;
use App\Service\AuthenticationService;

/**
 * 利用者バリデーション
 */
class Validation extends BaseValidator
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Users $users,
        private Roles $roles,
        private AuthenticationService $authenticationService,
    ) {
    }

    /**
     * バリデーションチェック: メールアドレス
     */
    public function validateEmail(?string $email, ?int $selfId = null): self
    {
        // 未入力ならチェック対象外
        if ($email === null) {
            return $this;
        }

        // メールアドレス形式か (簡易判定)
        $validPattern = preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9.-]+$/', $email) > 0;
        if (!$validPattern) {
            return $this->setError('email', 'invalid-pattern');
        }

        // 同メールアドレスの利用者が存在するか
        $existsEmail = ($selfId === null)
            ? $this->users->existsEmail($email)
            : $this->users->existsEmailOnUpdate($email, $selfId);
        if ($existsEmail) {
            return $this->setError('email', 'exists');
        };

        return $this;
    }

    /**
     * バリデーションチェック: 権限ID
     */
    public function validateRoleId(?int $roleId): self
    {
        // 未入力ならチェック対象外
        if ($roleId === null) {
            return $this;
        }

        // 権限IDが存在するか
        $existsRoleId = $this->roles->exists($roleId);
        if (!$existsRoleId) {
            return $this->setError('roleId', 'not-found');
        }

        return $this;
    }

    /**
     * バリデーションチェック: パスワード
     */
    public function validatePassword(?string $password): self
    {
        // 未入力ならチェック対象外
        if ($password === null) {
            return $this;
        }

        // パスワードがルールに合致するか
        $validPassword = $this->isValidPassword($password);
        if (!$validPassword) {
            return $this->setError('password', 'rule');
        };

        return $this;
    }

    /**
     * バリデーションチェック: 現在のパスワード
     */
    public function validateCurrentPassword(string $password, int $id): self
    {
        $email = $this->users->getEmailById($id);
        try {
            $this->authenticationService->authenticate($email, $password);
        } catch (CustomException $e) {
            return match ($e->errors()['reason']) {
                'locked' => $this->setError('current_password', 'locked'),
                'failure' => $this->setError('current_password', 'not-equal'),
            };
        }
        return $this;
    }

    /**
     * バリデーションチェック: 再入力パスワード
     */
    public function validateRetypePassword(string $password, $newPassword): self
    {
        // パスワードが一致するか
        if ($password !== $newPassword) {
            return $this->setError('retype_password', 'not-equal');
        };

        return $this;
    }

    /**
     * バリデーションチェック: 備考
     */
    public function validateNote(?string $note): self
    {
        // 未入力ならチェック対象外
        if ($note === null) {
            return $this;
        }
        return $this;
    }

    /**
     * バリデーションチェック: 削除IDリスト
     */
    public function validateDeleteIds(array $deleteIds): self
    {
        return $this;
    }

    /**
     * パスワードがルールに合致するか
     * 
     * 【パスワードルール】
     * ・小文字を含む
     * ・大文字を含む
     * ・数字を含む
     * ・記号を含む
     */
    private function isValidPassword(string $password): bool
    {
        $hasUpperCharacter = preg_match('/[a-z]/', $password) === 1;
        $hasLowerCharacter = preg_match('/[A-Z]/', $password) === 1;
        $hasNumer = preg_match('/[0-9]/', $password) === 1;
        $hasSymbol = preg_match('/[!-\/:-@\[-`{-~]/', $password) === 1;

        return ($hasUpperCharacter && $hasLowerCharacter && $hasNumer && $hasSymbol);
    }
}
