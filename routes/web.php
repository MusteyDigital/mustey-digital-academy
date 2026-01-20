<?php

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
})->middleware(['auth'])->name('dashboard');

// Courses routes
Route::middleware(['auth'])->group(function () {
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
});

Route::middleware(['auth'])->group(function () {
    // Instructor creates lessons
    Route::get('/courses/{course}/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::post('/courses/{course}/lessons', [LessonController::class, 'store'])->name('lessons.store');

    // View a lesson (enrolled students / owner instructor)
    Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
});


// Student enrollment routes
Route::get('/student/dashboard', function () {
    $courses = Auth::user()->coursesEnrolled;
    return view('dashboards.student', compact('courses'));
})->middleware(['auth'])->name('student.dashboard');

// Student enrollment routes
Route::middleware(['auth'])->group(function () {
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'enroll'])->name('courses.enroll');
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
    return view('dashboards.instructor');
})->middleware(['auth'])->name('instructor.dashboard');

require __DIR__.'/auth.php';
