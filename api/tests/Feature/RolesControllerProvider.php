<?php

namespace Tests\Feature;

class RolesControllerProvider
{
    public static function test_search(): array
    {
        $db = [
            'roles' => [
                ['id' => 1, 'name' => 'Administrator', 'note' => ''],
                ['id' => 2, 'name' => 'Role 2', 'note' => 'note 2'],
                ['id' => 3, 'name' => 'Role 3', 'note' => 'note 3'],
            ]
        ];
        return [
            '正常系 page=1' => [
                'params' => '?perPage=2',
                'database' => $db,
                'responseCode' => 200,
                'responseContent' => [
                    'data' => [
                        ['id' => 1, 'name' => 'Administrator', 'note' => ''],
                        ['id' => 2, 'name' => 'Role 2', 'note' => 'note 2'],
                    ],
                    'total' => 3
                ],
            ],
            '正常系 page=2' => [
                'params' => '?page=2&perPage=2',
                'database' => $db,
                'responseCode' => 200,
                'responseContent' => [
                    'data' => [
                        ['id' => 3, 'name' => 'Role 3', 'note' => 'note 3'],
                    ],
                    'total' => 3
                ],
            ],
            'page=0はエラー' =>    [
                'params' => '?page=0',
                'database' => [],
                'responseCode' => 422,
                'responseContent' => [
                    'reason' => 'invalid_parameters'
                ],
            ],
            ' perPage=0はエラー' => [
                'params' => '?perPage=0',
                'database' => [],
                'responseCode' => 422,
                'responseContent' => [
                    'reason' => 'invalid_parameters'
                ],
            ],
        ];
    }

    public static function test_create()
    {
        $db = [
            'roles' => [
                ['id' => 1, 'name' => 'Administrator', 'note' => ''],
            ],
            'permissions' => [
                ['id' => 1, 'label' => 'p1', 'backend' => '{}', 'frontend' => '{}'],
            ],
        ];
        return [
            '作成成功' => [
                'params' => [
                    'name' => 'Role 2',
                    'note' => 'note 2',
                    'permissionIds' => [1],
                ],
                'database' => $db,
                'responseCode' => 200,
                'responseContent' => [
                    // 'id' => '*',
                ],
                'afterDatabase' => [
                    'roles' => [
                        ['id' => 1, 'name' => 'Administrator', 'note' => ''],
                        ['name' => 'Role 2', 'note' => 'note 2'],
                    ],
                ]
            ],
            '名称重複エラー' => [
                'params' => [
                    'name' => 'Administrator',
                    'note' => 'note X',
                ],
                'database' => $db,
                'responseCode' => 422,
                'responseContent' => [
                    'name' => 'exists',
                ],
                'afterDatabase' => [
                    'roles' => [
                        ['id' => 1, 'name' => 'Administrator', 'note' => ''],
                    ],
                ]
            ],
        ];
    }

    public static function test_edit()
    {
        $db = [
            'roles' => [
                ['id' => 1, 'name' => 'Administrator', 'note' => ''],
            ],
        ];
        return [
            '編集成功' => [
                'id' => 1,
                'params' => [
                    'name' => 'Role 2',
                    'note' => 'note 2',
                ],
                'database' => $db,
                'responseCode' => 200,
                'responseContent' => [
                    'status' => 'succeed',
                ],
                'afterDatabase' => [
                    'roles' => [
                        ['id' => 1, 'name' => 'Role 2', 'note' => 'note 2'],
                    ],
                ]
            ],
            '存在しないID' => [
                'id' => 999,
                'params' => [
                    'name' => 'Role 2',
                    'note' => 'note 2',
                ],
                'database' => $db,
                'responseCode' => 422,
                'responseContent' => [
                    'reason' => 'record_not_found',
                ],
                'afterDatabase' => [
                    'roles' => [
                        ['id' => 1, 'name' => 'Administrator', 'note' => ''],
                    ],
                ]
            ],
        ];
    }

    public static function test_delete()
    {
        $db = [
            'roles' => [
                ['id' => 1, 'name' => 'Administrator', 'note' => ''],
                ['id' => 2, 'name' => 'Role 2', 'note' => 'note 2'],
            ]
        ];
        return [
            '削除成功' => [
                'id' => 2,
                'database' => $db,
                'responseCode' => 200,
                'responseContent' => [
                    'status' => 'succeed',
                ],
                'afterDatabase' => [
                    'roles' => [
                        ['id' => 1, 'name' => 'Administrator', 'note' => ''],
                    ],
                ]
            ],
            '初期ロール削除エラー' => [
                'id' => 1,
                'database' => $db,
                'responseCode' => 422,
                'responseContent' => [
                    'deleteIds' => 'super_role',
                ],
                'afterDatabase' => [
                    'roles' => [
                        ['id' => 1, 'name' => 'Administrator', 'note' => ''],
                        ['id' => 2, 'name' => 'Role 2', 'note' => 'note 2'],
                    ],
                ]
            ],
            '存在しないID' => [   // @todo 422 record_not_foundにすべきか？
                'id' => 999,
                'database' => $db,
                'responseCode' => 200,
                'responseContent' => [
                    'status' => 'succeed',
                ],
                'afterDatabase' => [
                    'roles' => [
                        ['id' => 1, 'name' => 'Administrator', 'note' => ''],
                        ['id' => 2, 'name' => 'Role 2', 'note' => 'note 2'],
                    ],
                ]
            ],
        ];
    }
}
