<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonDoubtMessage;
use App\Services\LessonDoubtSolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LessonDoubtController extends Controller
{
    public function store(Request $request, Course $course, Lesson $lesson, LessonDoubtSolver $solver)
    {
        $user = auth()->user();
        abort_unless($user, 403);

        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        if ($user->role === 'student') {
            $isEnrolled = $user->coursesEnrolled()
                ->where('courses.id', $course->id)
                ->exists();

            abort_unless($isEnrolled, 403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'question' => ['required', 'string', 'max:2000'],
        ]);

        // Prior turns for this student on this lesson, most recent first, capped for prompt size.
        $history = LessonDoubtMessage::where('lesson_id', $lesson->id)
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->reverse()
            ->map(fn ($m) => ['role' => $m->role, 'body' => $m->body])
            ->values()
            ->all();

        $userMessage = LessonDoubtMessage::create([
            'lesson_id' => $lesson->id,
            'course_id' => $course->id,
            'user_id' => $user->id,
            'role' => 'user',
            'body' => $data['question'],
            'status' => 'complete',
        ]);

        try {
            $answer = $solver->ask($lesson, $data['question'], $history);

            $assistantMessage = LessonDoubtMessage::create([
                'lesson_id' => $lesson->id,
                'course_id' => $course->id,
                'user_id' => $user->id,
                'role' => 'assistant',
                'body' => $answer,
                'status' => 'complete',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Lesson doubt-solver failed to answer', [
                'lesson_id' => $lesson->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            $assistantMessage = LessonDoubtMessage::create([
                'lesson_id' => $lesson->id,
                'course_id' => $course->id,
                'user_id' => $user->id,
                'role' => 'assistant',
                'body' => "Sorry, I couldn't process that just now. Please try again in a moment.",
                'status' => 'failed',
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'question' => [
                    'id' => $userMessage->id,
                    'body' => $userMessage->body,
                ],
                'answer' => [
                    'id' => $assistantMessage->id,
                    'body' => $assistantMessage->body,
                    'status' => $assistantMessage->status,
                ],
            ]);
        }

        return back();
    }

    public function index(Course $course, Lesson $lesson)
    {
        $user = auth()->user();
        abort_unless($user, 403);

        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $messages = LessonDoubtMessage::where('lesson_id', $lesson->id)
            ->where('user_id', $user->id)
            ->orderBy('created_at')
            ->get(['id', 'role', 'body', 'status', 'created_at']);

        return response()->json([
            'messages' => $messages,
        ]);
    }
}
