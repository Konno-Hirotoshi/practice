<?php

namespace App\Controller;

use App\Domain\Users\UserCollection;
use App\Domain\Users\UserUseCase;
use Illuminate\Http\Request;

/**
 * 利用者 - コントローラークラス
 */
class UsersController
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Request $request,
        private UserCollection $userCollection,
        private UserUseCase $useCase,
    ) {
    }

    /**
     * 検索
     */
    public function search()
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            'search' => ['array'],
            'sort' => ['array'],
            'sort.*' => ['string'],
            'page' => ['integer'],
            'perPage' => ['integer', 'max:100'],
        ]);

        // 02. Invoke Use Case
        $users = $this->userCollection->search(...$inputData);

        // 03. Return Response
        return $users;
    }

    /**
     * 詳細情報取得
     */
    public function show(int $id)
    {
        // 01. Validate Request
        // (NOP)

        // 02. Invoke Use Case
        $user = $this->userCollection->get($id);

        // 03. Return Response
        return $user;
    }

    /**
     * 新規作成
     */
    public function create()
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            // 氏名
            'fullName' => ['required', 'string', 'max:30'],
            // メールアドレス
            'email' => ['required', 'string', 'max:100'],
            // 部署ID
            'departmentId' => ['required', 'integer'],
            // 役割ID
            'roleId' => ['required', 'integer'],
            // パスワード
            'password' => ['required', 'min:8', 'max:100'],
            // 備考
            'note' => ['string', 'max:200'],
        ]) + [
            // パスワード デフォルト値
            'password' => 'Default@1234',
            // 備考 デフォルト値
            'note' => '',
        ];

        // 02. Invoke Use Case
        $userId = $this->useCase->create($inputData);

        // 03. Return Response
        return ['id' => $userId];
    }

    /**
     * 編集
     */
    public function edit(int $id)
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            // 氏名
            'fullName' => ['filled', 'string', 'max:30'],
            // メールアドレス
            'email' => ['filled', 'string', 'max:100'],
            // 部署ID
            'departmentId' => ['filled', 'integer'],
            // 役割ID
            'roleId' => ['filled', 'integer'],
            // パスワード
            'password' => ['filled', 'min:8', 'max:100'],
            // 備考
            'note' => ['string', 'max:200'],
            // 最終更新日時
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]);

        // 02. Invoke Use Case
        $this->useCase->edit($id, $inputData);

        // 03. Return Response
        return ['succeed' => true];
    }

    /**
     * パスワード編集
     */
    public function editPassword(int $id)
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            // パスワード
            'password' => ['required', 'min:8', 'max:100'],
        ]);
        $additionalData = $this->request->validate([
            // 現在のパスワード
            'currentPssword' => ['required', 'min:8', 'max:100'],
            // 新しいパスワード（再入力）
            'retypePassword' => ['required', 'min:8', 'max:100'],
        ]);

        // 02. Invoke Use Case
        $this->useCase->editPassword(
            $id,
            ...$inputData,
            ...$additionalData,
        );

        // 03. Return Response
        return ['succeed' => true];
    }

    /**
     * 削除
     */
    public function delete(int $id)
    {
        // 01. Validate Request
        // (NOP)

        // 02. Invoke Use Case
        $this->useCase->delete($id);

        // 03. Return Response
        return ['succeed' => true];
    }
}
