<?php

namespace App\Storage\Roles;

use App\Base\CustomException;
use App\Domain\Roles\Interface\Storage;
use App\Domain\Roles\Role;
use App\Domain\Roles\Validator\Create;
use App\Domain\Roles\Validator\Delete;
use App\Domain\Roles\Validator\Edit;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

/**
 * 役割 - 操作クラス
 */
class Command extends Query implements Storage
{
    /**
     * 引数のオブジェクトをストレージへ保存する
     */
    public function save(Role $role, string $context)
    {
        return match ($context) {
            Create::class => $this->create($role),
            Edit::class => $this->edit($role),
            Delete::class => $this->delete($role),
        };
    }

    /**
     * 作成
     * 
     * @return int 作成された役割のID
     */
    private function create(Role $role): int
    {
        return DB::transaction(function () use ($role) {
            // 役割テーブル
            $roleId = DB::table('roles')->insertGetId([
                'name' => $role->name,
                'note' => $role->note,
            ]);

            // 役割-パーミッションテーブル
            $rolePermissions = array_map(fn ($permissionId) => [
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ], $role->permissionIds);
            DB::table('roles_permissions')->insert($rolePermissions);

            return $roleId;
        });
    }

    /**
     * 編集
     */
    private function edit(Role $role): void
    {
        DB::transaction(function () use ($role) {
            // ロック取得
            $this->lockForUpdate($role);

            // 役割テーブル
            DB::table('roles')->where('id', $role->id)->update(array_filter([
                'name' => $role->name ?? null,
                'note' => $role->note ?? null,
                'updated_at' => new Expression('CURRENT_TIMESTAMP'),
            ], fn ($value) => isset($value)));

            // 役割-パーミッションテーブル
            if ($role->permissionIds !== null) {
                $rolePermissions = array_map(fn ($permissionId) => [
                    'role_id' => $role->id,
                    'permission_id' => $permissionId,
                ], $role->permissionIds);
                DB::table('roles_permissions')->where('role_id', $role->id)->delete();
                DB::table('roles_permissions')->insert($rolePermissions);
            }
        });
    }

    /**
     * 削除
     */
    private function delete(Role $role): void
    {
        // 役割テーブル
        DB::table('roles')
            ->where('id', $role->id)
            ->delete();

        // 役割-パーミッションテーブル
        DB::table('roles_permissions')
            ->where('role_id', $role->id)
            ->delete();
    }

    /**
     * 更新対象のロックを取得する
     * 
     * @param Role $role
     * @return void
     */
    private function lockForUpdate(Role $role): void
    {
        $row = DB::table('roles')
            ->lockForUpdate()
            ->where('id', $role->id)
            ->first(['updated_at']);

        if ($row === null) {
            throw new CustomException('record_not_found');
        }

        if (isset($role->updatedAt) && $role->updatedAt !== $row->updated_at) {
            throw new CustomException('conflict');
        }
    }
}
