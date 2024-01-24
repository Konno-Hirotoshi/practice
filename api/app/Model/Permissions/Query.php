<?php

namespace App\Model\Permissions;

use Illuminate\Support\Facades\DB;

/**
 * 権限 - 問い合わせクラス
 */
class Query
{
    /**
     * 権限設定
     */
    public $permissions = [
        [
            'label' => 'none',
        ],
        [
            'label' => '利用者/Read',
            'backend' => ['users' => ['index', 'show']],
            'frontend' => ['users'],
        ],
        [
            'label' => '利用者/Write',
            'backend' => ['users' => ['create', 'edit', 'delete']],
            'frontend' => [],
        ],
        [
            'label' => '権限/Read',
            'backend' => ['roles' => ['index', 'show']],
            'frontend' => ['roles'],
        ],
        [
            'label' => '権限/Write',
            'backend' => ['roles' => ['creste', 'edit', 'delete']],
            'frontend' => [],
        ],
    ];

    /**
     * 指定された役割が利用可能なバックエンドのパスの一覧を取得する
     * 
     * @param array|int $permissionIds
     * @return array
     */
    public function getFrontendPaths(array|int $permissionIds): array
    {
        return array_reduce((array)$permissionIds, function ($carry, $permissionId) {
            return array_merge_recursive($carry, $this->permissions[$permissionId]['frontend']);
        }, []);
    }

    /**
     * 指定された役割が利用可能なフロントエンドのパスの一覧を取得する
     * 
     * @param array|int $permissionIds
     * @return array
     */
    public function getBackendPaths(array|int $permissionIds): array
    {
        return array_reduce((array)$permissionIds, function ($carry, $permissionId) {
            return array_merge_recursive($carry, $this->permissions[$permissionId]['backend']);
        }, []);
    }

    /**
     * 指定IDが存在するか判定する
     * 
     * @param array|int $permissionIds
     * @return bool
     */
    public function exists(array|int $permissionIds): bool
    {
        foreach ((array)$permissionIds as $permissionId) {
            if (!isset($this->permissions[$permissionId])) {
                return false;
            }
        }
        return true;
    }
}
