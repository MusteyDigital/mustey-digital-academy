<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('instructor')->latest()->get();
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $data['instructor_id'] = auth()->id();

        $course = Course::create($data);

        return redirect()->route('courses.show', $course->id)
            ->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        // If you already have logic for completedLessonIds, keep it here.
        // For now we just pass the course.
        return view('courses.show', compact('course'));
    }

    // Live session settings (meeting_url, starts_at)
    public function editSession(Course $course)
    {
        return view('courses.session', compact('course'));
    }

    public function updateSession(Request $request, Course $course)
    {
        $data = $request->validate([
            'meeting_url' => ['nullable', 'url'],
            'starts_at' => ['nullable', 'date'],
        ]);

        $course->update($data);

        return back()->with('success', 'Live session updated.');
    }
}
