<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;

class QuizQuestionController extends Controller
{
    protected function ensureCanManageQuiz(Course $course, Quiz $quiz): void
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
    }

    protected function ensureQuestionBelongsToQuiz(Quiz $quiz, QuizQuestion $question): void
    {
        if ($question->quiz_id !== $quiz->id) {
            abort(404);
        }
    }

    public function create(Course $course, Quiz $quiz)
    {
        $this->ensureCanManageQuiz($course, $quiz);

        return view('quizzes.questions.create', compact('course', 'quiz'));
    }

    public function store(Request $request, Course $course, Quiz $quiz)
    {
        $this->ensureCanManageQuiz($course, $quiz);

        $validated = $request->validate([
            'question' => ['required', 'string'],
            'option_a' => ['required', 'string'],
            'option_b' => ['required', 'string'],
            'option_c' => ['required', 'string'],
            'option_d' => ['required', 'string'],
            'correct_option' => ['required', 'in:a,b,c,d'],
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

    public function edit(Course $course, Quiz $quiz, QuizQuestion $question)
    {
        $this->ensureCanManageQuiz($course, $quiz);
        $this->ensureQuestionBelongsToQuiz($quiz, $question);

        return view('quizzes.questions.edit', compact('course', 'quiz', 'question'));
    }

    public function update(Request $request, Course $course, Quiz $quiz, QuizQuestion $question)
    {
        $this->ensureCanManageQuiz($course, $quiz);
        $this->ensureQuestionBelongsToQuiz($quiz, $question);

        $validated = $request->validate([
            'question' => ['required', 'string'],
            'option_a' => ['required', 'string'],
            'option_b' => ['required', 'string'],
            'option_c' => ['required', 'string'],
            'option_d' => ['required', 'string'],
            'correct_option' => ['required', 'in:a,b,c,d'],
        ]);

        $question->update($validated);

        return redirect()
            ->route('quizzes.show', [$course->id, $quiz->id])
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Course $course, Quiz $quiz, QuizQuestion $question)
    {
        $this->ensureCanManageQuiz($course, $quiz);
        $this->ensureQuestionBelongsToQuiz($quiz, $question);

        $question->delete();

        return redirect()
            ->route('quizzes.show', [$course->id, $quiz->id])
            ->with('success', 'Question deleted successfully.');
    }
}
