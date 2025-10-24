<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $query = Subject::query()->with(['categories']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('course_number')) {
            $query->where('course_number', $request->course_number);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        $subjects = $query->orderBy('name')->paginate(9);
        $subjects->appends(request()->query());

        $categories = Category::all();

        return view('subjects.index', compact('subjects', 'categories'));
    }

    public function show(Subject $subject): View
    {
        $subject->load(['categories']);

        return view('subjects.show', compact('subject'));
    }
}
