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
    /**
     * コンストラクタ
     *
     * @param int $id ID
     * @param string $name 名称
     * @param string $note 備考
     * @param array $permissionIds 選択された権限のリスト
     * @param string $updatedAt 最終更新日時
     */
    public function __construct(
        public ?int $id = null,
        public string $name = '',
        public string $note = '',
        public array $permissionIds = [],
        public string $updatedAt = '',
    ) {
        $validationErrors = [];

        foreach ($permissionIds as $permissionId) {
            if (!ctype_digit($permissionId)) {
                $validationErrors['permission_ids'] = 'format';
                break;
            }
        }

        if ($validationErrors) {
            throw new CustomException($validationErrors);
        }
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
        return $storage->save($validator::class, $this);
    }
}
