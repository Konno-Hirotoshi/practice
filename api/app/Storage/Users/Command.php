<?php

namespace App\Storage\Users;

use App\Base\CustomException;
use App\Domain\Users\User;
use App\Domain\Users\Interface\Storage;
use App\Domain\Users\Validator\Create;
use App\Domain\Users\Validator\Delete;
use App\Domain\Users\Validator\Edit;
use App\Domain\Users\Validator\EditPassword;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

/**
 * 利用者 - 操作クラス
 */
class Command extends Query implements Storage
{
    /**
     * 引数のオブジェクトをストレージへ保存する
     */
    public function save(User $user, string $context)
    {
        return match ($context) {
            Create::class => $this->create($user),
            Edit::class => $this->edit($user),
            EditPassword::class => $this->editPassword($user),
            Delete::class => $this->delete($user),
        };
    }

    /**
     * 作成
     * 
     * @return int 作成された利用者のID
     */
    private function create(User $user): int
    {
        return DB::transaction(function () use ($user) {
            // 利用者テーブル
            $userId = DB::table('users')->insertGetId([
                'full_name' => $user->fullName,
                'email' => $user->email,
                'department_id' => $user->departmentId,
                'role_id' => $user->roleId,
                'password' => password_hash($user->password, PASSWORD_BCRYPT),
                'note' => $user->note,
            ]);
            return $userId;
        });
    }

    /**
     * 編集
     * 
     * @return void
     */
    private function edit(User $user): void
    {
        DB::transaction(function () use ($user) {
            // ロック取得
            $this->lockForUpdate($user);

            // 利用者テーブル
            DB::table('users')->where('id', $user->id)->update(array_filter([
                'full_name' => $user->fullName ?? null,
                'email' => $user->email ?? null,
                'department_id' => $user->departmentId ?? null,
                'role_id' => $user->roleId ?? null,
                'password' => isset($user->password) ? password_hash($user->password, PASSWORD_BCRYPT) : null,
                'note' => $user->note ?? null,
                'updated_at' => new Expression('CURRENT_TIMESTAMP'),
            ], fn ($value) => isset($value)));
        });
    }

    /**
     * パスワード編集
     * 
     * @return void
     */
    private function editPassword(User $user): void
    {
        DB::transaction(function () use ($user) {
            // 利用者テーブル
            DB::table('users')->where('id', $user->id)->update([
                'password' => password_hash($user->password, PASSWORD_BCRYPT),
                'updated_at' => new Expression('CURRENT_TIMESTAMP'),
            ]);
        });
    }

    /**
     * 削除
     * 
     * @return void
     */
    private function delete(User $user): void
    {
        // 利用者テーブル
        DB::table('users')
            ->where('id', $user->id)
            ->delete();
    }

    /**
     * 更新対象のロックを取得する
     * 
     * @param User $user
     * @return void
     */
    private function lockForUpdate(User $user): void
    {
        $row = DB::table('users')
            ->lockForUpdate()
            ->where('id', $user->id)
            ->first(['updated_at']);

        if ($row === null) {
            throw new CustomException('record_not_found');
        }

        if (isset($user->updatedAt) && $user->updatedAt !== $row->updated_at) {
            throw new CustomException('conflict');
        }
    }
}
