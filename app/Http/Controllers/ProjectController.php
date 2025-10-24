<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\Technology;
use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Project::class);

        $user = Auth::user();
        $query = Project::with(['event', 'supervisor.user', 'technologies', 'assignedTo', 'messages', 'offers']);

        if ($user->hasRole('admin')) {
        } elseif ($user->hasRole('student')) {
            $query->where('assigned_to', $user->id);
        } else {
            $supervisorIds = Supervisor::where('user_id', $user->id)->pluck('id');
            $query->whereIn('supervisor_id', $supervisorIds);
        }

        if ($request->filled('search')) {
            $query->searchByNameOrBody($request->search);
        }

        if ($request->filled('event')) {
            $query->byEvent($request->event);
        }

        if ($request->filled('technology')) {
            $query->byTechnology($request->technology);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        if ($sortBy === 'name') {
            $query->orderByName($sortDirection);
        } else {
            $query->orderByCreated($sortDirection);
        }

        $projects = $query->paginate(12);
        $projects->appends(request()->query());

        $events = Event::orderBy('name')->get();
        $technologies = Technology::orderBy('name')->get();

        return view('projects.index', compact('projects', 'events', 'technologies'));
    }

    public function offers(): View
    {
        return view('projects.offers');
    }

    public function show(Project $project): View
    {
        $this->authorize('view', $project);

        $cacheKey = "project_{$project->id}_show";
        $cacheDuration = now()->addHour();

        $project = Cache::remember($cacheKey, $cacheDuration, function () use ($project) {
            return $project->load([
                'event.category',
                'supervisor.user',
                'technologies',
                'assignedTo',
                'messages.sender'
            ]);
        });

        return view('projects.show', compact('project'));
    }
}
