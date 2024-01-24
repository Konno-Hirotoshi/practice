<?php

namespace Tests\Feature;

class SessionsControllerProvider
{
    public static function test_login(): array
    {
        return [
            'パラメータ: 無し' => [
                'params' => [],
                'database' => [],
                'responseCode' => 422,
                'responseContent' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                ]
            ],

            'パラメータ: emailのみ' => [
                'params' => ['email' => '123'],
                'database' => [],
                'responseCode' => 422,
                'responseContent' => [
                    'password' => ['The password field is required.'],
                ]
            ],

            'パラメータ: passwordのみ' => [
                'params' => ['password' => '123'],
                'database' => [],
                'responseCode' => 422,
                'responseContent' => [
                    'email' => ['The email field is required.'],
                ]
            ],

            '間違ったemail, 正しいpassword' => [
                'params' => ['email' => 'invalid-email', 'password' => '123'],
                'database' => [],
                'responseCode' => 422,
                'responseContent' => ['reason' => 'failure']
            ],

            '正しいemail, 間違ったpassword' => [
                'params' => ['email' => '123', 'password' => 'invalid-password'],
                'database' => [],
                'responseCode' => 422,
                'responseContent' => ['reason' => 'failure']
            ],

            '正しいemail, 正しいpassword' => [
                'params' => ['email' => 'test', 'password' => '123'],
                'database' => [
                    'users' => [
                        ['id' => 1, 'full_name' => 'user 1', 'email' => 'test', 'department_id' => 10, 'role_id' => 1, 'password' => password_hash('123', PASSWORD_BCRYPT), 'note' => '-'],
                    ],
                ],
                'responseCode' => 200,
                'responseContent' => [
                    'user_id' => 1,
                    'permissions' => []
                ]
            ],

        ];
    }

    public static function test_logout(): array
    {
        return [
            'key無し' => [
                'key' => '',
                'responseCode' => 200,
                'responseContent' => ['message' => 'succeed']
            ],
            'key有り' => [
                'key' => 'abcdef',
                'responseCode' => 200,
                'responseContent' => ['message' => 'succeed']
            ],
        ];
    }
}
