<?php

namespace App\Domain\Users\Service;

use App\Base\BaseUseCase;
use App\Domain\Users\Dto\CreateDto;
use App\Domain\Users\Dto\EditDto;
use App\Domain\Users\Support\CreateRule;
use App\Domain\Users\Support\EditRule;
use App\Domain\Users\User;
use App\Storage\Users\Command as Users;
use App\Service\AuthenticationService;

/**
 * 利用者 - ユースケースクラス
 */
class UseCase extends BaseUseCase
{
    /**
     * コンストラクタ
     *
     * @param Users $users 役割
     * @param AuthenticationService $authenticationService 認証サービス
     * @param CreateRule $createRule 新規作成時ビジネスルール
     * @param EditRule $editRule 編集時ビジネスルール
     * @param ?int $id 利用者ID
     * @param ?string $updatedAt 最終更新日時
     */
    public function __construct(
        private Users $users,
        private AuthenticationService $authenticationService,
        private CreateRule $createRule,
        private EditRule $editRule,
        private ?int $id = null,
        private ?string $updatedAt = null,
    ) {
    }

    /**
     * 対象を指定する
     *
     * @param int $id 利用者ID
     * @param ?string $updatedAt 最終更新日時
     * @return self
     */
    public function target(int $id, ?string $updatedAt = null): self
    {
        $this->id = $id;
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * 新規作成
     *
     * @param CreateDto $dto 入力データ
     * @return int
     */
    public function create(CreateDto $dto): int
    {
        $user = User::create($dto);

        $this->createRule->validate($user);

        return $this->users->save($user, context: __METHOD__);
    }

    /**
     * 編集
     *
     * @param EditDto $dto 入力データ
     * @return void
     */
    public function edit(EditDto $dto): void
    {
        $user = $this->users
            ->getEntity($this->id, $this->updatedAt, context: __METHOD__)
            ->edit($dto);

        $this->editRule->validate($user);

        $this->users->save($user, context: __METHOD__);
    }

    /**
     * パスワード編集
     *
     * @param string $password 新しいパスワード
     * @param string $currentPassword 現在のパスワード
     * @return void
     */
    public function editPassword(string $password, string $currentPassword): void
    {
        // 現在のパスワードが正しいか
        if ($errorCode = $this->authenticationService->getErrorCode($this->id, $currentPassword)) {
            $this->setError('currentPassword', $errorCode)->throw();
        }

        $user = $this->users
            ->getEntity($this->id, $this->updatedAt, context: __METHOD__)
            ->editPassword($password);

        $this->users->save($user, context: __METHOD__);
    }

    /**
     * 削除
     *
     * @return void
     */
    public function delete(): void
    {
        $user = $this->users
            ->getEntity($this->id, $this->updatedAt, context: __METHOD__)
            ->delete();

        $this->users->save($user, context: __METHOD__);
    }
}
