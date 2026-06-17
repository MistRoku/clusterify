<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;

class TaskPolicy
{
    public function update(User $user, Task $task)
    {
        return $user->id === $task->created_by
            || $user->hasCompanyRole('company_admin', $task->project->company_id)
            || $user->hasCompanyRole('project_lead', $task->project->company_id)
            || $user->is_super_admin;
    }
}
