<?php

namespace App\Model\Users;

use App\Base\CustomException;
use App\Service\Users\Commands\Create;
use App\Service\Users\Commands\Delete;
use App\Service\Users\Commands\Edit;
use App\Service\Users\Commands\EditPassword;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

/**
 * 利用者 - 操作クラス
 */
class Command extends Query
{
    public function save(object $dto)
    {
        return match (true) {
            $dto instanceof Create => $this->Create($dto),
            $dto instanceof Edit => $this->Edit($dto),
            $dto instanceof EditPassword => $this->editPassword($dto),
            $dto instanceof Delete => $this->Delete($dto),
        };
    }

    /**
     * 作成
     * 
     * @return int 作成された利用者のID
     */
    private function create(Create $dto): int
    {
        return DB::transaction(function () use ($dto) {
            // 利用者テーブル
            $userId = DB::table('users')->insertGetId([
                'email' => $dto->email,
                'password' => password_hash($dto->password, PASSWORD_BCRYPT),
                'role_id' => $dto->roleId,
                'note' => $dto->note,
            ]);
            return $userId;
        });
    }

    /**
     * 編集
     * 
     * @return void
     */
    private function edit(Edit $dto): void
    {
        DB::transaction(function () use ($dto) {
            // ロック取得
            $this->lockForUpdate($dto->id, $dto->updatedAt);

            // 利用者テーブル
            DB::table('users')->where('id', $dto->id)->update(array_filter([
                'email' => $dto->email,
                'password' => $dto->password ? password_hash($dto->password, PASSWORD_BCRYPT) : null,
                'role_id' => $dto->roleId,
                'note' => $dto->note,
                'updated_at' => new Expression('CURRENT_TIMESTAMP'),
            ], fn ($value) => isset($value)));
        });
    }

    /**
     * パスワード編集
     * 
     * @return void
     */
    private function editPassword(EditPassword $dto): void
    {
        DB::transaction(function () use ($dto) {
            // 利用者テーブル
            DB::table('users')->where('id', $dto->id)->update([
                'password' => password_hash($dto->password, PASSWORD_BCRYPT),
                'updated_at' => new Expression('CURRENT_TIMESTAMP'),
            ]);
        });
    }

    /**
     * 削除
     * 
     * @return void
     */
    private function delete(Delete $dto): void
    {
        // 利用者テーブル
        DB::table('users')
            ->where('id', $dto->deleteIds)
            ->delete();
    }

    /**
     * 更新対象のロックを取得する
     * 
     * @param int $id
     * @param string|null $updatedAt
     * @return void
     */
    private function lockForUpdate(int $id, ?string $updatedAt = null): void
    {
        $row = DB::table('users')
            ->lockForUpdate()
            ->where('id', $id)
            ->first(['updated_at']);

        if ($row === null) {
            throw new CustomException('record_not_found');
        }

        if ($updatedAt !== null && $updatedAt !== $row->updated_at) {
            throw new CustomException('conflict');
        }
    }
}
