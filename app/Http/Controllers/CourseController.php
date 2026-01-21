<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        return view('courses.index', [
            'courses' => Course::all()
        ]);
    }

    public function create()
    {
        if (Auth::user()->role !== 'instructor') {
            abort(403);
        }

        return view('courses.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'instructor') {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Course::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'instructor_id' => Auth::id(),
        ]);

        return redirect()->route('courses.index');
    }

    public function show(Course $course)
{
    $course->load('lessons', 'instructor', 'students');
    return view('courses.show', compact('course'));
}

}
