<?php

namespace App\Domain\Users;

use App\Base\BaseUseCase;
use App\Base\CustomException;
use App\Storage\Departments\Query as Departments;
use App\Storage\Roles\Query as Roles;
use App\Storage\Users\Command as Users;
use App\Service\AuthenticationService;

/**
 * 役割 - ユースケースクラス
 */
class UserUseCase extends BaseUseCase
{
    /**
     * コンストラクタ
     *
     * @param Users $users 役割
     * @param Roles $roles 権限
     * @param Departments $departments 部署
     */
    public function __construct(
        private Users $users,
        private Roles $roles,
        private Departments $departments,
        private AuthenticationService $authenticationService,
    ) {
    }

    /**
     * 新規作成
     *
     * @param array $inputData 入力データ
     * @return int
     */
    public function create(array $inputData)
    {
        $user = new User($inputData);

        $this->validateFullName($user);
        $this->validateEmail($user);
        $this->validateDepartmentId($user);
        $this->validateRoleId($user);
        $this->throwIfErrors();

        return $this->users->save(
            user: $user,
            context: __METHOD__,
        );
    }

    /**
     * 編集
     *
     * @param int $id 役割ID
     * @param array $inputData 入力パラメータ
     * @return void
     */
    public function edit($id, array $inputData): void
    {
        $user = $this->users
            ->getEntity($id, $inputData['updated_at'] ?? null, context: __METHOD__)
            ->edit($inputData);

        $this->validateDepartmentId($user);
        $this->validateRoleId($user);
        $this->throwIfErrors();
        
        $this->users->save($user, context: __METHOD__);
    }

    /**
     * パスワード編集
     *
     * @param int $id 役割ID
     * @param string $password 新しいパスワード
     * @param string $currentPassword 現在のパスワード
     * @param string $retypePassword パスワード再入力
     * @param string $updatedAt 最終更新日時
     * @return void
     */
    public function editPassword($id, string $password, string $currentPassword, string $retypePassword, $updatedAt = null): void
    {
        $user = $this->users
            ->getEntity($id, $updatedAt, context: __METHOD__)
            ->editPassword($password);

        // 現在のパスワードが正しいか
        $curentPasswordError = $this->validateCurrentPassword($user->id, $currentPassword);
        if ($curentPasswordError) {
            $this->setError('current_password', $curentPasswordError);
        }

        // 新しいパスワード（再入力）が一致するか
        if ($user->password !== $retypePassword) {
            $this->setError('retype_password', 'not-equal');
        };
        $this->throwIfErrors();

        $this->users->save($user, context: __METHOD__);
    }

    /**
     * 削除
     *
     * @param int $id 役割ID
     * @param string $updatedAt 最終更新日時
     * @return void
     */
    public function delete(int $id, $updatedAt = null)
    {
        $user = $this->users
            ->getEntity($id, $updatedAt, context: __METHOD__);

        $this->users->save($user, context: __METHOD__);
    }


    /**
     * バリデーション：氏名
     *
     * @param User $user 利用者エンティティ
     */
    private function validateFullName(User $user)
    {
        // 氏名がセットされているか
        if (!isset($user->fullName)) {
            return $this->setError('fullName', 'unset');
        }
    }

    /**
     * バリデーション：メールアドレス
     *
     * @param User $user 利用者エンティティ
     */
    private function validateEmail(User $user)
    {
        // メールアドレスがセットされているか
        if (!isset($user->email)) {
            return $this->setError('email', 'unset');
        }
    }

    /**
     * バリデーション：部署ID
     *
     * @param User $user 利用者エンティティ
     */
    private function validateDepartmentId(User $user)
    {
        // 役割IDがセットされているか
        if (!isset($user->departmentId)) {
            return $this->setError('departmentId', 'unset');
        }

        // 部署IDが存在するか
        $existsDepartmentId = $this->departments->exists($user->departmentId);
        if (!$existsDepartmentId) {
            $this->setError('departmentId', 'not_found');
        };
    }

    /**
     * バリデーション：役割ID
     *
     * @param User $user 利用者エンティティ
     */
    private function validateRoleId(User $user)
    {
        // 役割IDがセットされているか
        if (!isset($user->roleId)) {
            return;
        }
        
        // 役割IDが存在するか
        $existsRoleId = $this->roles->exists($user->roleId);
        if (!$existsRoleId) {
            $this->setError('roleId', 'not_found');
        }
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
