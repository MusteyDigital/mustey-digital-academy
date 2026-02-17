<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'thumbnail' => ['nullable', 'image', 'max:2048'], // NEW
        ]);

        $data['instructor_id'] = auth()->id();

        // Thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')
                ->store('course-thumbnails', 'public');
        }

        $course = Course::create($data);

        return redirect()->route('courses.show', $course->id)
            ->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }

    // ================= EDIT COURSE (NEW) =================
    public function edit(Course $course)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        return view('courses.edit', compact('course'));
    }

    // ================= UPDATE COURSE (NEW) =================
    public function update(Request $request, Course $course)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        // Replace thumbnail if new one uploaded
        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }

            $data['thumbnail'] = $request->file('thumbnail')
                ->store('course-thumbnails', 'public');
        }

        $course->update($data);

        return redirect()->route('courses.show', $course->id)
            ->with('success', 'Course updated successfully.');
    }

    // ================= DELETE COURSE (NEW) =================
public function destroy(Course $course)
{
    $user = auth()->user();

    // Admin can delete anything
    if ($user->role === 'admin') {
        $course->delete();
        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    // Instructor can delete ONLY their own course
    if ($user->role === 'instructor' && $course->instructor_id === $user->id) {
        $course->delete();
        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    abort(403);
}

    // ================= LIVE SESSION =================
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
