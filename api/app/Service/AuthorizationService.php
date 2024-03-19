<?php

namespace App\Service;

use App\Storage\Roles\Query as Roles;
use App\Storage\Permissions\Query as Permissions;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * 認可サービス
 */
final class AuthorizationService
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Roles $roles,
        private Permissions $permissions,
    ) {
    }

    /**
     * 役割が指定パスへのアクセス権限を持っているか判定する
     * 
     * @param int $roleId 役割ID
     * @param string $path 判定するパス
     */
    public function authorize(int $roleId, string $path)
    {
        if ($roleId === Roles::SUPER_ROLE_ID) {
            return;
        }

        list($controller, $action) = explode('/', $path);
        $allowedPaths = $this->getBackendPaths($roleId);

        if (isset($allowedPaths[$controller]) && in_array($action ?? 'index', $allowedPaths[$controller])) {
            return;
        }

        throw new AccessDeniedHttpException();
    }

    /**
     * 役割が利用可能なバックエンドパスの一覧を取得する
     */
    public function getFrontendPaths(int $roleId): array
    {
        $permissionIds = $this->roles->getPermissions($roleId);
        return $this->permissions->getFrontendPaths($permissionIds);
    }

    /**
     * 役割が利用可能なバックエンドパスの一覧を取得する
     */
    private function getBackendPaths(int $roleId): array
    {
        $permissionIds = $this->roles->getPermissions($roleId);
        return $this->permissions->getBackendPaths($permissionIds);
    }
}
