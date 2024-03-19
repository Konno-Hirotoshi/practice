<?php

namespace App\Controller;

use App\Domain\Roles\Role;
use App\Domain\Roles\RoleCollection;
use App\Domain\Roles\Validator\Create;
use App\Domain\Roles\Validator\Delete;
use App\Domain\Roles\Validator\Edit;
use App\Storage\Roles\Command;
use Illuminate\Http\Request;

/**
 * 役割 - コントローラークラス
 */
class RolesController
{
    /**
     * コンストラクタ
     *
     */
    public function __construct(
        private Request $request,
        private RoleCollection $roleCollection,
        private Command $roles,
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
        $roles = $this->roleCollection->search($inputData);

        // 03. Return Response
        return $roles;
    }

    /**
     * 詳細情報取得
     */
    public function show(int $id)
    {
        // 01. Validate Request
        // (NOP)

        // 02. Invoke Use Case
        $role = $this->roleCollection->get($id);

        // 03. Return Response
        return $role;
    }

    /**
     * 新規作成
     */
    public function create(Create $create)
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
        ]) + [
            // 備考 デフォルト値
            'note' => '',
        ];

        // 02. Invoke Use Case
        $role = new Role($inputData);
        $roleId = $role->save(
            validator: $create,
            storage: $this->roles,
        );

        // 03. Return Response
        return ['id' => $roleId];
    }

    /**
     * 編集
     */
    public function edit(int $id, Edit $edit)
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
            // 最終更新日時
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]) + [
            'id' => $id,
        ];

        // 02. Invoke Use Case
        $role = new Role($inputData);
        $role->save(
            validator: $edit,
            storage: $this->roles,
        );

        // 03. Return Response
        return ['succeed' => true];
    }

    /**
     * 削除
     */
    public function delete(int $id, Delete $delete)
    {
        // 01. Validate Request
        $inputData = ['id' => $id];

        // 02. Invoke Use Case
        $role = new Role($inputData);
        $role->save(
            validator: $delete,
            storage: $this->roles,
        );

        // 03. Return Response
        return ['succeed' => true];
    }
}
