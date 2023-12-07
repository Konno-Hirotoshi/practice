<?php

namespace App\Controller;

use App\Model\Roles\Query as Roles;
use App\Service\AuthenticationService;
use App\Service\SessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * セッション - コントローラークラス
 */
class SessionsController
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Request $request,
        private Roles $roles,
        private AuthenticationService $authenticationService,
        private SessionService $sessionService,
    ) {
        $sessionKey = $this->request->cookie('key');
        $this->sessionService->delete($sessionKey);
    }

    /**
     * ログイン処理
     * メールアドレスとパスワードから利用者認証を行い、セッションキーを生成する
     */
    public function login()
    {
        // 01. Validate Request
        $validatedRequest = $this->request->validate([
            'email' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:100'],
        ]);

        // 02. Invoke Use Case
        $user = $this->authenticationService->authenticate(...$validatedRequest);

        // 03. Return Response
        $body = [
            'user_id' => $user->id,
            'permissions' => $this->roles->getFrontendPermission($user->role_id),
        ];

        $sessionKey = $this->sessionService->create(
            userId: $user->id,
            roleId: $user->role_id,
        );

        return (new JsonResponse($body))->withCookie('key', $sessionKey);
    }

    /**
     * ログアウト処理
     */
    public function logout()
    {
        return (new JsonResponse(['message' => 'succeed']))->withoutCookie('key');
    }
}
