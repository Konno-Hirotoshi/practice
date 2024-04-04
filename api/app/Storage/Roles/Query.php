<?php

namespace App\Storage\Roles;

use App\Base\CustomException;
use App\Base\SearchOption;
use App\Domain\Roles\Role;
use App\Domain\Roles\UseCase\Delete;
use App\Domain\Roles\UseCase\Edit;
use Illuminate\Support\Facades\DB;

/**
 * 役割 - 問い合わせクラス
 */
class Query
{
    /**
     * 全権限ロールID
     */
    public const SUPER_ROLE_ID = 1;

    /**
     * 検索する
     *
     * @param SearchOption $option
     * @return array
     */
    public function search(SearchOption $option): array
    {
        $results = DB::table('roles')
            ->select([
                'id',
                'name',
                'note',
            ])
            ->exSearch($option);

        return $results;
    }

    /**
     * 1件取得する
     * 
     * @param int $id
     * @return object
     */
    public function get(int $id): object
    {
        $role = DB::table('roles')
            ->where('id', $id)
            ->first([
                'name',
                'note',
                'updated_at',
            ]);

        if ($role === null) {
            throw new CustomException('record_not_found');
        }

        $permissions = DB::table('roles_permissions')
            ->where('role_id', $id)
            ->get([
                'permission_id',
            ]);

        $role->permissionIds = $permissions->pluck('permission_id');

        return $role;
    }

    /**
     * 役割に割り当てられた権限を取得する
     * 
     * @param int $id
     * @return array
     */
    public function getPermissions(int $id): array
    {
        $permissions = DB::table('roles_permissions')
            ->where('role_id', $id)
            ->get(['permission_id']);

        return $permissions->pluck('permission_id')->toArray();
    }

    /**
     * エンティティを取得する
     *
     * @param int $id 役割ID
     * @return Role
     */
    public function getEntity(int $id, ?string $updatedAt = null, ?string $context = null): Role
    {
        // contextに応じたカラムのみ取得する
        $dto = DB::table('roles')
            ->where('id', $id)
            ->first(match ($context) {
                Edit::class => ['id', 'name', 'note', 'updated_at'],
                Delete::class => ['id', 'updated_at'],
            });

        // レコードが存在しなければエラーとする
        if ($dto === null) {
            throw new CustomException('record_not_found');
        }

        // 最終更新日時に差異があればエラーとする
        if ($updatedAt !== null && $updatedAt !== $dto->updated_at) {
            throw new CustomException('conflict');
        }

        return new Role($this->convert($dto));
    }

    /**
     * 役割が存在するか
     * 
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        $count = DB::table('roles')
            ->where('id', $id)
            ->count();
        return $count > 0;
    }

    /**
     * 同名称の役割が存在するか
     * 
     * @param string $name
     * @return bool
     */
    public function existsName(string $name): bool
    {
        $sameNameCount = DB::table('roles')
            ->where('name', $name)
            ->count();
        return $sameNameCount > 0;
    }

    /**
     * 同名称の役割が存在するか (更新時)
     * 
     * @param string $name
     * @param int $selfId
     * @return bool
     */
    public function existsNameOnUpdate(string $name, int $selfId): bool
    {
        $sameNameCount = DB::table('roles')
            ->where('name', $name)
            ->whereNot('id', $selfId)
            ->count();
        return $sameNameCount > 0;
    }

    /**
     * 取得データをエンティティのコンストラクタの入力形式に変換する
     *
     * @param object $dto　取得データDTO
     * @return array
     */
    private function convert(object $dto)
    {
        $mapping = [
            'id' => 'id',
            'name' => 'name',
            'note' => 'note',
            'updated_at' => 'updatedAt',
        ];

        $inputData = [];
        foreach ((array)$dto as $key => $value) {
            $inputData[$mapping[$key]] = $value;
        }

        return $inputData;
    }
}
