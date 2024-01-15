<?php

namespace App\Model\Roles;

use App\Base\CustomException;
use App\Service\Roles\Commands\Create;
use App\Service\Roles\Commands\Delete;
use App\Service\Roles\Commands\Edit;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

/**
 * 役割 - 操作クラス
 */
class Command extends Query
{
    /**
     * 引数のオブジェクトをストレージへ保存する
     */
    public function save(object $dto)
    {
        return match (true) {
            $dto instanceof Create => $this->create($dto),
            $dto instanceof Edit => $this->edit($dto),
            $dto instanceof Delete => $this->delete($dto),
        };
    }

    /**
     * 作成
     * 
     * @return int 作成された役割のID
     */
    private function create(Create $dto): int
    {
        return DB::transaction(function () use ($dto) {
            // 役割テーブル
            $roleId = DB::table('roles')->insertGetId([
                'name' => $dto->name,
                'note' => $dto->note,
            ]);

            // 役割-パーミッションテーブル
            $rolePermissions = array_map(fn ($permissionId) => [
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ], $dto->permissionIds);
            DB::table('roles_permissions')->insert($rolePermissions);

            return $roleId;
        });
    }

    /**
     * 編集
     */
    private function edit(Edit $dto): void
    {
        DB::transaction(function () use ($dto) {
            // ロック取得
            $this->lockForUpdate($dto->id, $dto->updatedAt);

            // 役割テーブル
            DB::table('roles')->where('id', $dto->id)->update(array_filter([
                'name' => $dto->name,
                'note' => $dto->note,
                'updated_at' => new Expression('CURRENT_TIMESTAMP'),
            ], fn ($value) => isset($value)));

            // 役割-パーミッションテーブル
            if ($dto->permissionIds !== null) {
                $rolePermissions = array_map(fn ($permissionId) => [
                    'role_id' => $dto->id,
                    'permission_id' => $permissionId,
                ], $dto->permissionIds);
                DB::table('roles_permissions')->where('role_id', $dto->id)->delete();
                DB::table('roles_permissions')->insert($rolePermissions);
            }
        });
    }

    /**
     * 削除
     */
    private function delete(Delete $dto): void
    {
        // 役割テーブル
        DB::table('roles')
            ->whereIn('id', $dto->deleteIds)
            ->delete();

        // 役割-パーミッションテーブル
        DB::table('roles_permissions')
            ->whereIn('role_id', $dto->deleteIds)
            ->delete();
    }

    /**
     * 更新対象のロックを取得する
     * 
     * @param int $id
     * @param string|null $updatedAt
     * @return void
     */
    private function lockForUpdate(int $id, ?string $updatedAt = null): void
    {
        $row = DB::table('roles')
            ->lockForUpdate()
            ->where('id', $id)
            ->first(['updated_at']);

        if ($row === null) {
            throw new CustomException('record_not_found');
        }

        if ($updatedAt !== null && $updatedAt !== $row->updated_at) {
            throw new CustomException('conflict');
        }
    }
}
