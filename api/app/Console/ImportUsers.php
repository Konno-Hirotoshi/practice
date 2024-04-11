<?php

namespace App\Console;

use App\Base\CustomException;
use App\Domain\Users\UserUseCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * 利用者インポートバッチ
 */
class ImportUsers extends Command
{
    /** シグネチャ */
    protected $signature = 'import:user {id?}';

    private UserUseCase $useCase;

    /**
     * メイン処理
     */
    public function handle(UserUseCase $useCase)
    {
        $this->useCase = $useCase;

        Log::info('start');

        $ids = [$this->argument('id') ?? rand(1000, 9999)];

        foreach ($ids as $id) {
            try {
                $this->do($id);
            } catch (CustomException $e) {
                Log::error($e);
            }
        }
        Log::info('end' . PHP_EOL);
    }

    private function do($id)
    {
        $this->useCase->create([
            'fullName' => 'imported user ' . $id,
            'email' => 'user' . $id . '@example.com',
            'roleId' => 1,
            'departmentId' => 1,
            'password' => 'Aa12345678@',
            'note' => '',
        ]);
    }
}
