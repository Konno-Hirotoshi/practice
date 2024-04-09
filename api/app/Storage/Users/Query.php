<?php

namespace App\Storage\Users;

use App\Base\CustomException;
use App\Base\SearchOption;
use App\Domain\Users\UseCase\Delete;
use App\Domain\Users\UseCase\Edit;
use App\Domain\Users\UseCase\EditPassword;
use App\Domain\Users\User;
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
                'full_name',
                'email',
                'department_id',
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
                'full_name',
                'email',
                'department_id',
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
                'department_id',
                'role_id',
                'password',
            ]);
        return $user;
    }

    /**
     * メールアドレスを取得する
     * 
     * @param int $id
     * @return string
     */
    public function getEmailById(int $id): string
    {
        $user = DB::table('users')
            ->where('id', $id)
            ->first('email');

        if ($user === null) {
            throw new CustomException('record_not_found');
        }

        return $user->email;
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
            ->whereIn('role_id', (array)$roleIds)
            ->count();
        return $count;
    }
    /**
     * エンティティを取得する
     *
     * @param int $id 利用者ID
     * @return User
     */
    public function getEntity(int $id, ?string $updatedAt = null, ?string $context = null): User
    {
        // contextに応じたカラムのみ取得する
        $dto = DB::table('roles')
            ->where('id', $id)
            ->first(match ($context) {
                'edit' => ['id', 'updated_at'],
                'editPassword' => ['id', 'password', 'updated_at'],
                'delete' => ['id', 'updated_at'],
            });

        // レコードが存在しなければエラーとする
        if ($dto === null) {
            throw new CustomException('record_not_found');
        }

        // 最終更新日時に差異があればエラーとする
        if ($updatedAt !== null && $updatedAt !== $dto->updated_at) {
            throw new CustomException('conflict');
        }

        return new User($this->convert($dto));
    }

    /**
     * 取得データをエンティティのコンストラクタの入力形式に変換する
     *
     * @param object $dto　取得データDTO
     * @return array
     */
    private function convert(object $dto)
    {
        $mapping = [
            'id' => 'id',
            'full_name' => 'fullName',
            'email' => 'email',
            'department_id' => 'departmentId',
            'password' => 'password',
            'note' => 'note',
            'updated_at' => 'updatedAt',
        ];

        $inputData = [];
        foreach ((array)$dto as $key => $value) {
            $inputData[$mapping[$key]] = $value;
        }

        return $inputData;
    }
}
