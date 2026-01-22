<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    // Instructor: show create quiz form
    public function create(Course $course)
    {
        $user = Auth::user();

        if ($user->role !== 'instructor') {
            abort(403);
        }

        if ($course->instructor_id !== $user->id) {
            abort(403);
        }

        return view('quizzes.create', compact('course'));
    }

    // Instructor: store quiz
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();

        if ($user->role !== 'instructor') {
            abort(403);
        }

        if ($course->instructor_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
        ]);

        return redirect()->route('quizzes.show', [$course->id, $quiz->id]);
    }

    // Show quiz (Instructor owner can manage; Student enrolled can take)
    public function show(Course $course, Quiz $quiz)
    {
        if ($quiz->course_id !== $course->id) {
            abort(404);
        }

        $quiz->load('questions');

        $user = Auth::user();

        // Instructor owner
        if ($user->role === 'instructor' && $course->instructor_id === $user->id) {
            return view('quizzes.show', compact('course', 'quiz'));
        }

        // Student enrolled
        if ($user->role === 'student') {
            $enrolled = $user->coursesEnrolled()->where('courses.id', $course->id)->exists();
            if (!$enrolled) abort(403);

            return view('quizzes.take', compact('course', 'quiz'));
        }

        // Admin optional
        if ($user->role === 'admin') {
            return view('quizzes.show', compact('course', 'quiz'));
        }

        abort(403);
    }
}
