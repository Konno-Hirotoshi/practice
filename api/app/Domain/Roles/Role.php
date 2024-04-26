<?php

namespace App\Domain\Roles;

use App\Base\CustomException;
use App\Domain\Roles\Dto\CreateDto;
use App\Domain\Roles\Dto\EditDto;

/**
 * 役割 - エンティティ
 */
readonly class Role
{
    /** 全権限ロールID */
    const int SUPER_ROLE_ID = 1;

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
     * 新規作成
     */
    public static function create(CreateDto $dto)
    {
        return new self($dto->getData());
    }

    /**
     * 編集
     */
    public function edit(EditDto $dto)
    {
        return $this->newInstance($dto->getData());
    }

    /**
     * 削除
     */
    public function delete()
    {
        // 全権限ロールか
        if ($this->isSuperRole()) {
            throw new CustomException('super_role');
        }

        return $this;
    }

    /**
     * 新しいインスタンスを作成する
     *
     * @param array $inputData 入力パラメータ
     * @return self
     */
    private function newInstance(array $inputData): self
    {
        return new self([
            'id' => $this->id,
            'updatedAt' => $this->updatedAt ?? null,
        ] + $inputData);
    }

    /**
     * エンティティの妥当性を検証する
     */
    private function validate(): array
    {
        $validationErrors = [];

        // 名称
        if (isset($this->name)) {
            // 空欄でないこと
            if ($this->name === '') {
                $validationErrors['name'] = 'empty';
            }
        }

        // 選択された権限のリスト
        if (isset($this->permissionIds)) {
            foreach ($this->permissionIds as $permissionId) {
                // 数値であること
                if (!is_int($permissionId)) {
                    $validationErrors['permission_ids'] = 'format';
                    break;
                }
            }
        }

        return $validationErrors;
    }

    /**
     * 全権限ロールIDか
     * 
     * @return bool
     */
    private function isSuperRole(): bool
    {
        return $this->id === self::SUPER_ROLE_ID;
    }
}
