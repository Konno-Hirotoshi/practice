<?php

namespace App\Controller;

use App\Domain\Roles\Dto\CreateDto;
use App\Domain\Roles\Dto\EditDto;
use App\Domain\Roles\Service\Collection;
use App\Domain\Roles\Service\UseCase;
use Illuminate\Http\Request;

/**
 * 役割 - コントローラークラス
 */
class RolesController
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
        $results = $this->collection->search($inputData);

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
            // 名称
            'name' => ['required', 'string', 'max:20'],
            // 備考
            'note' => ['string', 'max:200'],
            // 選択された権限のリスト
            'permissionIds' => ['array'],
            'permissionIds.*' => ['integer'],
        ]);

        // 02. Invoke Use Case
        $roleId = $this->useCase->create(new CreateDto(...$inputData));

        // 03. Return Response
        return ['id' => $roleId];
    }

    /**
     * 編集
     */
    public function edit(int $id)
    {
        // 01. Validate Request
        $inputData = $this->request->validate([
            // 名称
            'name' => ['filled', 'string', 'max:20'],
            // 備考
            'note' => ['string', 'max:200'],
            // 選択された権限のリスト
            'permissionIds' => ['array'],
            'permissionIds.*' => ['integer'],
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
