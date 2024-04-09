<?php

namespace App\Domain\Roles;

use App\Base\CustomException;

/**
 * 役割
 */
readonly class Role
{
    /** @var int $id ID */
    public ?int $id;

    /** @var string $name 名称 */
    public string $name;

    /** @var string $note 備考 */
    public string $note;

    /** @var array $permissionIds 選択された権限のリスト */
    public array $permissionIds;

    /** @var string $updatedAt 最終更新日時 */
    public string $updatedAt;


    /**
     * コンストラクタ
     *
     * @param array $inputData 入力パラメータ
     */
    public function __construct(array $inputData)
    {
        foreach ($inputData as $key => $value) {
            $this->{$key} = $value;
        }

        if ($validationErrors = $this->validate()) {
            throw new CustomException($validationErrors);
        }
    }

    /**
     * 編集
     */
    public function edit(array $inputData)
    {
        return new Role(['id' => $this->id] + $inputData);
    }

    /**
     * エンティティの妥当性を検証する
     */
    private function validate(): array
    {
        $validationErrors = [];

        if (isset($this->permissionIds)) {
            foreach ($this->permissionIds as $permissionId) {
                if (!is_int($permissionId)) {
                    $validationErrors['permission_ids'] = 'format';
                    break;
                }
            }
        }

        return $validationErrors;
    }
}
