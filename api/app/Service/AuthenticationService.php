<?php

namespace App\Service;

use App\Base\CustomException;
use App\Storage\LoginHistories\Command as LoginHistories;
use App\Storage\Users\Query as Users;

/**
 * 認証サービス
 */
final class AuthenticationService
{
    /**
     * 認証失敗を許容する回数
     */
    private const RATE_LIMIT = 5;

    /**
     * 認証失敗をカウントする時間 (秒)
     */
    private const RATE_TIME = 1800;

    /**
     * コンストラクタ
     */
    public function __construct(
        private Users $users,
        private LoginHistories $loginHistories,
    ) {
    }

    /**
     * 利用者パスワード認証
     * 
     * @param string $email メールアドレス
     * @param string $password パスワード
     * @return object
     */
    public function authenticate(string $email, string $password): object
    {
        if ($email === '' || $password === '') {
            throw new CustomException('empty');
        }

        // 連続認証失敗チェック
        $isLocked = $this->isLocked($email);
        if ($isLocked) {
            $this->loginHistories->createDeny($email);
            throw new CustomException('locked');
        }

        // パスワードチェック
        $user = $this->users->getForAuthoricate($email);
        if ($user === null || !password_verify($password, $user->password)) {
            $this->loginHistories->createBlock($email);
            throw new CustomException('failure');
        }

        // ログイン履歴を追加する
        $this->loginHistories->createPass($email);

        return $user;
    }

    /**
     * 利用者パスワード認証を行い、認証に失敗した場合はエラーコードを返却する
     * 
     * @param int $userId 利用者ID
     * @param string $password パスワード
     * @return ?string
     */
    public function getErrorCode(int $userId, string $password): ?string
    {
        try {
            $email = $this->users->getEmailById($userId);
            $this->authenticate($email, $password);
            return null;
        } catch (CustomException $e) {
            return match ($e->errors()['reason']) {
                'record_not_found' => 'not_equal',
                'empty' => 'not_equal',
                'locked' => 'locked',
                'failure' => 'not_equal',
            };
        }
    }
    /**
     * 指定されたメールアドレスがロックされているかどうか
     * 
     * @param string $email
     * @return bool
     */
    private function isLocked(string $email): bool
    {
        $blockCount = $this->loginHistories->getRecentBlockCount(
            email: $email,
            period: self::RATE_TIME,
        );
        return $blockCount > self::RATE_LIMIT;
    }
}
