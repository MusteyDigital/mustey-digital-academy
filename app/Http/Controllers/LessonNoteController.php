<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonNoteController extends Controller
{
    public function store(Request $request, Course $course, Lesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            abort(403);
        }

        $enrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if (!$enrolled) {
            abort(403);
        }

        $validated = $request->validate([
            'note' => ['nullable', 'string'],
        ]);

        LessonNote::updateOrCreate(
            [
                'lesson_id' => $lesson->id,
                'user_id' => $user->id,
            ],
            [
                'note' => $validated['note'] ?? '',
            ]
        );

        return redirect()
            ->route('lessons.show', [$course->id, $lesson->id])
            ->with('success', 'Your lesson note has been saved.');
    }
}
