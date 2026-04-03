<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class QuizAnalyticsController extends Controller
{
    public function show(Course $course, Quiz $quiz)
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        if ($quiz->course_id !== $course->id) {
            abort(404);
        }

        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('status', 'submitted')
            ->latest()
            ->get();

        $totalAttempts = $attempts->count();
        $uniqueStudents = $attempts->pluck('user_id')->unique()->count();
        $averageScore = $totalAttempts > 0 ? round($attempts->avg('score'), 2) : 0;
        $averagePercentage = $totalAttempts > 0 ? round($attempts->avg('percentage'), 2) : 0;

        $passMark = $quiz->pass_mark ?? 0;

        $passedCount = $attempts->filter(function ($attempt) use ($passMark) {
            return ($attempt->percentage ?? 0) >= $passMark;
        })->count();

        $failedCount = $totalAttempts - $passedCount;
        $passRate = $totalAttempts > 0 ? round(($passedCount / $totalAttempts) * 100, 2) : 0;

        $recentAttempts = QuizAttempt::with('user')
            ->where('quiz_id', $quiz->id)
            ->where('status', 'submitted')
            ->latest()
            ->take(20)
            ->get();

        return view('instructor.quizzes.analytics', compact(
            'course',
            'quiz',
            'totalAttempts',
            'uniqueStudents',
            'averageScore',
            'averagePercentage',
            'passedCount',
            'failedCount',
            'passRate',
            'recentAttempts'
        ));
    }
}
