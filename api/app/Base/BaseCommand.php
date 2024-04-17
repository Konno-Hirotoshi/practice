<?php

namespace App\Base;

use DateTime;
use Illuminate\Console\Command;
use Throwable;

abstract class BaseCommand extends Command
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Handler method
     */
    public function handle()
    {
        $this->info('start');
        $t0 = microtime(true);
        try {
            $this->main();
        } catch (Throwable $e) {
            $this->error($e);
        } finally {
            $t1 = microtime(true) - $t0;
            $this->info('end (time: ' . floor($t1 / 60) . 'm' . ($t1 % 60) . 's)' . PHP_EOL);
        }
    }

    /**
     * Write a string as information output.
     *
     * @param string $string
     * @param int|string|null $verbosity
     * @return void
     */
    public function info($string, $verbosity = null)
    {
        $now = new DateTime();
        $this->line($now->format(self::DATE_FORMAT) . ' INFO ' . $string, 'info', $verbosity);
    }

    /**
     * Write a string as error output.
     *
     * @param string $string
     * @param int|string|null $verbosity
     * @return void
     */
    public function error($string, $verbosity = null)
    {
        $now = new DateTime();
        $this->line($now->format(self::DATE_FORMAT) . ' ERROR ' . $string, 'error', $verbosity);
    }

    /**
     * Main method
     */
    abstract protected function main();
}
