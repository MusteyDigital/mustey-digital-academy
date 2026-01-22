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
    $user = Auth::user();

    // Load lessons + instructor
    $course->load(['lessons', 'instructor']);

    $completedLessonIds = [];

    if ($user && $user->role === 'student') {
        // Only consider completions for this course's lessons
        $lessonIds = $course->lessons->pluck('id');

        $completedLessonIds = \App\Models\LessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessonIds)
            ->pluck('lesson_id')
            ->toArray();
    }

    return view('courses.show', compact('course', 'completedLessonIds'));
}
public function editSession(Course $course)
{
    if (Auth::user()->role !== 'instructor') {
        abort(403);
    }

    if ($course->instructor_id !== Auth::id()) {
        abort(403);
    }

    return view('courses.session', compact('course'));
}

public function updateSession(Request $request, Course $course)
{
    if (Auth::user()->role !== 'instructor') {
        abort(403);
    }

    if ($course->instructor_id !== Auth::id()) {
        abort(403);
    }

    $validated = $request->validate([
        'meeting_url' => 'nullable|url|max:2048',
        'starts_at' => 'nullable|date',
    ]);

    $course->update([
        'meeting_url' => $validated['meeting_url'] ?? null,
        'starts_at' => $validated['starts_at'] ?? null,
    ]);

    return redirect()->route('courses.show', $course->id);
}

}
