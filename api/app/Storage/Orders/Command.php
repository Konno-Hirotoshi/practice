<?php

namespace App\Storage\Orders;

use App\Base\CustomException;
use App\Domain\Orders\Interface\Storage;
use App\Domain\Orders\Order;
use App\Domain\Orders\Validator\Create;
use App\Domain\Orders\Validator\Edit;
use App\Domain\Orders\Validator\Apply;
use App\Domain\Orders\Validator\Approve;
use App\Domain\Orders\Validator\Reject;
use App\Domain\Orders\Validator\Cancel;
use App\Domain\Orders\Validator\Delete;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

/**
 * 取引 - 操作クラス
 */
class Command extends Query implements Storage
{
    /**
     * 引数のオブジェクトをストレージへ保存する
     */
    public function save(Order $order, string $context)
    {
        return match ($context) {
            Create::class => $this->create($order),
            Edit::class => $this->edit($order),
            Apply::class => $this->apply($order),
            Approve::class => $this->approve($order),
            Reject::class => $this->reject($order),
            Cancel::class => $this->cancel($order),
            Delete::class => $this->delete($order),
        };
    }

    /**
     * 作成
     * 
     * @return int 作成された取引のID
     */
    private function create(Order $order): int
    {
        return DB::transaction(function () use ($order) {
            // レコード作成 (取引テーブル)
            $orderId = DB::table('orders')->insertGetId([
                'title' => $order->title,
                'body' => $order->body,
                'approval_status' => 0,
                'owner_user_id' => 0,
            ]);
            return $orderId;
        });
    }

    /**
     * 編集
     */
    private function edit(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // ロック取得
            $this->lockForUpdate($order->id, $order->updatedAt);

            // レコード更新 (取引テーブル)
            DB::table('orders')->where('id', $order->id)->update(array_filter([
                'title' => $order->title,
                'body' => $order->body,
                'updated_at' => new Expression('CURRENT_TIMESTAMP'),
            ], fn ($value) => isset($value)));
        });
    }

    /**
     * 承認フロー: 申請
     */
    private function apply(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // ロック取得
            $this->lockForUpdate($order->id, $order->updatedAt);

            // ステータス更新 (取引テーブル)
            DB::table('orders')->where('id', $order->id)->update([
                'approval_status' => self::APPROVAL_STATUS_APPLY,
            ]);

            // 過去の承認フローを削除 (取引-承認フローテーブル)
            DB::table('order_approval_flows')
                ->where('order_id', $order->id)
                ->delete();

            // 承認フロー追加 (取引-承認フローテーブル)
            $approvalFlows = array_map(fn ($userId, $sequenceNo) => [
                'order_id' => $order->id,
                'sequence_no' => $sequenceNo,
                'user_id' => $userId,
                'approval_status' => 0,
            ], $order->approvalFlows, range(1, count($order->approvalFlows)));
            DB::table('order_approval_flows')
                ->insert($approvalFlows);
        });
    }

    /**
     * 承認フロー: 承認
     */
    private function approve(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // ロック取得
            $this->lockForUpdate($order->id, $order->updatedAt);

            // ステータス更新 (取引テーブル)
            DB::table('orders')->where('id', $order->id)->update([
                'approval_status' => $order->newStatus,
                'updated_at' => new Expression('CURRENT_TIMESTAMP'),
            ]);

            // 承認フロー更新 (取引-承認フローテーブル)
            DB::table('order_approval_flows')
                ->where('order_id', $order->id)
                ->where('sequence_no', $order->sequenceNo)
                ->update([
                    'approval_date' => date('Y-m-d H:i:s', $order->approvalDate),
                    'approval_status' => self::APPROVAL_STATUS_APPROVE,
                ]);
        });
    }

    /**
     * 承認フロー: 非承認
     */
    private function reject(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // ロック取得
            $this->lockForUpdate($order->id, $order->updatedAt);

            // ステータス更新 (取引テーブル)
            DB::table('orders')->where('id', $order->id)->update([
                'approval_status' => self::APPROVAL_STATUS_REJECT,
            ]);

            // 承認フロー更新 (取引-承認フローテーブル)
            DB::table('order_approval_flows')
                ->where('order_id', $order->id)
                ->where('sequence_no', $order->sequenceNo)
                ->update([
                    'approval_date' => date('Y-m-d H:i:s', $order->approvalDate),
                    'approval_status' => self::APPROVAL_STATUS_REJECT,
                ]);
        });
    }

    /**
     * 承認フロー: 取り消し
     */
    private function cancel(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // ロック取得
            $this->lockForUpdate($order->id, $order->updatedAt);

            // ステータス更新 (取引テーブル)
            DB::table('orders')->where('id', $order->id)->update([
                'approval_status' => self::APPROVAL_STATUS_CANCEL,
            ]);
        });
    }

    /**
     * 削除
     */
    private function delete(Order $order): void
    {
        // レコード削除 (取引テーブル)
        DB::table('orders')
            ->where('id', $order->id)
            ->delete();

        // レコード削除 (取引-承認フローテーブル)
        DB::table('order_approval_flows')
            ->where('order_id', $order->id)
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
