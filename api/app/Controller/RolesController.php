<?php

namespace App\Controller;

use App\Domain\Roles\Role;
use App\Domain\Roles\UseCase\Create;
use App\Domain\Roles\UseCase\Delete;
use App\Domain\Roles\UseCase\Edit;
use App\Service\RolesService;
use App\Storage\Roles\Command;
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
        private RolesService $rolesService,
        private Command $roles,
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
        $roles = $this->rolesService->search(...$validatedRequest);

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
        $role = $this->rolesService->get($id);

        // 03. Return Response
        return $role;
    }

    /**
     * 新規作成
     */
    public function create(Create $create)
    {
        // 01. Validate Request
        $validatedRequest = $this->request->validate([
            'name' => ['required', 'string', 'max:20'],
            'note' => ['string', 'max:200'],
            'permissionIds' => ['array'],
            'permissionIds.*' => ['integer'],
        ]);

        // 02. Invoke Use Case
        $role = new Role(...$validatedRequest);
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
        $validatedRequest = $this->request->validate([
            'name' => ['filled', 'string', 'max:20'],
            'note' => ['string', 'max:200'],
            'permissionIds' => ['array'],
            'permissionIds.*' => ['integer'],
            'updatedAt' => ['string', 'min:19', 'max:19'],
        ]);

        // 02. Invoke Use Case
        $role = new Role(...$validatedRequest, id: $id);
        $roleId = $role->save(
            validator: $edit,
            storage: $this->roles,
        );

        // 03. Return Response
        return ['status' => 'succeed'];
    }

    /**
     * 削除
     */
    public function delete(int $id, Delete $delete)
    {
        // 01. Validate Request
        // (NOP)

        // 02. Invoke Use Case
        $role = new Role(id: $id);
        $roleId = $role->save(
            validator: $delete,
            storage: $this->roles,
        );

        // 03. Return Response
        return ['status' => 'succeed'];
    }
}
