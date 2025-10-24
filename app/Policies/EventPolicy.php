<?php

namespace Alison\ProjectManagementAssistant\Policies;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view events');
    }

    public function view(User $user, Event $event): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return $event->supervisors->contains('user_id', $user->id);
        }

        if ($user->hasRole('student')) {
            return $event->category->course_number === $user->course_number;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create events');
    }

    public function update(User $user, Event $event): bool
    {
        if (!$user->hasPermissionTo('edit events')) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return $event->supervisors->contains('user_id', $user->id);
        }

        return false;
    }

    public function delete(User $user, Event $event): bool
    {
        if (!$user->hasPermissionTo('delete events')) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    public function manageSupervisors(User $user, Event $event): bool
    {
        if (!$user->hasPermissionTo('view supervisors')) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return $event->supervisors->contains('user_id', $user->id);
        }

        return false;
    }
}
