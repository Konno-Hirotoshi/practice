<?php

namespace App\Controller;

use App\Domain\Users\Dto\CreateDto;
use App\Domain\Users\Dto\EditDto;
use App\Domain\Users\Service\Collection;
use App\Domain\Users\Service\UseCase;
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
        private Collection $collection,
        private UseCase $useCase,
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
        $results = $this->collection->search(...$inputData);

        // 03. Return Response
        return $results;
    }

    /**
     * 詳細情報取得
     */
    public function show(int $id)
    {
        // 01. Validate Request
        // (NOP)

        // 02. Invoke Use Case
        $result = $this->collection->get($id);

        // 03. Return Response
        return $result;
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
        ]);

        // 02. Invoke Use Case
        $userId = $this->useCase->create(new CreateDto(...$inputData));

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
        ]);
        $additionalData = $this->request->validate([
            // 最終更新日時
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]);

        // 02. Invoke Use Case
        $this->useCase
            ->target($id, ...$additionalData)
            ->edit(new EditDto(...$inputData));

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
            // 現在のパスワード
            'currentPssword' => ['required', 'min:8', 'max:100'],
        ]);
        $additionalData = $this->request->validate([
            // 最終更新日時
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]);

        // 02. Invoke Use Case
        $this->useCase
            ->target($id, ...$additionalData)
            ->editPassword(...$inputData);

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
        $this->useCase->target($id)->delete();

        // 03. Return Response
        return ['succeed' => true];
    }
}
