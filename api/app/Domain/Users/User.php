<?php

namespace App\Domain\Users;

use App\Base\CustomException;
use App\Domain\Users\Interface\Storage;
use App\Domain\Users\Interface\Validator;

/**
 * 利用者
 */
readonly class User
{
    /**
     * コンストラクタ
     *
     * @param int $id ID
     * @param string $fullName フルネーム
     * @param string $email メールアドレス
     * @param int $departmentId 部署ID
     * @param int $roleId 役割ID
     * @param string $password パスワード
     * @param string $note 備考
     * @param string $updatedAt 最終更新日時
     */
    public function __construct(
        public ?int $id = null,
        public string $fullName = '',
        public string $email = '',
        public int $departmentId = 0,
        public int $roleId = 0,
        public string $password = 'default@',
        public string $note = '',
        public string $updatedAt = '',
    ) {
        $validationErrors = [];

        // メールアドレスの形式が正しいか
        $validEmail = $this->isValidEmail($email);
        if (!$validEmail) {
            $validationErrors['email'] = 'rule';
        }

        // パスワードがルールに合致するか
        $validPassword = $this->isValidPassword($password);
        if (!$validPassword) {
            $validationErrors['password'] = 'rule';
        }

        if ($validationErrors) {
            throw new CustomException($validationErrors);
        }
    }

    /**
     * メールアドレスの形式が正しいか (簡易判定)
     * 
     * 【メールアドレスルール】
     * ・@を含む
     * ・半角英数記号のみ
     */
    private function isValidEmail(string $email): bool
    {
        if ($email === '') {
            return true;
        }
        return preg_match('/^[!-~]+@[!-~]+$/', $email) === 1;
    }

    /**
     * パスワードがルールに合致するか
     * 
     * 【パスワードルール】
     * ・小文字を含む
     * ・大文字を含む
     * ・数字を含む
     * ・記号を含む
     */
    private function isValidPassword(string $password): bool
    {
        if ($password === '') {
            return true;
        }

        $hasUpperCharacter = preg_match('/[a-z]/', $password) === 1;
        $hasLowerCharacter = preg_match('/[A-Z]/', $password) === 1;
        $hasNumer = preg_match('/[0-9]/', $password) === 1;
        $hasSymbol = preg_match('/[!-\/:-@\[-`{-~]/', $password) === 1;

        return ($hasUpperCharacter && $hasLowerCharacter && $hasNumer && $hasSymbol);
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
