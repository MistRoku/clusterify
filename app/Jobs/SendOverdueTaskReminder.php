<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Mail;
use App\Mail\OverdueTasksMail;

class SendOverdueTaskReminder implements ShouldQueue
{
    use Queueable;
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            $overdueTasks = $user->tasksAssigned()
                ->where('due_date', '<', now())
                ->where('status', '!=', 'done')
                ->get();
            if ($overdueTasks->count()) {
                Mail::to($user->email)->send(new OverdueTasksMail($overdueTasks));
            }
        }
    }
}
