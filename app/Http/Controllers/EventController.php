<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $page = $request->get('page', 1);
        $cacheKey = "events_index_" . md5(json_encode($request->query()) . "_page_{$page}_user_{$user->id}");
        $cacheDuration = now()->addMinutes(30);

        $events = Cache::remember($cacheKey, $cacheDuration, function () use ($user, $request) {
            $query = Event::query()->with(['category', 'projects', 'supervisors.user']);

            // Обмеження доступу до подій
            if ($user->hasRole('teacher')) {
                $query->whereHas('supervisors', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } elseif ($user->hasRole('student')) {
                $query->whereHas('category', function ($q) use ($user) {
                    $q->where('course_number', $user->course_number);
                });
            }

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('start_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('end_date', '<=', $request->date_to);
            }

            return $query->orderBy('start_date')->paginate(9);
        });

        $events->appends(request()->query());

        return view('events.index', compact('events'));
    }

    public function current(Request $request): View
    {
        $user = Auth::user();
        $page = $request->get('page', 1);
        $cacheKey = "events_current_" . md5(json_encode($request->query()) . "_page_{$page}_user_{$user->id}");
        $cacheDuration = now()->addMinutes(15);

        $events = Cache::remember($cacheKey, $cacheDuration, function () use ($user, $request) {
            $query = Event::query()
                ->where('start_date', '<=', Carbon::now())
                ->where('end_date', '>=', Carbon::now())
                ->with(['category', 'projects', 'supervisors.user']);

            // Обмеження доступу до подій
            if ($user->hasRole('teacher')) {
                $query->whereHas('supervisors', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } elseif ($user->hasRole('student')) {
                $query->whereHas('category', function ($q) use ($user) {
                    $q->where('course_number', $user->course_number);
                });
            }

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            return $query->orderBy('start_date')->paginate(9);
        });

        $events->appends(request()->query());

        return view('events.current', compact('events'));
    }

    public function upcoming(Request $request): View
    {
        $user = Auth::user();
        $page = $request->get('page', 1);
        $cacheKey = "events_upcoming_" . md5(json_encode($request->query()) . "_page_{$page}_user_{$user->id}");
        $cacheDuration = now()->addHour();

        $events = Cache::remember($cacheKey, $cacheDuration, function () use ($user, $request) {
            $query = Event::query()
                ->where('start_date', '>', Carbon::now())
                ->with(['category', 'projects', 'supervisors.user']);

            // Обмеження доступу до подій
            if ($user->hasRole('teacher')) {
                $query->whereHas('supervisors', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } elseif ($user->hasRole('student')) {
                $query->whereHas('category', function ($q) use ($user) {
                    $q->where('course_number', $user->course_number);
                });
            }

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            return $query->orderBy('start_date')->paginate(9);
        });

        $events->appends(request()->query());

        return view('events.upcoming', compact('events'));
    }

    public function archived(Request $request): View
    {
        $user = Auth::user();
        $page = $request->get('page', 1);
        $cacheKey = "events_archived_" . md5(json_encode($request->query()) . "_page_{$page}_user_{$user->id}");
        $cacheDuration = now()->addDay();

        $events = Cache::remember($cacheKey, $cacheDuration, function () use ($user, $request) {
            $query = Event::query()
                ->where('end_date', '<', Carbon::now())
                ->with(['category', 'projects', 'supervisors.user']);

            // Обмеження доступу до подій
            if ($user->hasRole('teacher')) {
                $query->whereHas('supervisors', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } elseif ($user->hasRole('student')) {
                $query->whereHas('category', function ($q) use ($user) {
                    $q->where('course_number', $user->course_number);
                });
            }

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            return $query->orderByDesc('end_date')->paginate(9);
        });

        $events->appends(request()->query());

        return view('events.archived', compact('events'));
    }

    public function show(Event $event): View
    {
        $this->authorize('view', $event);

        $cacheKey = "event_{$event->id}_show";
        $cacheDuration = now()->addHours(6);

        $event = Cache::remember($cacheKey, $cacheDuration, function () use ($event) {
            return $event->load([
                'category',
                'supervisors.user',
                'projects.technologies',
                'projects.assignedTo',
                'subevents'
            ]);
        });

        return view('events.show', compact('event'));
    }
}
