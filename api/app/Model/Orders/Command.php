<?php

namespace App\Model\Orders;

use App\Base\CustomException;
use App\Service\Orders\Commands\Approve;
use App\Service\Orders\Commands\Cancel;
use App\Service\Orders\Commands\Create;
use App\Service\Orders\Commands\Delete;
use App\Service\Orders\Commands\Edit;
use App\Service\Orders\Commands\Reject;
use App\Service\Orders\Commands\Apply;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

/**
 * 取引 - 操作クラス
 */
class Command extends Query
{
    /**
     * 引数のオブジェクトをストレージへ保存する
     */
    public function save(object $dto)
    {
        return match (true) {
            $dto instanceof Create => $this->create($dto),
            $dto instanceof Edit => $this->edit($dto),
            $dto instanceof Apply => $this->apply($dto),
            $dto instanceof Approve => $this->approve($dto),
            $dto instanceof Reject => $this->reject($dto),
            $dto instanceof Cancel => $this->cancel($dto),
            $dto instanceof Delete => $this->delete($dto),
        };
    }

    /**
     * 作成
     * 
     * @return int 作成された取引のID
     */
    private function create(Create $dto): int
    {
        return DB::transaction(function () use ($dto) {
            // レコード作成 (取引テーブル)
            $orderId = DB::table('orders')->insertGetId([
                'title' => $dto->title,
                'body' => $dto->body,
            ]);
            return $orderId;
        });
    }

    /**
     * 編集
     */
    private function edit(Edit $dto): void
    {
        DB::transaction(function () use ($dto) {
            // ロック取得
            $this->lockForUpdate($dto->id, $dto->updatedAt);

            // レコード更新 (取引テーブル)
            DB::table('orders')->where('id', $dto->id)->update(array_filter([
                'title' => $dto->title,
                'body' => $dto->body,
                'updated_at' => new Expression('CURRENT_TIMESTAMP'),
            ], fn ($value) => isset($value)));
        });
    }

    /**
     * 承認フロー: 申請
     */
    private function apply(Apply $dto): void
    {
        DB::transaction(function () use ($dto) {
            // ロック取得
            $this->lockForUpdate($dto->id, $dto->updatedAt);

            // ステータス更新 (取引テーブル)
            DB::table('orders')->where('id', $dto->id)->update([
                'approval_status' => self::APPROVAL_STATUS_APPLY,
            ]);

            // 過去の承認フローを削除 (取引-承認フローテーブル)
            DB::table('order_approval_flows')
                ->where('order_id', $dto->id)
                ->delete();

            // 承認フロー追加 (取引-承認フローテーブル)
            $approvalFlows = array_map(fn ($userId, $sequenceNo) => [
                'order_id' => $dto->id,
                'sequence_no' => $sequenceNo,
                'user_id' => $userId,
            ], $dto->approvalFlows, range(1, count($dto->approvalFlows)));
            DB::table('order_approval_flows')
                ->insert($approvalFlows);
        });
    }

    /**
     * 承認フロー: 承認
     */
    private function approve(Approve $dto): void
    {
        DB::transaction(function () use ($dto) {
            // ロック取得
            $this->lockForUpdate($dto->id, $dto->updatedAt);

            // ステータス更新 (取引テーブル)
            DB::table('orders')->where('id', $dto->id)->update([
                'approval_status' => $dto->newStatus,
                'updated_at' => new Expression('CURRENT_TIMESTAMP'),
            ]);

            // 承認フロー更新 (取引-承認フローテーブル)
            DB::table('order_approval_flows')
                ->where('order_id', $dto->id)
                ->where('sequence_no', $dto->sequenceNo)
                ->update([
                    'approval_date' => date('Y-m-d H:i:s', $dto->approvalDate),
                    'approval_status' => self::APPROVAL_STATUS_APPROVE,
                ]);
        });
    }

    /**
     * 承認フロー: 非承認
     */
    private function reject(Reject $dto): void
    {
        DB::transaction(function () use ($dto) {
            // ロック取得
            $this->lockForUpdate($dto->id, $dto->updatedAt);

            // ステータス更新 (取引テーブル)
            DB::table('orders')->where('id', $dto->id)->update([
                'approval_status' => self::APPROVAL_STATUS_REJECT,
            ]);

            // 承認フロー更新 (取引-承認フローテーブル)
            DB::table('order_approval_flows')
                ->where('order_id', $dto->id)
                ->where('sequence_no', $dto->sequenceNo)
                ->update([
                    'approval_date' => date('Y-m-d H:i:s', $dto->approvalDate),
                    'approval_status' => self::APPROVAL_STATUS_REJECT,
                ]);
        });
    }

    /**
     * 承認フロー: 取り消し
     */
    private function cancel(Cancel $dto): void
    {
        DB::transaction(function () use ($dto) {
            // ロック取得
            $this->lockForUpdate($dto->id, $dto->updatedAt);

            // ステータス更新 (取引テーブル)
            DB::table('orders')->where('id', $dto->id)->update([
                'approval_status' => self::APPROVAL_STATUS_CANCEL,
            ]);
        });
    }

    /**
     * 削除
     */
    private function delete(Delete $dto): void
    {
        // レコード削除 (取引テーブル)
        DB::table('orders')
            ->where('id', $dto->deleteIds)
            ->delete();

        // レコード削除 (取引-承認フローテーブル)
        DB::table('order_approval_flows')
            ->where('order_id', $dto->deleteIds)
            ->delete();
    }

    /**
     * 更新対象のロックを取得する
     * 
     * @param int $id
     * @param string|null $updatedAt
     * @return void
     */
    private function lockForUpdate(int $id, string $updatedAt = null): void
    {
        $row = DB::table('orders')
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
