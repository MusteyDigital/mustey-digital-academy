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

        // instructor OR admin (optional)
        if (!in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        // If instructor, must own the course
        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        return view('quizzes.create', compact('course'));
    }

    // Instructor: store quiz
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
        ]);

        return redirect()
            ->route('quizzes.show', [$course->id, $quiz->id])
            ->with('success', 'Quiz created successfully. Now add questions.');
    }

    // Show quiz (Instructor owner can manage; Student enrolled can take)
    public function show(Course $course, Quiz $quiz)
    {
        if ($quiz->course_id !== $course->id) {
            abort(404);
        }

        $quiz->load('questions');
        $user = Auth::user();

        // Instructor owner: manage
        if ($user->role === 'instructor' && $course->instructor_id === $user->id) {
            return view('quizzes.show', compact('course', 'quiz'));
        }

        // Student enrolled: take
        if ($user->role === 'student') {
            $enrolled = $user->coursesEnrolled()->where('courses.id', $course->id)->exists();
            if (!$enrolled) abort(403);

            return view('quizzes.take', compact('course', 'quiz'));
        }

        // Admin: manage
        if ($user->role === 'admin') {
            return view('quizzes.show', compact('course', 'quiz'));
        }

        abort(403);
    }
    
}
