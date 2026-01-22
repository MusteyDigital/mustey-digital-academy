<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizAttemptController extends Controller
{
    // Student: submit answers and score
    public function submit(Request $request, Course $course, Quiz $quiz)
    {
        $user = Auth::user();

        if ($user->role !== 'student') abort(403);
        if ($quiz->course_id !== $course->id) abort(404);

        $enrolled = $user->coursesEnrolled()->where('courses.id', $course->id)->exists();
        if (!$enrolled) abort(403);

        $quiz->load('questions');

        // prevent multiple attempts for now
        $existing = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return redirect()->route('quizzes.result', [$course->id, $quiz->id]);
        }

        $answers = $request->input('answers', []);
        $score = 0;
        $total = $quiz->questions->count();

        foreach ($quiz->questions as $q) {
            $chosen = $answers[$q->id] ?? null;
            if ($chosen && $chosen === $q->correct_option) {
                $score++;
            }
        }

        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'score' => $score,
            'total' => $total,
            'taken_at' => now(),
        ]);

        return redirect()->route('quizzes.result', [$course->id, $quiz->id]);
    }

    // Student: view result
    public function result(Course $course, Quiz $quiz)
    {
        $user = Auth::user();

        if ($user->role !== 'student') abort(403);
        if ($quiz->course_id !== $course->id) abort(404);

        $enrolled = $user->coursesEnrolled()->where('courses.id', $course->id)->exists();
        if (!$enrolled) abort(403);

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return view('quizzes.result', compact('course', 'quiz', 'attempt'));
    }
}
