<?php

namespace App\Controller;

use App\Service\Users\UsersService;
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
        private UsersService $usersService,
    ) {
    }

    /**
     * 検索
     */
    public function search()
    {
        // 01. Validate Request
        $validatedRequest = $this->request->validate([
            'search' => ['array'],
            'sort' => ['array'],
            'sort.*' => ['string'],
            'page' => ['integer'],
            'perPage' => ['integer', 'max:100'],
        ]);

        // 02. Invoke Use Case
        $users = $this->usersService->search(...$validatedRequest);

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
        $user = $this->usersService->get($id);

        // 03. Return Response
        return $user;
    }

    /**
     * 新規作成
     */
    public function create()
    {
        // 01. Validate Request
        $validatedRequest = $this->request->validate([
            'fullName' => ['required', 'string', 'max:30'],
            'email' => ['required', 'string', 'max:100'],
            'departmentId' => ['required', 'integer'],
            'roleId' => ['required', 'integer'],
            'password' => ['string', 'min:8', 'max:100'],
            'note' => ['string', 'max:200'],
        ]);

        // 02. Invoke Use Case
        $userId = $this->usersService->create(...$validatedRequest);

        // 03. Return Response
        return ['id' => $userId];
    }

    /**
     * 編集
     */
    public function edit(int $id)
    {
        // 01. Validate Request
        $validatedRequest = $this->request->validate([
            'fullName' => ['filled', 'string', 'max:30'],
            'email' => ['filled', 'string', 'max:100'],
            'departmentId' => ['filled', 'integer'],
            'roleId' => ['filled', 'integer'],
            'password' => ['filled', 'min:8', 'max:100'],
            'note' => ['string', 'max:200'],
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]);

        // 02. Invoke Use Case
        $this->usersService->edit($id, ...$validatedRequest);

        // 03. Return Response
        return ['status' => 'succeed'];
    }

    /**
     * 削除
     */
    public function delete(int $id)
    {
        // 01. Validate Request
        // (NOP)

        // 02. Invoke Use Case
        $this->usersService->delete($id);

        // 03. Return Response
        return ['status' => 'succeed'];
    }
}
