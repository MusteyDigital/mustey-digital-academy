<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function show(Request $request, $courseId, $lessonId)
    {
        $enrolled = Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $courseId)
            ->exists();

        if (!$enrolled) {
            return response()->json([
                'message' => 'You are not enrolled in this course.',
            ], 403);
        }

        $lesson = Lesson::where('id', $lessonId)
            ->where('course_id', $courseId)
            ->with('module')
            ->firstOrFail();

        $completed = LessonCompletion::where('user_id', $request->user()->id)
            ->where('lesson_id', $lessonId)
            ->exists();

        return response()->json([
            'lesson' => $lesson,
            'completed' => $completed,
        ]);
    }

    public function complete(Request $request, $lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);

        $enrolled = Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $lesson->course_id)
            ->exists();

        if (!$enrolled) {
            return response()->json([
                'message' => 'You are not enrolled in this course.',
            ], 403);
        }

        LessonCompletion::firstOrCreate([
            'user_id' => $request->user()->id,
            'lesson_id' => $lessonId,
        ], [
            'completed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Lesson marked as complete.',
        ]);
    }
}