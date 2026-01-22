<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizQuestionController extends Controller
{
    // Instructor: add question form
    public function create(Course $course, Quiz $quiz)
    {
        $user = Auth::user();

        if ($user->role !== 'instructor') abort(403);
        if ($course->instructor_id !== $user->id) abort(403);
        if ($quiz->course_id !== $course->id) abort(404);

        return view('quizzes.questions.create', compact('course', 'quiz'));
    }

    // Instructor: store question
    public function store(Request $request, Course $course, Quiz $quiz)
    {
        $user = Auth::user();

        if ($user->role !== 'instructor') abort(403);
        if ($course->instructor_id !== $user->id) abort(403);
        if ($quiz->course_id !== $course->id) abort(404);

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

        return redirect()->route('quizzes.show', [$course->id, $quiz->id]);
    }
}
