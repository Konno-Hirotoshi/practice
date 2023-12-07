<?php

namespace App\Base;

use Illuminate\Foundation\Console\Kernel;

class ConsoleKernel extends Kernel
{
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/../Console');
    }
}
