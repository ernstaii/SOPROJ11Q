<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunShortSchedule extends Command
{
    protected $signature = 'short-schedule:daemon {--sleep=1}';
    protected $description = 'Runs the short-schedule as a background process constantly.';

    public function handle()
    {
        while (true)
        {
            $this->call('short-schedule:run');
            sleep($this->option('sleep'));
        }
    }
}
