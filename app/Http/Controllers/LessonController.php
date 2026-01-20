<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    // Instructor: show form to create lesson for a course
    public function create(Course $course)
    {
        if (Auth::user()->role !== 'instructor') {
            abort(403);
        }

        // Optional: ensure instructor owns the course
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        return view('lessons.create', compact('course'));
    }

    // Instructor: store lesson
    public function store(Request $request, Course $course)
    {
        if (Auth::user()->role !== 'instructor') {
            abort(403);
        }

        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|string|max:2048',
            'starts_at' => 'nullable|date',
        ]);

        Lesson::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
        ]);

        return redirect()->route('courses.show', $course->id);
    }

    // Student (enrolled only) + Instructor (owner) can view lessons
    public function show(Course $course, Lesson $lesson)
    {
        // Ensure lesson belongs to the course in URL
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $user = Auth::user();

        // Instructor who owns course can view
        if ($user->role === 'instructor' && $course->instructor_id === $user->id) {
            return view('lessons.show', compact('course', 'lesson'));
        }

        // Student must be enrolled
        if ($user->role === 'student') {
            $enrolled = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->exists();

            if (!$enrolled) {
                abort(403);
            }

            return view('lessons.show', compact('course', 'lesson'));
        }

        // Admin can view everything (optional)
        if ($user->role === 'admin') {
            return view('lessons.show', compact('course', 'lesson'));
        }

        abort(403);
    }
}
