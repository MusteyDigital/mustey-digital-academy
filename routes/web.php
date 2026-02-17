<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\StudentProgressController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizQuestionController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ✅ Instructor Controllers (NEW)
use App\Http\Controllers\Instructor\CourseManageController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminEnrollmentController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminCertificateController;

Route::get('/', function () {
    return view('welcome');
});

// ✅ ONE dashboard entry point (redirect by role)
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user->role === 'instructor') {
        return redirect()->route('instructor.dashboard');
    }

    return redirect()->route('student.dashboard');
})->middleware(['auth'])->name('dashboard');


// ====================== COURSES ======================
Route::middleware(['auth'])->group(function () {
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    // Live session settings (meeting_url, starts_at)
    Route::get('/courses/{course}/session', [CourseController::class, 'editSession'])->name('courses.session.edit');
    Route::put('/courses/{course}/session', [CourseController::class, 'updateSession'])->name('courses.session.update');
});


// ====================== INSTRUCTOR COURSE MANAGEMENT (NEW for Step 4) ======================
Route::middleware(['auth'])->group(function () {

    // ✅ Uses your Gate: manage-courses (admin OR instructor)
    Route::middleware('can:manage-courses')->prefix('instructor')->name('instructor.')->group(function () {

        Route::get('/courses', [CourseManageController::class, 'index'])
            ->name('courses.index');

        Route::get('/courses/{course}/edit', [CourseManageController::class, 'edit'])
            ->name('courses.edit');

        Route::put('/courses/{course}', [CourseManageController::class, 'update'])
            ->name('courses.update');

        Route::delete('/courses/{course}', [CourseManageController::class, 'destroy'])
            ->name('courses.destroy');

    });

});


// ====================== QUIZZES ======================
Route::middleware(['auth'])->group(function () {
    Route::get('/courses/{course}/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
    Route::post('/courses/{course}/quizzes', [QuizController::class, 'store'])->name('quizzes.store');

    Route::get('/courses/{course}/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');

    Route::get('/courses/{course}/quizzes/{quiz}/questions/create', [QuizQuestionController::class, 'create'])->name('quiz-questions.create');
    Route::post('/courses/{course}/quizzes/{quiz}/questions', [QuizQuestionController::class, 'store'])->name('quiz-questions.store');

    Route::post('/courses/{course}/quizzes/{quiz}/submit', [QuizAttemptController::class, 'submit'])->name('quizzes.submit');
    Route::get('/courses/{course}/quizzes/{quiz}/result', [QuizAttemptController::class, 'result'])->name('quizzes.result');
});


// ====================== LESSONS ======================
Route::middleware(['auth'])->group(function () {
    Route::get('/courses/{course}/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::post('/courses/{course}/lessons', [LessonController::class, 'store'])->name('lessons.store');

    Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');

    Route::post('/courses/{course}/lessons/{lesson}/complete', [LessonController::class, 'complete'])
        ->name('lessons.complete');
});


// ====================== ENROLLMENT ======================
Route::middleware(['auth'])->group(function () {
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'enroll'])->name('courses.enroll');
    Route::delete('/courses/{course}/unenroll', [EnrollmentController::class, 'unenroll'])->name('courses.unenroll');
    Route::get('/my-courses', [EnrollmentController::class, 'myCourses'])->name('enrollments.my-courses');
});


// ====================== STUDENT PROGRESS ======================
Route::middleware(['auth'])->group(function () {
    Route::get('/my-progress', [StudentProgressController::class, 'index'])->name('progress.index');
    Route::get('/courses/{course}/progress', [StudentProgressController::class, 'show'])->name('progress.show');
});


// ====================== PROFILE ======================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// ====================== CERTIFICATE ======================
Route::middleware(['auth'])->group(function () {
    Route::get('/courses/{course}/certificate', [CertificateController::class, 'download'])
        ->name('certificates.download');
});


// ====================== NOTIFICATIONS ======================
Route::middleware(['auth'])->group(function () {

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])
        ->name('notifications.read');

    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])
        ->name('notifications.readAll');

});

// ✅ Public verification by token (secure)
Route::get('/certificates/verify/{token}', [CertificateController::class, 'verify'])
    ->name('certificates.verify');


// ====================== ATTENDANCE ======================
Route::middleware(['auth'])->group(function () {
    Route::post('/courses/{course}/lessons/{lesson}/attendance', [AttendanceController::class, 'store'])
        ->name('attendance.store');

    Route::get('/courses/{course}/lessons/{lesson}/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/courses/{course}/attendance/live', [AttendanceController::class, 'liveStore'])
        ->name('attendance.live.store');

    Route::get('/courses/{course}/attendance/live', [AttendanceController::class, 'liveIndex'])
        ->name('attendance.live.index');
});


// ====================== ROLE DASHBOARDS ======================
Route::get('/student/dashboard', function () {
    $courses = Auth::user()->coursesEnrolled()->get();
    return view('dashboards.student', compact('courses'));
})->middleware(['auth'])->name('student.dashboard');

Route::get('/instructor/dashboard', function () {
    $user = Auth::user();

    if ($user->role !== 'instructor') {
        abort(403);
    }

    $courses = $user->coursesTaught()->get();

    return view('dashboards.instructor', compact('courses'));
})->middleware(['auth'])->name('instructor.dashboard');


// ====================== ADMIN PANEL ======================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
    Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');

    // Courses
    Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
    Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');

    // Enrollments
    Route::get('/enrollments', [AdminEnrollmentController::class, 'index'])->name('enrollments.index');

    // Attendance
    Route::get('/attendance/lessons', [AdminAttendanceController::class, 'lessonIndex'])->name('attendance.lessons');
    Route::get('/attendance/live', [AdminAttendanceController::class, 'liveIndex'])->name('attendance.live');

    // Certificates
    Route::get('/certificates', [AdminCertificateController::class, 'index'])->name('certificates.index');
});

require __DIR__ . '/auth.php';
