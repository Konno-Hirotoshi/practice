<?php

namespace App\Storage\Orders;

use App\Base\CustomException;
use App\Domain\Orders\Order;
use App\Domain\Orders\UseCase\Create;
use App\Domain\Orders\UseCase\Edit;
use App\Domain\Orders\UseCase\Apply;
use App\Domain\Orders\UseCase\Approve;
use App\Domain\Orders\UseCase\Reject;
use App\Domain\Orders\UseCase\Cancel;
use App\Domain\Orders\UseCase\Delete;
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
    public function save(Order $order, string $context)
    {
        return match ($context) {
            Create::class => $this->create($order),
            Edit::class => $this->edit($order),
            Apply::class => $this->apply($order),
            Approve::class => $this->updateApprovalFlow($order),
            Reject::class => $this->updateApprovalFlow($order),
            Cancel::class => $this->updateApprovalFlow($order),
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
            $this->lockForUpdate($order);

            // レコード更新 (取引テーブル)
            DB::table('orders')->where('id', $order->id)->update(array_filter([
                'title' => $order->title ?? null,
                'body' => $order->body ?? null,
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
            $this->lockForUpdate($order);

            // ステータス更新 (取引テーブル)
            DB::table('orders')->where('id', $order->id)->update([
                'approval_status' => $order->approvalStatus,
            ]);

            // 過去の承認フローを削除 (取引-承認フローテーブル)
            DB::table('order_approval_flows')
                ->where('order_id', $order->id)
                ->delete();

            // 承認フロー追加 (取引-承認フローテーブル)
            $approvalFlows = array_map(fn ($approvalFlow) => [
                'order_id' => $order->id,
                'sequence_no' => $approvalFlow->sequenceNo,
                'approval_user_id' => $approvalFlow->approvalUserId,
                'approval_status' => $approvalFlow->approvalStatus,
            ], $order->approvalFlows);
            DB::table('order_approval_flows')
                ->insert($approvalFlows);
        });
    }

    /**
     * 承認フロー: 承認
     * 承認フロー: 非承認
     * 承認フロー: 取り消し
     */
    private function updateApprovalFlow(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // ロック取得
            $this->lockForUpdate($order);

            // ステータス更新 (取引テーブル)
            DB::table('orders')->where('id', $order->id)->update([
                'approval_status' => $order->approvalStatus,
                'updated_at' => new Expression('CURRENT_TIMESTAMP'),
            ]);

            // 承認フロー更新 (取引-承認フローテーブル)
            foreach ($order->approvalFlows as $approvalFlow) {
                DB::table('order_approval_flows')
                    ->where('order_id', $approvalFlow->orderId)
                    ->where('sequence_no', $approvalFlow->sequenceNo)
                    ->update([
                        'approval_date' => $approvalFlow->approvalDate,
                        'approval_status' => $approvalFlow->approvalStatus,
                    ]);
            }
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
     * @param Order $order
     * @return void
     */
    private function lockForUpdate(Order $order): void
    {
        $row = DB::table('orders')
            ->lockForUpdate()
            ->where('id', $order->id)
            ->first(['updated_at']);

        if ($row === null) {
            throw new CustomException('record_not_found');
        }

        if (isset($order->updatedAt) && $order->updatedAt !== $row->updated_at) {
            throw new CustomException('conflict');
        }
    }
}
