<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:complete-expired-events')]
#[Description('Command description')]
class CompleteExpiredEvents extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
