<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::query()->with(['subjects']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('course_number')) {
            $query->where('course_number', $request->course_number);
        }

        if ($request->filled('subject')) {
            $query->withSubject($request->subject);
        }

        if ($request->filled('min_period')) {
            $query->minPeriod($request->min_period);
        }

        if ($request->filled('max_period')) {
            $query->maxPeriod($request->max_period);
        }

        $categories = $query->orderBy('name')->paginate(9);
        $categories->appends(request()->query());

        $subjects = Subject::all();

        return view('categories.index', compact('categories', 'subjects'));
    }

    public function show(Category $category): View
    {
        $category->load(['subjects']);

        return view('categories.show', compact('category'));
    }
}
