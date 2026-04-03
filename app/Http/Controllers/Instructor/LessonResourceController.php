<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonResource;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LessonResourceController extends Controller
{
    public function index(Course $course, Module $module, Lesson $lesson)
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

        $resources = $lesson->resources()->latest()->get();

        return view('instructor.lessons.resources', [
            'course' => $course,
            'module' => $module,
            'lesson' => $lesson,
            'resources' => $resources,
        ]);
    }

    public function store(Request $request, Course $course, Module $module, Lesson $lesson)
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
            'file' => ['required', 'file', 'max:20480'],
        ]);

        $file = $validated['file'];
        $path = $file->store('lesson-resources', 'public');

        LessonResource::create([
            'lesson_id' => $lesson->id,
            'title' => $validated['title'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'download_count' => 0,
        ]);

        return redirect()
            ->route('instructor.modules.lessons.resources.index', [$course->id, $module->id, $lesson->id])
            ->with('success', 'Resource uploaded successfully.');
    }

    public function destroy(Course $course, Module $module, Lesson $lesson, LessonResource $resource)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        if (
            $module->course_id !== $course->id ||
            $lesson->course_id !== $course->id ||
            $lesson->module_id !== $module->id ||
            $resource->lesson_id !== $lesson->id
        ) {
            abort(404);
        }

        if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return redirect()
            ->route('instructor.modules.lessons.resources.index', [$course->id, $module->id, $lesson->id])
            ->with('success', 'Resource deleted successfully.');
    }
}
