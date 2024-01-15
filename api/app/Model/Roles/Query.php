<?php

namespace App\Model\Roles;

use App\Base\CustomException;
use App\Base\SearchOption;
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
     * 役割に割り当てられたバックエンドパーミッションを取得する
     * 
     * @param int $id
     * @return array
     */
    public function getBackendPermission(int $id): array
    {
        $permissions = DB::table('roles_permissions as rp')
            ->join('permissions as p', 'p.id', '=', 'rp.permission_id')
            ->where('role_id', $id)
            ->get(['p.backend']);

        return $permissions->reduce(function ($carry, $permission) {
            return array_merge_recursive($carry, json_decode($permission->backend, true));
        }, []);
    }

    /**
     * 役割に割り当てられたフロントエンドパーミッションを取得する
     * 
     * @param int $id
     * @return array
     */
    public function getFrontendPermission(int $id): array
    {
        $permissions = DB::table('roles_permissions as rp')
            ->join('permissions as p', 'p.id', '=', 'rp.permission_id')
            ->where('role_id', $id)
            ->get(['p.frontend']);

        return $permissions->reduce(function ($carry, $permission) {
            return array_merge_recursive($carry, json_decode($permission->frontend, true));
        }, []);
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
}
