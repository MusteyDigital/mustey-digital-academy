<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LessonResourceDownloadController extends Controller
{
    public function download(Course $course, Lesson $lesson, LessonResource $resource)
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        if ($resource->lesson_id !== $lesson->id) {
            abort(404);
        }

        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $allowed = false;

        if ($user->role === 'admin') {
            $allowed = true;
        }

        if ($user->role === 'instructor' && $course->instructor_id === $user->id) {
            $allowed = true;
        }

        if ($user->role === 'student') {
            $enrolled = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->exists();

            if ($enrolled) {
                $allowed = true;
            }
        }

        if (!$allowed) {
            abort(403);
        }

        if (!$resource->file_path || !Storage::disk('public')->exists($resource->file_path)) {
            abort(404, 'File not found.');
        }

        $resource->increment('download_count');

        return Storage::disk('public')->download(
            $resource->file_path,
            $resource->file_name ?: basename($resource->file_path)
        );
    }
}
