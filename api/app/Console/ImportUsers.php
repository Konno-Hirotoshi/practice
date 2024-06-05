<?php

namespace App\Console;

use App\Base\BaseCommand;
use App\Base\CustomException;
use App\Domain\Users\Dto\CreateDto;
use App\Domain\Users\Service\UseCase as UserUseCase;

/**
 * 利用者インポートバッチ
 */
class ImportUsers extends BaseCommand
{
    /**
     * シグネチャ
     */
    protected $signature = 'import:user {id?}';

    /**
     * コンストラクタ
     */
    public function __construct(private UserUseCase $useCase)
    {
        parent::__construct();
    }

    /**
     * メイン処理
     */
    protected function main()
    {
        $ids = [$this->argument('id') ?? rand(1000, 9999), 1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2, 3, 4, 5];
        foreach ($ids as $id) {
            try {
                $this->do($id);
            } catch (CustomException $e) {
                $this->error($e);
            }
        }
    }

    private function do($id)
    {
        $lastInsertId = $this->useCase->create(new CreateDto(...[
            'fullName' => 'imported user ' . $id,
            'email' => 'user' . $id . '@example.com',
            'roleId' => (int)rand(1, 2),
            'departmentId' => (int)rand(1, 2),
            'password' => 'Aa12345678@',
            'note' => '',
        ]));
        $this->info('registered: ' . $lastInsertId);
    }
}
