<?php

namespace App\Model\Sessions;

use Illuminate\Support\Facades\DB;

/**
 * セッション - 操作クラス
 */
class Command extends Query
{
    /**
     * セッションの有効期限 (秒)
     */
    public const SESSION_LIFETIME = 1800;

    /**
     * セッションを作成する
     * 
     * Note: 主キー重複時に例外を投げる可能性有り (確率は極めて低いので再試行などの対策はしない)
     * 
     * @param string $key
     * @param int $employeeId
     * @param int $departmentId
     * @param int $roleId
     * @param int $expiredAt
     * @return void
     */
    public function create(string $key, int $employeeId, int $departmentId, int $roleId, int $expiredAt): void
    {
        DB::table('sessions')
            ->insert([
                'key' => $key,
                'employee_id' => $employeeId,
                'department_id' => $departmentId,
                'role_id' => $roleId,
                'expired_at' => date('Y-m-d H:i:s', $expiredAt),
            ]);
    }

    /**
     * セッションの有効期限を更新する
     * 
     * @param string $key
     * @param int $expiredAt
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
     * @param string $key
     * @param int $now
     * @return void
     */
    public function delete(string $key, int $now = null): void
    {
        DB::table('sessions')
            ->where(['key' => $key])
            ->orWhere('expired_at', '<=', date('Y-m-d H:i:s', $now))
            ->delete();
    }
}
