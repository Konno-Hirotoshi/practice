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
     * 指定された役割が指定パスへのアクセス権限を持っているか判定する
     * 
     * @param int $roleId
     * @param string $path
     */
    public function authorize(int $roleId, string $path)
    {
        if ($roleId === Roles::SUPER_ROLE_ID) {
            return;
        }

        $path = explode('/', $path);
        $controller = $path[0];
        $action = $path[1] ?? 'index';
        $allowedPaths = $this->getBackendPaths($roleId);

        if (isset($allowedPaths[$controller]) && in_array($action, $allowedPaths[$controller])) {
            return;
        }

        throw new AccessDeniedHttpException();
    }

    /**
     * 指定された役割が利用可能なバックエンドのパスの一覧を取得する
     */
    public function getFrontendPaths(int $roleId): array
    {
        $permissionIds = $this->roles->getPermissions($roleId);
        return $this->permissions->getFrontendPaths($permissionIds);
    }

    /**
     * 指定された役割が利用可能なフロントエンドのパスの一覧を取得する
     */
    private function getBackendPaths(int $roleId): array
    {
        $permissionIds = $this->roles->getPermissions($roleId);
        return $this->permissions->getBackendPaths($permissionIds);
    }
}
