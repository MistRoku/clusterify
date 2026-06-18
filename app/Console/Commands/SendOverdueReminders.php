<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Jobs\SendOverdueTaskReminder;

#[Signature('app:send-overdue-reminders')]
#[Description('Command description')]
class SendOverdueReminders extends Command
{
    /**
     * Execute the console command.
     */

    protected $signature = 'tasks:overdue-remind';
    protected $description = 'Queue overdue task reminders';


    public function handle()
    {
        SendOverdueTaskReminder::dispatch();
        $this->info('Overdue reminder job queued.');
    }
}
