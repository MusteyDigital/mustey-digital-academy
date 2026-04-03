<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('instructor')
            ->latest()
            ->get();

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'instructor'])) {
            abort(403);
        }

        return view('courses.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'instructor'])) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'integer', 'min:0'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'meeting_url' => ['nullable', 'url'],
            'starts_at' => ['nullable', 'date'],
        ]);

        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        Course::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'price' => (int) ($validated['price'] ?? 0),
            'thumbnail' => $thumbnailPath,
            'meeting_url' => $validated['meeting_url'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'instructor_id' => $user->id,
        ]);

        return redirect()->route('courses.index')->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        $course->load(['instructor', 'lessons', 'activeLiveSession']);

        $user = Auth::user();

        $completedLessonIds = [];
        $nextUnfinishedLesson = null;

        if ($user && $user->role === 'student') {
            $enrolled = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->exists();

            if ($enrolled) {
                $orderedLessons = $course->lessons()
                    ->orderBy('order')
                    ->orderBy('id')
                    ->get();

                $completedLessonIds = LessonCompletion::where('user_id', $user->id)
                    ->whereIn('lesson_id', $orderedLessons->pluck('id'))
                    ->pluck('lesson_id')
                    ->toArray();

                foreach ($orderedLessons as $lesson) {
                    if (!in_array($lesson->id, $completedLessonIds, true)) {
                        $nextUnfinishedLesson = $lesson;
                        break;
                    }
                }
            }
        }

        return view('courses.show', [
            'course' => $course,
            'completedLessonIds' => $completedLessonIds,
            'nextUnfinishedLesson' => $nextUnfinishedLesson,
        ]);
    }

    public function edit(Course $course)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'instructor'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'instructor'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'integer', 'min:0'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'meeting_url' => ['nullable', 'url'],
            'starts_at' => ['nullable', 'date'],
        ]);

        if ($request->hasFile('thumbnail')) {
            $course->thumbnail = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $course->title = $validated['title'];
        $course->description = $validated['description'] ?? null;
        $course->price = (int) ($validated['price'] ?? 0);
        $course->meeting_url = $validated['meeting_url'] ?? null;
        $course->starts_at = $validated['starts_at'] ?? null;
        $course->save();

        return redirect()->route('courses.show', $course->id)->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'instructor'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
    }

    public function editSession(Course $course)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'instructor'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        return view('courses.session', compact('course'));
    }

    public function updateSession(Request $request, Course $course)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'instructor'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'meeting_url' => ['nullable', 'url'],
            'starts_at' => ['nullable', 'date'],
        ]);

        $course->meeting_url = $validated['meeting_url'] ?? null;
        $course->starts_at = $validated['starts_at'] ?? null;
        $course->save();

        return redirect()->route('courses.show', $course->id)->with('success', 'Live session updated successfully.');
    }
}
