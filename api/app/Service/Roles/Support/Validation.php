<?php

namespace App\Service\Roles\Support;

use App\Model\Roles\Query as Roles;
use App\Model\Permissions\Query as Permissions;
use App\Base\BaseValidator;

/**
 * 役割バリデーション
 */
class Validation extends BaseValidator
{
    /**
     * コンストラクタ
     */
    public function __construct(
        private Roles $roles,
        private Permissions $permissions
    ) {
    }

    /**
     * バリデーションチェック: 名称
     */
    public function validateName(?string $name, ?int $selfId = null): self
    {
        // 未入力ならチェック対象外
        if ($name === null) {
            return $this;
        }

        // 同名称の役割が存在するか
        $existsName = ($selfId === null)
            ? $this->roles->existsName($name)
            : $this->roles->existsNameOnUpdate($name, $selfId);
        if ($existsName) {
            return $this->setError('name', 'exists');
        };

        return $this;
    }

    /**
     * バリデーションチェック: 備考
     */
    public function validateNote(?string $note): self
    {
        // 未入力ならチェック対象外
        if ($note === null) {
            return $this;
        }
        return $this;
    }

    /**
     * バリデーションチェック: 権限IDリスト
     */
    public function validatePermissionIds(?array $permissionIds): self
    {
        // 未入力ならチェック対象外
        if ($permissionIds === null) {
            return $this;
        }

        // 存在する権限IDか
        $existsPermissionIds = $this->permissions->exists($permissionIds);
        if (!$existsPermissionIds) {
            return $this->setError('permissionIds', 'not_found');
        }

        return $this;
    }
}
