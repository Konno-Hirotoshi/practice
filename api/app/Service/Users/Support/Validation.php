<?php

namespace App\Service\Users\Support;

use App\Base\CustomException;
use App\Base\BaseValidator;
use App\Model\Departments\Query as Departments;
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
        private Departments $departments,
        private AuthenticationService $authenticationService,
    ) {
    }

    /**
     * バリデーションチェック: 部署ID
     */
    public function validateDepartmentId(?int $departmentId): self
    {
        // 未入力ならチェック対象外
        if ($departmentId === null) {
            return $this;
        }

        // 部署IDが存在するか
        $existsDepartmentId = $this->departments->exists($departmentId);
        if (!$existsDepartmentId) {
            return $this->setError('departmentId', 'not_found');
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
            return $this->setError('roleId', 'not_found');
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
        try {
            $email = $this->users->getEmailById($id);
            $this->authenticationService->authenticate($email, $password);
        } catch (CustomException $e) {
            return $this->setError('current_password', match ($e->errors()['reason']) {
                'record_not_found' => 'not_equal',
                'empty' => 'not_equal',
                'locked' => 'locked',
                'failure' => 'not_equal',
            });
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
