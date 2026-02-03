<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizQuestionController extends Controller
{
    private function ensureCanManageQuiz(Course $course, Quiz $quiz): void
    {
        $user = Auth::user();

        if (!in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        // If instructor, must own the course
        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        // Quiz must belong to course
        if ($quiz->course_id !== $course->id) {
            abort(404);
        }
    }

    // Instructor/Admin: add question form
    public function create(Course $course, Quiz $quiz)
    {
        $this->ensureCanManageQuiz($course, $quiz);

        // IMPORTANT: match your blade path
        // If your file is resources/views/quizzes/questions/create.blade.php:
        return view('quizzes.questions.create', compact('course', 'quiz'));

        // If your file is resources/views/quiz-questions/create.blade.php instead, use:
        // return view('quiz-questions.create', compact('course', 'quiz'));
    }

    // Instructor/Admin: store question
    public function store(Request $request, Course $course, Quiz $quiz)
    {
        $this->ensureCanManageQuiz($course, $quiz);

        $validated = $request->validate([
            'question' => 'required|string',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'required|string|max:255',
            'option_d' => 'required|string|max:255',
            'correct_option' => 'required|in:a,b,c,d',
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => $validated['question'],
            'option_a' => $validated['option_a'],
            'option_b' => $validated['option_b'],
            'option_c' => $validated['option_c'],
            'option_d' => $validated['option_d'],
            'correct_option' => $validated['correct_option'],
        ]);

        return redirect()
            ->route('quizzes.show', [$course->id, $quiz->id])
            ->with('success', 'Question added successfully.');
    }
}
