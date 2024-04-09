<?php

namespace App\Domain\Users;

use App\Base\CustomException;

/**
 * 利用者
 */
readonly class User
{
    /** @var int ID */
    public ?int $id;

    /** @var string フルネーム */
    public string $fullName;

    /** @var string メールアドレス */
    public string $email;

    /** @var int 部署ID */
    public int $departmentId;

    /** @var int 役割ID */
    public int $roleId;

    /** @var string パスワード */
    public string $password;

    /** @var string 備考 */
    public string $note;

    /** @var string 最終更新日時 */
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
     * 編集
     */
    public function edit(array $inputData)
    {
        return new User(['id' => $this->id] + $inputData);
    }

    /**
     * パスワード編集
     */
    public function editPassword(string $password)
    {
        return new User([
            'id' => $this->id,
            'password' => $password,
        ]);
    }

    /**
     * 削除
     */
    public function delete()
    {
        return $this;
    }

    /**
     * エンティティの妥当性を検証する
     */
    private function validate(): array
    {
        $validationErrors = [];

        if (isset($this->email)) {
            // メールアドレスの形式が正しいか
            $validEmail = $this->isValidEmail($this->email);
            if (!$validEmail) {
                $validationErrors['email'] = 'rule';
            }
        }

        if (isset($this->password)) {
            // パスワードがルールに合致するか
            $validPassword = $this->isValidPassword($this->password);
            if (!$validPassword) {
                $validationErrors['password'] = 'rule';
            }
        }

        return $validationErrors;
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
        $hasUpperCharacter = preg_match('/[a-z]/', $password) === 1;
        $hasLowerCharacter = preg_match('/[A-Z]/', $password) === 1;
        $hasNumer = preg_match('/[0-9]/', $password) === 1;
        $hasSymbol = preg_match('/[!-\/:-@\[-`{-~]/', $password) === 1;

        return ($hasUpperCharacter && $hasLowerCharacter && $hasNumer && $hasSymbol);
    }
}
