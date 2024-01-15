<?php

namespace App\Service\Orders;

use App\Base\CustomException;
use App\Base\SearchOption;
use App\Model\Orders\Command as Orders;
use App\Service\Orders\Commands\Approve;
use App\Service\Orders\Commands\Cancel;
use App\Service\Orders\Commands\Create;
use App\Service\Orders\Commands\Delete;
use App\Service\Orders\Commands\Edit;
use App\Service\Orders\Commands\Reject;
use App\Service\Orders\Commands\Apply;
use App\Service\Orders\Support\BusinessRule;
use App\Service\Orders\Support\Validation;

/**
 * 取引 - サービスクラス
 */
class OrdersService
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Orders $orders,
        private Validation $validation,
        private BusinessRule $rule,
    ) {
    }

    /**
     * 一覧検索
     */
    public function search(array $search = [], array $sort = [], int $page = 1, int $perPage = 3): array
    {
        $option = SearchOption::create($search, $sort, $page, $perPage, [
            'id' => 'value',
        ]);

        return $this->orders->search($option);
    }

    /**
     * 詳細情報取得
     */
    public function get(int $id)
    {
        return $this->orders->get($id);
    }

    /**
     * 新規作成
     */
    public function create(
        string $title,
        string $body = '',
    ): int {
        $this->validation
            ->validateTitle($title)
            ->validateBody($body)
            ->throwIfErrors();

        $create = new Create(
            title: $title,
            body: $body,
        );

        return $this->orders->save($create);
    }

    /**
     * 編集
     */
    public function edit(
        int $id,
        ?string $title = null,
        ?string $body = null,
        ?string $updatedAt = null,
    ) {
        $this->validation
            ->validateTitle($title)
            ->validateBody($body)
            ->throwIfErrors();

        // 編集可能であること (承認フローが進行中や承認済みでは無い)
        if (!$this->rule->isEditable($id)) {
            throw new CustomException('not_editable');
        }

        $edit = new Edit(
            id: $id,
            title: $title,
            body: $body,
            updatedAt: $updatedAt,
        );

        $this->orders->save($edit);
    }

    /**
     * 承認フロー: 申請
     */
    public function apply(
        int $id,
        ?string $updatedAt = null,
    ): void {
        // 編集可能であること (承認フローが進行中や承認済みでは無い)
        if (!$this->rule->isEditable($id)) {
            throw new CustomException('not_editable');
        }

        $apply = new Apply(
            id: $id,
            approvalFlows: [1, 1],    // @todo
            updatedAt: $updatedAt,
        );

        $this->orders->save($apply);
    }

    /**
     * 承認フロー: 承認
     */
    public function approve(
        int $id,
        int $sequenceNo,
        int $approvalUserId,
        ?string $updatedAt = null,
    ) {
        // 承認全体のステータスチェック
        if (!$this->rule->isApprovalFlowInProgress($id)) {
            throw new CustomException('not_in_progress');
        }

        // 承認フローのうち、現在承認・却下のアクション待ちの行を取得する
        $approvalFlow = $this->rule->getCurrentApprovalFlow($id);

        // シーケンス番号チェック
        if ($approvalFlow->sequence_no !== $sequenceNo) {
            throw new CustomException('wrong_sequence_no');
        }

        // 指定シーケンス番号の承認者チェック
        if ($approvalFlow->user_id !== $approvalUserId) {
            throw new CustomException('deny_user');
        }

        $approve = new Approve(
            id: $id,
            sequenceNo: $sequenceNo,
            approvalDate: time(),
            newStatus: $this->rule->getNewStatus($approvalFlow->isFinal),
            updatedAt: $updatedAt,
        );

        $this->orders->save($approve);
    }

    /**
     * 承認フロー: 却下
     */
    public function reject(
        int $id,
        int $sequenceNo,
        int $approvalUserId,
        ?string $updatedAt = null,
    ) {
        // 承認全体のステータスチェック
        if (!$this->rule->isApprovalFlowInProgress($id)) {
            throw new CustomException('not_in_progress');
        }

        // 承認フローのうち、現在承認・却下のアクション待ちの行を取得する
        $approvalFlow = $this->rule->getCurrentApprovalFlow($id);

        // シーケンス番号チェック
        if ($approvalFlow->sequence_no !== $sequenceNo) {
            throw new CustomException('wrong_sequence_no');
        }

        // 指定シーケンス番号の承認者チェック
        if ($approvalFlow->user_id !== $approvalUserId) {
            throw new CustomException('deny_user');
        }

        $reject = new Reject(
            id: $id,
            sequenceNo: $sequenceNo,
            approvalDate: time(),
            updatedAt: $updatedAt,
        );

        $this->orders->save($reject);
    }

    /**
     * 承認フロー: 取消
     */
    public function cancel(
        int $id,
        int $approvalUserId,
        ?string $updatedAt = null,
    ) {
        // 承認全体のステータスチェック
        if (!$this->rule->isApprovalFlowInProgress($id)) {
            throw new CustomException('not_in_progress');
        }
        
        $cancel = new Cancel(
            id: $id,
            updatedAt: $updatedAt,
        );

        $this->orders->save($cancel);
    }

    /**
     * 削除
     */
    public function delete(int|array $deleteIds)
    {
        if (!is_array($deleteIds)) {
            $deleteIds = [$deleteIds];
        }

        $this->validation
            ->validateDeleteIds($deleteIds)
            ->throwIfErrors();

        $delete = new Delete(
            deleteIds: $deleteIds,
        );

        $this->orders->save($delete);
    }
}
