<?php

namespace App\Console\Commands;

use App\Http\Controllers\DemoController;
use Illuminate\Console\Command;

class ResetDemoData extends Command
{
    protected $signature   = 'demo:reset';
    protected $description = 'Reset demo student progress every 2 hours';

    public function handle(): void
    {
        DemoController::resetDemoData();
        $this->info('Demo data reset successfully.');
    }
}
