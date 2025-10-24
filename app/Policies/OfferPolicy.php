<?php

namespace Alison\ProjectManagementAssistant\Policies;

use Alison\ProjectManagementAssistant\Models\Offer;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\User;

class OfferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view offers');
    }

    public function create(User $user, Project $project): bool
    {
        if (!$user->hasPermissionTo('create offers')) {
            return false;
        }

        if (!$user->hasRole('student')) {
            return false;
        }

        if ($project->assigned_to !== null) {
            return false;
        }

        if ($project->event->category->course_number !== $user->course_number) {
            return false;
        }

        $hasProjectInEvent = Project::where('event_id', $project->event_id)
            ->where('assigned_to', $user->id)
            ->exists();

        if ($hasProjectInEvent) {
            return false;
        }

        $existingOffer = Offer::where('project_id', $project->id)
            ->where('student_id', $user->id)
            ->exists();

        return !$existingOffer;
    }

    public function delete(User $user, Offer $offer): bool
    {
        if (!$user->hasPermissionTo('delete offers')) {
            return false;
        }

        if ($user->hasRole('student')) {
            return $offer->student_id === $user->id;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        return false;
    }

    public function approve(User $user, Project $project): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return $project->supervisor && $project->supervisor->user_id === $user->id;
        }

        return false;
    }
}
