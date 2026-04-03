<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleLessonController extends Controller
{
    public function index(Course $course, Module $module)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        if ($module->course_id !== $course->id) {
            abort(404);
        }

        $lessons = $module->lessons()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('instructor.modules.lessons', [
            'course' => $course,
            'module' => $module,
            'lessons' => $lessons,
        ]);
    }

    public function store(Request $request, Course $course, Module $module)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        if ($module->course_id !== $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:50'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:1000'],
            'starts_at' => ['nullable', 'date'],
            'order' => ['nullable', 'integer', 'min:0'],
            'enable_drab' => ['nullable', 'boolean'],
        ]);

        Lesson::create([
            'course_id' => $course->id,
            'module_id' => $module->id,
            'title' => $validated['title'],
            'duration' => $validated['duration'] ?? null,
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'order' => $validated['order'] ?? 0,
            'enable_drab' => $request->boolean('enable_drab'),
        ]);

        return redirect()
            ->route('instructor.modules.lessons.index', [$course->id, $module->id])
            ->with('success', 'Lesson added successfully.');
    }

    public function update(Request $request, Course $course, Module $module, Lesson $lesson)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        if ($module->course_id !== $course->id || $lesson->course_id !== $course->id || $lesson->module_id !== $module->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:50'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:1000'],
            'starts_at' => ['nullable', 'date'],
            'order' => ['nullable', 'integer', 'min:0'],
            'enable_drab' => ['nullable', 'boolean'],
        ]);

        $lesson->update([
            'title' => $validated['title'],
            'duration' => $validated['duration'] ?? null,
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'order' => $validated['order'] ?? 0,
            'enable_drab' => $request->boolean('enable_drab'),
        ]);

        return redirect()
            ->route('instructor.modules.lessons.index', [$course->id, $module->id])
            ->with('success', 'Lesson updated successfully.');
    }

    public function destroy(Course $course, Module $module, Lesson $lesson)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        if ($module->course_id !== $course->id || $lesson->course_id !== $course->id || $lesson->module_id !== $module->id) {
            abort(404);
        }

        $lesson->delete();

        return redirect()
            ->route('instructor.modules.lessons.index', [$course->id, $module->id])
            ->with('success', 'Lesson deleted successfully.');
    }
}
