<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    protected function ensureCanManageCourse(Course $course): void
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }
    }

    public function create(Request $request, Course $course)
    {
        $this->ensureCanManageCourse($course);

        $lessons = $course->lessons()->orderBy('order')->orderBy('id')->get();
        $selectedLessonId = $request->query('lesson_id');

        return view('quizzes.create', compact('course', 'lessons', 'selectedLessonId'));
    }

    public function store(Request $request, Course $course)
    {
        $this->ensureCanManageCourse($course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'lesson_id' => 'nullable|exists:lessons,id',
            'pass_mark' => 'nullable|numeric|min:0|max:100',
            'max_attempts' => 'nullable|integer|min:1',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'is_published' => 'nullable|boolean',
        ]);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'lesson_id' => $validated['lesson_id'] ?? null,
            'title' => $validated['title'],
            'pass_mark' => $validated['pass_mark'] ?? 50,
            'max_attempts' => $validated['max_attempts'] ?? null,
            'time_limit_minutes' => $validated['time_limit_minutes'] ?? null,
            'is_published' => (bool) ($validated['is_published'] ?? false),
        ]);

        return redirect()
            ->route('quizzes.show', [$course->id, $quiz->id])
            ->with('success', 'Quiz created successfully.');
    }

    public function show(Course $course, Quiz $quiz)
    {
        if ($quiz->course_id !== $course->id) {
            abort(404);
        }

        $user = auth()->user();

        if ($user && $user->role === 'student' && !$quiz->is_published) {
            return redirect()
                ->route('courses.show', $course->id)
                ->with('error', 'This quiz is not available yet.');
        }

        $quiz->load(['course', 'questions', 'attempts']);

        $remainingAttempts = null;
        $studentAttemptsCount = 0;
        $studentBestScore = null;
        $studentBestPercentage = null;

        if ($user && $user->role === 'student') {
            $studentAttempts = $quiz->attempts()
                ->where('user_id', $user->id)
                ->where('status', 'submitted')
                ->get();

            $studentAttemptsCount = $studentAttempts->count();

            if (!is_null($quiz->max_attempts)) {
                $remainingAttempts = max($quiz->max_attempts - $studentAttemptsCount, 0);
            }

            if ($studentAttempts->isNotEmpty()) {
                $bestAttempt = $studentAttempts->sortByDesc(function ($attempt) {
                    return $attempt->percentage ?? 0;
                })->first();

                $studentBestScore = $bestAttempt->score ?? null;

                if (!is_null($bestAttempt->percentage)) {
                    $studentBestPercentage = $bestAttempt->percentage;
                } elseif (
                    isset($bestAttempt->score) &&
                    isset($bestAttempt->total) &&
                    (int) $bestAttempt->total > 0
                ) {
                    $studentBestPercentage = ($bestAttempt->score / $bestAttempt->total) * 100;
                }
            }
        }

        return view('quizzes.show', compact(
            'course',
            'quiz',
            'remainingAttempts',
            'studentAttemptsCount',
            'studentBestScore',
            'studentBestPercentage'
        ));
    }

    public function togglePublish(Course $course, Quiz $quiz)
    {
        $this->ensureCanManageCourse($course);

        if ($quiz->course_id !== $course->id) {
            abort(404);
        }

        $quiz->update([
            'is_published' => !$quiz->is_published,
        ]);

        return redirect()
            ->route('quizzes.show', [$course->id, $quiz->id])
            ->with('success', $quiz->is_published ? 'Quiz published successfully.' : 'Quiz unpublished successfully.');
    }

    public function edit(Course $course, Quiz $quiz)
    {
        $this->ensureCanManageCourse($course);

        if ($quiz->course_id !== $course->id) {
            abort(404);
        }

        $lessons = $course->lessons()->orderBy('order')->orderBy('id')->get();

        return view('quizzes.edit', compact('course', 'quiz', 'lessons'));
    }

    public function update(Request $request, Course $course, Quiz $quiz)
    {
        $this->ensureCanManageCourse($course);

        if ($quiz->course_id !== $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'lesson_id' => 'nullable|exists:lessons,id',
            'pass_mark' => 'nullable|numeric|min:0|max:100',
            'max_attempts' => 'nullable|integer|min:1',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'is_published' => 'nullable|boolean',
        ]);

        $quiz->update([
            'title' => $validated['title'],
            'lesson_id' => $validated['lesson_id'] ?? null,
            'pass_mark' => $validated['pass_mark'] ?? $quiz->pass_mark,
            'max_attempts' => $validated['max_attempts'] ?? null,
            'time_limit_minutes' => $validated['time_limit_minutes'] ?? null,
            'is_published' => (bool) ($validated['is_published'] ?? false),
        ]);

        return redirect()
            ->route('quizzes.show', [$course->id, $quiz->id])
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Course $course, Quiz $quiz)
    {
        $this->ensureCanManageCourse($course);

        if ($quiz->course_id !== $course->id) {
            abort(404);
        }

        $quiz->delete();

        return redirect()
            ->route('courses.show', $course->id)
            ->with('success', 'Quiz deleted successfully.');
    }
}
