<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use Illuminate\Http\Request;

class QuizAttemptController extends Controller
{
    public function start(Course $course, Quiz $quiz)
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'student') {
            abort(403);
        }

        if ($quiz->course_id !== $course->id) {
            abort(404);
        }

        if (!$quiz->is_published) {
            return redirect()
                ->route('quizzes.show', [$course->id, $quiz->id])
                ->with('error', 'This quiz is not published yet.');
        }

        $existingAttemptsCount = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->count();

        if (!is_null($quiz->max_attempts) && $existingAttemptsCount >= $quiz->max_attempts) {
            return redirect()
                ->route('quizzes.show', [$course->id, $quiz->id])
                ->with('error', 'You have reached the maximum number of attempts for this quiz.');
        }

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'score' => 0,
            'total' => $quiz->questions()->count(),
            'status' => 'in_progress',
            'started_at' => now(),
            'submitted_at' => null,
            'percentage' => 0,
        ]);

        return redirect()->to(route('quizzes.show', [$course->id, $quiz->id]) . '?attempt=' . $attempt->id);
    }

    public function submit(Request $request, Course $course, Quiz $quiz)
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'student') {
            abort(403);
        }

        if ($quiz->course_id !== $course->id) {
            abort(404);
        }

        $attemptId = $request->input('attempt_id');

        $attempt = QuizAttempt::where('id', $attemptId)
            ->where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($attempt->status === 'submitted') {
            return redirect()
                ->route('quizzes.result', [$course->id, $quiz->id])
                ->with('error', 'This attempt has already been submitted.');
        }

        if (!is_null($quiz->time_limit_minutes) && $attempt->started_at) {
            $elapsedMinutes = $attempt->started_at->diffInMinutes(now());

            if ($elapsedMinutes > $quiz->time_limit_minutes) {
                return redirect()
                    ->route('quizzes.show', [$course->id, $quiz->id])
                    ->with('error', 'Time limit exceeded.');
            }
        }

        $questions = $quiz->questions()->get();
        $score = 0;
        $totalQuestions = $questions->count();

        foreach ($questions as $question) {
            $submittedAnswer = $request->input('answers.' . $question->id);

            $isCorrect = ((string) $submittedAnswer === (string) $question->correct_option);

            if ($isCorrect) {
                $score++;
            }

            QuizAttemptAnswer::updateOrCreate(
                [
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ],
                [
                    'selected_option' => $submittedAnswer,
                    'is_correct' => $isCorrect,
                ]
            );
        }

        $percentage = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100, 2) : 0;

        $attempt->update([
            'score' => $score,
            'total' => $totalQuestions,
            'status' => 'submitted',
            'submitted_at' => now(),
            'percentage' => $percentage,
        ]);

        return redirect()->route('quizzes.result', [$course->id, $quiz->id]);
    }

    public function result(Course $course, Quiz $quiz)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$attempt) {
            return redirect()
                ->route('quizzes.show', [$course->id, $quiz->id])
                ->with('error', 'No quiz attempt found.');
        }

        $totalQuestions = max($quiz->questions()->count(), 1);
        $percentage = $attempt->percentage ?? round((($attempt->score ?? 0) / $totalQuestions) * 100, 2);
        $passed = $percentage >= ($quiz->pass_mark ?? 0);

        return view('quizzes.result', compact(
            'course',
            'quiz',
            'attempt',
            'percentage',
            'passed',
            'totalQuestions'
        ));
    }

    public function history(Course $course, Quiz $quiz)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('quizzes.attempts.index', compact('course', 'quiz', 'attempts'));
    }

    public function reviewAttempt(Course $course, Quiz $quiz, QuizAttempt $attempt)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        if ($quiz->course_id !== $course->id) {
            abort(404);
        }

        if ($attempt->quiz_id !== $quiz->id) {
            abort(404);
        }

        if ($user->role === 'student' && $attempt->user_id !== $user->id) {
            abort(403);
        }

        $attempt->load(['answers.question']);

        return view('quizzes.review', compact('course', 'quiz', 'attempt'));
    }
}
