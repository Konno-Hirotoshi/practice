<?php

namespace App\Model\Users;

use App\Base\CustomException;
use App\Base\SearchOption;
use Illuminate\Support\Facades\DB;

/**
 * 利用者 - 問い合わせクラス
 */
class Query
{
    /**
     * 検索する
     *
     * @param SearchOption $option
     * @return array
     */
    public function search(SearchOption $option): array
    {
        $results = DB::table('users')
            ->select([
                'id',
                'email',
                'role_id',
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
        $user = DB::table('users')
            ->where('id', $id)
            ->first([
                'email',
                'role_id',
                'note',
                'updated_at',
            ]);

        if ($user === null) {
            throw new CustomException('record_not_found');
        }

        return $user;
    }

    /**
     * 認証用にユーザー情報を取得する
     * 
     * @param string $email
     * @return ?object
     */
    public function getForAuthoricate(string $email): ?object
    {
        $user = DB::table('users')
            ->where('email', $email)
            ->first([
                'id',
                'role_id',
                'password',
            ]);
        return $user;
    }

    /**
     * メールアドレスを取得する
     * 
     * @param int $id
     * @return ?string
     */
    public function getEmailById(int $id): ?string
    {
        $email = DB::table('users')
            ->where('id', $id)
            ->first('email');
        return $email;
    }

    /**
     * 同メールアドレスの利用者が存在するか
     * 
     * @param string $email
     * @return bool
     */
    public function existsEmail(string $email): bool
    {
        $sameEmailCount = DB::table('users')
            ->where('email', $email)
            ->count();
        return $sameEmailCount > 0;
    }

    /**
     * 同名称の利用者が存在するか
     * 
     * @param string $email
     * @param int $selfId
     * @return bool
     */
    public function existsEmailOnUpdate(string $email, int $selfId): bool
    {
        $sameEmailCount = DB::table('users')
            ->where('email', $email)
            ->whereNot('id', $selfId)
            ->count();
        return $sameEmailCount > 0;
    }

    /**
     * 指定した役割IDの利用者数
     * 
     * @param int|array $roleIds
     * @return int
     */
    public function getCountByRoleId(int|array $roleIds): int
    {
        $count = DB::table('users')
            ->whereIn('role_id', $roleIds)
            ->count();
        return $count;
    }
}
