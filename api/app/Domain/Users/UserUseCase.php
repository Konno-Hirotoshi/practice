<?php

namespace App\Domain\Users;

use App\Base\BaseUseCase;
use App\Domain\Users\Support\CreateRule;
use App\Domain\Users\Support\EditRule;
use App\Storage\Users\Command as Users;
use App\Service\AuthenticationService;

/**
 * 利用者 - ユースケースクラス
 */
class UserUseCase extends BaseUseCase
{
    /**
     * コンストラクタ
     *
     * @param Users $users 役割
     * @param AuthenticationService $authenticationService 認証サービス
     * @param CreateRule $createRule 新規作成時ビジネスルール
     * @param EditRule $editRule 編集時ビジネスルール
     */
    public function __construct(
        private Users $users,
        private AuthenticationService $authenticationService,
        private CreateRule $createRule,
        private EditRule $editRule,
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

        $this->createRule->validate($user);

        return $this->users->save(user: $user, context: __METHOD__);
    }

    /**
     * 編集
     *
     * @param int $id 利用者ID
     * @param array $inputData 入力データ
     * @return void
     */
    public function edit($id, array $inputData): void
    {
        $user = $this->users
            ->getEntity($id, $inputData['updated_at'] ?? null, context: __METHOD__)
            ->edit($inputData);

        $this->editRule->validate($user);

        $this->users->save($user, context: __METHOD__);
    }

    /**
     * パスワード編集
     *
     * @param int $id 利用者ID
     * @param string $password 新しいパスワード
     * @param string $currentPassword 現在のパスワード
     * @param string $updatedAt 最終更新日時
     * @return void
     */
    public function editPassword($id, string $password, string $currentPassword, $updatedAt = null): void
    {
        $user = $this->users
            ->getEntity($id, $updatedAt, context: __METHOD__)
            ->editPassword($password);

        // 現在のパスワードが正しいか
        if ($errorCode = $this->authenticationService->getErrorCode($id, $currentPassword)) {
            $this->setError('currentPassword', $errorCode);
        }

        $this->throwIfErrors();

        $this->users->save($user, context: __METHOD__);
    }

    /**
     * 削除
     *
     * @param int $id 利用者ID
     * @param string $updatedAt 最終更新日時
     * @return void
     */
    public function delete(int $id, $updatedAt = null)
    {
        $user = $this->users
            ->getEntity($id, $updatedAt, context: __METHOD__)
            ->delete();

        $this->users->save($user, context: __METHOD__);
    }
}
