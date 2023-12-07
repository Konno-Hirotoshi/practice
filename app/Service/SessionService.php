<?php

namespace App\Service;

use App\Model\Sessions\Command as Sessions;

/**
 * セッションサービス
 */
class SessionService
{
    /**
     * セッションの有効期限 (秒)
     */
    public const SESSION_LIFETIME = 1800;

    /**
     * コンストラクタ
     * 
     * @param Sessions $sessions
     */
    public function __construct(
        private Sessions $sessions,
    ) {
    }

    /**
     * セッションを作成する
     * 
     * @param int $userId
     * @param int $roleId
     * @return string
     */
    public function create(int $userId, int $roleId): string
    {
        $sessionKey = session_create_id();
        $this->sessions->create(
            key: $sessionKey,
            userId: $userId,
            roleId: $roleId,
            expiredAt: $this->getExpiredAt(),
        );
        return $sessionKey;
    }

    /**
     * セッションを復元する
     * 
     * ・有効期限内のセッションに限り復元する
     * ・復元したセッションの有効期限を最新化する
     * 
     * @param string|null $key
     * @return object|null
     */
    public function restore(?string $sessionKey): ?object
    {
        if ($sessionKey === null) {
            return null;
        }

        // セッションを取得する
        $session = $this->sessions->get($sessionKey);
        if ($session === null) {
            return null;
        }

        // セッション有効期限をチェックする
        $session->expired_at = strtotime($session->expired_at);
        if ($session->expired_at < $this->getNow()) {
            return null;
        }

        // セッション有効期限を更新する
        // (performance: 有効期限の差が60秒未満の場合、ディスクIOを減らすために書き込みをスキップする)
        if ($this->getExpiredAt() - $session->expired_at >=  60) {
            $this->sessions->updateExpiredAt(
                key: $sessionKey,
                expiredAt: $this->getExpiredAt(),
            );
        }

        return $session;
    }

    /**
     * セッションを削除する
     * 
     * ・指定されたセッションを削除する
     * ・有効期限(設定値: 30分)を過ぎているセッションも一緒に削除する
     * 
     * @param ?string $sessionKey
     * @return self
     */
    public function delete(?string $sessionKey): self
    {
        if ($sessionKey === null) {
            return $this;
        }
        $this->sessions->delete(
            key: $sessionKey,
            now: $this->getNow(),
        );

        return $this;
    }

    /**
     * 現在日時を取得する
     * 
     * @return int
     */
    private function getNow(): int
    {
        return time();
    }

    /**
     * 有効期限を取得する
     * 
     * @return int
     */
    private function getExpiredAt(): int
    {
        return time() + self::SESSION_LIFETIME;
    }
}
