<?php

namespace App\Storage\Sessions;

use Illuminate\Support\Facades\DB;

/**
 * セッション - 操作クラス
 */
class Command extends Query
{
    /**
     * セッションを作成する
     * 
     * @param string $key セッションキー
     * @param int $userId 利用者ID
     * @param int $departmentId 部署ID
     * @param int $roleId 役割ID
     * @param int $expiredAt 有効期限
     * @return void
     */
    public function create(string $key, int $userId, int $departmentId, int $roleId, int $expiredAt): void
    {
        DB::table('sessions')
            ->insert([
                'key' => $key,
                'user_id' => $userId,
                'department_id' => $departmentId,
                'role_id' => $roleId,
                'expired_at' => date('Y-m-d H:i:s', $expiredAt),
            ]);
    }

    /**
     * セッションの有効期限を更新する
     * 
     * @param string $key セッションキー
     * @param int $expiredAt 有効期限
     * @return void
     */
    public function updateExpiredAt(string $key, int $expiredAt): void
    {
        DB::table('sessions')
            ->where('key', $key)
            ->update([
                'expired_at' => date('Y-m-d H:i:s', $expiredAt),
            ]);
    }

    /**
     * セッションを削除する
     * 
     * 【対象条件】
     *   ・$keyで指定されたセッション
     *   ・または、有効期限(設定値: 30分)以上未更新のセッション
     * 
     * @param string $key セッションキー
     * @param ?int $basisTime 基準日時
     * @return void
     */
    public function delete(string $key, int $basisTime = null): void
    {
        DB::table('sessions')
            ->where(['key' => $key])
            ->orWhere('expired_at', '<=', date('Y-m-d H:i:s', $basisTime))
            ->delete();
    }
}
