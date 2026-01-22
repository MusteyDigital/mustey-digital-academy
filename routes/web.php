<?php

use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizQuestionController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->role === 'admin') {
        return view('dashboards.admin');
    } elseif ($user->role === 'instructor') {
        $courses = $user->coursesTaught()->get();
        return view('dashboards.instructor', compact('courses'));
    } else { // student
        $courses = $user->coursesEnrolled;
        return view('dashboards.student', compact('courses'));
    }
    Route::delete('/courses/{course}/unenroll', [EnrollmentController::class, 'unenroll'])->name('courses.unenroll');

})->middleware(['auth'])->name('dashboard');

// Courses routes
Route::middleware(['auth'])->group(function () {
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/session', [CourseController::class, 'editSession'])->name('courses.session.edit');
    Route::put('/courses/{course}/session', [CourseController::class, 'updateSession'])->name('courses.session.update');

});

Route::middleware(['auth'])->group(function () {

    // Instructor create quiz
    Route::get('/courses/{course}/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
    Route::post('/courses/{course}/quizzes', [QuizController::class, 'store'])->name('quizzes.store');

    // Show quiz: instructor manage, student take
    Route::get('/courses/{course}/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');

    // Instructor add questions
    Route::get('/courses/{course}/quizzes/{quiz}/questions/create', [QuizQuestionController::class, 'create'])->name('quiz-questions.create');
    Route::post('/courses/{course}/quizzes/{quiz}/questions', [QuizQuestionController::class, 'store'])->name('quiz-questions.store');

    // Student submit + view result
    Route::post('/courses/{course}/quizzes/{quiz}/submit', [QuizAttemptController::class, 'submit'])->name('quizzes.submit');
    Route::get('/courses/{course}/quizzes/{quiz}/result', [QuizAttemptController::class, 'result'])->name('quizzes.result');
});


Route::middleware(['auth'])->group(function () {
    // Instructor creates lessons
    Route::get('/courses/{course}/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::post('/courses/{course}/lessons', [LessonController::class, 'store'])->name('lessons.store');

    // View a lesson (enrolled students / owner instructor)
    Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    Route::post('/courses/{course}/lessons/{lesson}/complete', [LessonController::class, 'complete'])
    ->name('lessons.complete');
    Route::post('/courses/{course}/lessons/{lesson}/complete', [LessonController::class, 'complete'])
    ->name('lessons.complete');


});


// Student enrollment routes
Route::get('/student/dashboard', function () {
    $courses = Auth::user()->coursesEnrolled;
    return view('dashboards.student', compact('courses'));
})->middleware(['auth'])->name('student.dashboard');

// Student enrollment routes
Route::middleware(['auth'])->group(function () {
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'enroll'])->name('courses.enroll');
    Route::delete('/courses/{course}/unenroll', [EnrollmentController::class, 'unenroll'])->name('courses.unenroll');
    Route::get('/my-courses', [EnrollmentController::class, 'myCourses'])->name('enrollments.my-courses');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin/dashboard', function () {
    return view('dashboards.admin');
})->middleware(['auth'])->name('admin.dashboard');

Route::get('/instructor/dashboard', function () {
    $user = Auth::user();

    if ($user->role !== 'instructor') {
        abort(403);
    }

    $courses = $user->coursesTaught()->get();

    return view('dashboards.instructor', compact('courses'));
})->middleware(['auth'])->name('instructor.dashboard');

require __DIR__.'/auth.php';
