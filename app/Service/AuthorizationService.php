<?php

namespace App\Service;

use App\Model\Roles\Query as Roles;
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
        $serverPermissions = $this->roles->getBackendPermission($roleId);
        
        if (isset($serverPermissions[$controller]) && in_array($action, $serverPermissions[$controller])) {
            return;
        }

        throw new AccessDeniedHttpException();
    }
}
