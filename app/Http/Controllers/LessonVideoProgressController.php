<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonVideoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonVideoProgressController extends Controller
{
    public function store(Request $request, Course $course, Lesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            return response()->json(['ok' => false, 'message' => 'Lesson not in course'], 404);
        }

        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        $enrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if (!$enrolled) {
            return response()->json(['ok' => false, 'message' => 'Not enrolled'], 403);
        }

        $validated = $request->validate([
            'watched_seconds' => ['required', 'integer', 'min:0'],
        ]);

        $progress = LessonVideoProgress::updateOrCreate(
            [
                'lesson_id' => $lesson->id,
                'user_id' => $user->id,
            ],
            [
                'watched_seconds' => $validated['watched_seconds'],
            ]
        );

        return response()->json([
            'ok' => true,
            'id' => $progress->id,
            'watched_seconds' => $progress->watched_seconds,
        ]);
    }
}
