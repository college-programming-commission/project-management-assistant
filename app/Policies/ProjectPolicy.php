<?php

namespace Alison\ProjectManagementAssistant\Policies;

use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view projects');
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return $project->supervisor && $project->supervisor->user_id === $user->id;
        }

        if ($user->hasRole('student')) {
            return $project->assigned_to === $user->id 
                || $project->event->category->course_number === $user->course_number;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create projects');
    }

    public function update(User $user, Project $project): bool
    {
        if (!$user->hasPermissionTo('edit projects')) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return $project->supervisor && $project->supervisor->user_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Project $project): bool
    {
        if (!$user->hasPermissionTo('delete projects')) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return $project->supervisor && $project->supervisor->user_id === $user->id;
        }

        return false;
    }

    public function accessChat(User $user, Project $project): bool
    {
        if (!$project->assigned_to) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return $project->supervisor && $project->supervisor->user_id === $user->id;
        }

        if ($user->hasRole('student')) {
            return $project->assigned_to === $user->id;
        }

        return false;
    }
}
