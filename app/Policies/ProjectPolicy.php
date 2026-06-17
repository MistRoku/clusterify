<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;

class ProjectPolicy
{
    public function delete(User $user, Project $project)
    {
        return $user->id === $project->owner_id
            || $user->hasCompanyRole('company_admin', $project->company_id)
            || $user->is_super_admin;
    }
}
