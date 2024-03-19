<?php

namespace App\Domain\Roles;

use App\Base\CustomException;
use App\Domain\Roles\Interface\Storage;
use App\Domain\Roles\Interface\Validator;

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
    public function __construct($inputData)
    {
        foreach ($inputData as $key => $value) {
            $this->{$key} = $value;
        }

        if ($validationErrors = $this->validate()) {
            throw new CustomException($validationErrors);
        }
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

    /**
     * エンティティを検証して保存する
     * 
     * @param Validator $validator バリデータークラス
     * @param Storage $storage コマンドクラス
     * @return mixed
     */
    public function save(Validator $validator, Storage $storage): mixed
    {
        $validator->validate($this);
        return $storage->save($this, $validator::class);
    }
}
