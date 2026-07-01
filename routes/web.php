<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\StudentProgressController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\DrabController;
use App\Http\Controllers\QuizQuestionController;
use App\Http\Controllers\QuizAttemptController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaystackWebhookController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LiveSessionController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LessonResourceDownloadController;
use App\Http\Controllers\LessonNoteController;
use App\Http\Controllers\LessonVideoProgressController;
use App\Http\Controllers\LessonAssignmentController;
use App\Http\Controllers\AssignmentSubmissionController;
use App\Http\Controllers\LessonDiscussionController;
use App\Http\Controllers\CourseChatController;

// Instructor Controllers
use App\Http\Controllers\Instructor\CourseManageController;
use App\Http\Controllers\Instructor\ModuleController;
use App\Http\Controllers\Instructor\ModuleLessonController;
use App\Http\Controllers\Instructor\SortController;
use App\Http\Controllers\Instructor\QuizAnalyticsController;
use App\Http\Controllers\Instructor\LessonResourceController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminEnrollmentController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminCertificateController;

Route::get('/auth/token-login', function (\Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $redirect = $request->query('redirect', '/');
    $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
    $user = $tokenModel->tokenable;
    Auth::login($user);
    return redirect($redirect);
})->name('auth.token-login');

Route::get('/', [HomeController::class, 'index'])->name('home');

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
Route::post('/paystack/webhook', [PaystackWebhookController::class, 'handle'])->name('paystack.webhook');

Route::middleware(['auth'])->group(function () {
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::post('/courses/{course}/live-sessions/start', [LiveSessionController::class, 'start'])->name('live-sessions.start');
    Route::get('/live-sessions/{liveSession}', [LiveSessionController::class, 'show'])->name('live-sessions.show');
    Route::post('/live-sessions/{liveSession}/end', [LiveSessionController::class, 'end'])->name('live-sessions.end');

    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    Route::get('/courses/{course}/session', [CourseController::class, 'editSession'])->name('courses.session.edit');
    Route::put('/courses/{course}/session', [CourseController::class, 'updateSession'])->name('courses.session.update');

    Route::post('/courses/{course}/chat', [CourseChatController::class, 'store'])
        ->name('courses.chat.store');

    Route::delete('/courses/{course}/chat/{message}', [CourseChatController::class, 'destroy'])
        ->name('courses.chat.destroy');

    Route::post('/courses/{course}/chat/{message}/pin', [CourseChatController::class, 'pin'])
        ->name('courses.chat.pin');
});

// ====================== INSTRUCTOR PANEL ======================
Route::middleware(['auth'])
    ->prefix('instructor')
    ->name('instructor.')
    ->group(function () {

        Route::get('/dashboard', [CourseManageController::class, 'index'])
            ->name('dashboard');

        Route::middleware('can:manage-courses')->group(function () {

            Route::get('/courses', [CourseManageController::class, 'index'])->name('courses.index');
            Route::get('/courses/{course}/edit', [CourseManageController::class, 'edit'])->name('courses.edit');
            Route::put('/courses/{course}', [CourseManageController::class, 'update'])->name('courses.update');
            Route::delete('/courses/{course}', [CourseManageController::class, 'destroy'])->name('courses.destroy');

            Route::get('/courses/{course}/modules', [ModuleController::class, 'index'])->name('modules.index');
            Route::post('/courses/{course}/modules', [ModuleController::class, 'store'])->name('modules.store');
            Route::put('/courses/{course}/modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
            Route::delete('/courses/{course}/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');

            Route::get('/courses/{course}/modules/{module}/lessons', [ModuleLessonController::class, 'index'])
                ->name('modules.lessons.index');

            Route::post('/courses/{course}/modules/{module}/lessons', [ModuleLessonController::class, 'store'])
                ->name('modules.lessons.store');

            Route::put('/courses/{course}/modules/{module}/lessons/{lesson}', [ModuleLessonController::class, 'update'])
                ->name('modules.lessons.update');

            Route::delete('/courses/{course}/modules/{module}/lessons/{lesson}', [ModuleLessonController::class, 'destroy'])
                ->name('modules.lessons.destroy');

            Route::get('/courses/{course}/modules/{module}/lessons/{lesson}/resources', [LessonResourceController::class, 'index'])
                ->name('modules.lessons.resources.index');

            Route::post('/courses/{course}/modules/{module}/lessons/{lesson}/resources', [LessonResourceController::class, 'store'])
                ->name('modules.lessons.resources.store');

            Route::delete('/courses/{course}/modules/{module}/lessons/{lesson}/resources/{resource}', [LessonResourceController::class, 'destroy'])
                ->name('modules.lessons.resources.destroy');

            Route::post('/courses/{course}/sort/modules', [SortController::class, 'modules'])->name('sort.modules');
            Route::post('/courses/{course}/sort/lessons', [SortController::class, 'lessons'])->name('sort.lessons');

            Route::get('/courses/{course}/quizzes/{quiz}/analytics', [QuizAnalyticsController::class, 'show'])
                ->name('quizzes.analytics');
        });
    });

// ====================== QUIZZES ======================
Route::middleware(['auth'])->group(function () {
    Route::get('/courses/{course}/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
    Route::post('/courses/{course}/quizzes', [QuizController::class, 'store'])->name('quizzes.store');

    Route::get('/courses/{course}/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::get('/lessons/{lesson}/drab', [DrabController::class, 'index'])->name('drab.index');
    Route::post('/lessons/{lesson}/drab/submit', [DrabController::class, 'submit'])->name('drab.submit');
    Route::get('/courses/{course}/quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('/courses/{course}/quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
    Route::delete('/courses/{course}/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');

Route::post('/courses/{course}/quizzes/{quiz}/toggle-publish', [QuizController::class, 'togglePublish'])
    ->name('quizzes.toggle-publish');

    Route::get('/courses/{course}/quizzes/{quiz}/questions/create', [QuizQuestionController::class, 'create'])
        ->name('quiz-questions.create');

    Route::post('/courses/{course}/quizzes/{quiz}/questions', [QuizQuestionController::class, 'store'])
        ->name('quiz-questions.store');

    Route::get('/courses/{course}/quizzes/{quiz}/questions/{question}/edit', [QuizQuestionController::class, 'edit'])
    ->name('quiz-questions.edit');

    Route::put('/courses/{course}/quizzes/{quiz}/questions/{question}', [QuizQuestionController::class, 'update'])
    ->name('quiz-questions.update');

    Route::delete('/courses/{course}/quizzes/{quiz}/questions/{question}', [QuizQuestionController::class, 'destroy'])
    ->name('quiz-questions.destroy');


    Route::post('/courses/{course}/quizzes/{quiz}/start', [QuizAttemptController::class, 'start'])
        ->name('quizzes.start');

    Route::post('/courses/{course}/quizzes/{quiz}/submit', [QuizAttemptController::class, 'submit'])
        ->name('quizzes.submit');

    Route::get('/courses/{course}/quizzes/{quiz}/result', [QuizAttemptController::class, 'result'])
        ->name('quizzes.result');

    Route::get('/courses/{course}/quizzes/{quiz}/attempts', [QuizAttemptController::class, 'history'])
        ->name('quizzes.attempts');

    Route::get('/courses/{course}/quizzes/{quiz}/attempts/{attempt}', [QuizAttemptController::class, 'reviewAttempt'])
        ->name('quizzes.attempts.review');
});

// ====================== LESSONS ======================
Route::middleware(['auth'])->group(function () {
    Route::get('/courses/{course}/lessons/create', [LessonController::class, 'create'])
        ->name('lessons.create');

    Route::post('/courses/{course}/lessons', [LessonController::class, 'store'])
        ->name('lessons.store');

    Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])
        ->name('lessons.show');

    Route::post('/courses/{course}/lessons/{lesson}/complete', [LessonController::class, 'complete'])
        ->name('lessons.complete');

    Route::get('/courses/{course}/lessons/{lesson}/resources/{resource}/download', [LessonResourceDownloadController::class, 'download'])
        ->name('lesson-resources.download');

    Route::post('/courses/{course}/lessons/{lesson}/notes', [LessonNoteController::class, 'store'])
        ->name('lessons.notes.store');

    Route::post('/courses/{course}/lessons/{lesson}/video-progress', [LessonVideoProgressController::class, 'store'])
        ->name('lessons.video-progress.store');

    Route::post('/courses/{course}/lessons/{lesson}/discussion', [LessonDiscussionController::class, 'store'])
        ->name('lessons.discussion.store');

    Route::delete('/courses/{course}/lessons/{lesson}/discussion/{message}', [LessonDiscussionController::class, 'destroy'])
        ->name('lessons.discussion.destroy');

    Route::post('/courses/{course}/lessons/{lesson}/discussion/{message}/pin', [LessonDiscussionController::class, 'pin'])
        ->name('lessons.discussion.pin');

    Route::post('/courses/{course}/lessons/{lesson}/comments', [\App\Http\Controllers\LessonCommentController::class, 'store'])
        ->name('lessons.comments.store');

    Route::delete('/courses/{course}/lessons/{lesson}/comments/{comment}', [\App\Http\Controllers\LessonCommentController::class, 'destroy'])
        ->name('lessons.comments.destroy');

    Route::post('/courses/{course}/lessons/{lesson}/discussion/{message}/mark-answer', [LessonDiscussionController::class, 'markAnswer'])
        ->name('lessons.discussion.mark-answer');

    Route::post('/courses/{course}/lessons/{lesson}/assignment', [LessonAssignmentController::class, 'store'])
        ->name('assignments.store');

    Route::get('/courses/{course}/lessons/{lesson}/assignment-file', [LessonAssignmentController::class, 'downloadAttachment'])
        ->name('assignments.attachment.download');

    Route::get('/courses/{course}/lessons/{lesson}/assignment-submissions', [LessonAssignmentController::class, 'submissions'])
        ->name('assignments.submissions');

    Route::post('/courses/{course}/lessons/{lesson}/assignment-submissions/{submission}/grade', [LessonAssignmentController::class, 'grade'])
        ->name('assignments.grade');

    Route::post('/courses/{course}/lessons/{lesson}/submit-assignment', [AssignmentSubmissionController::class, 'store'])
        ->name('assignments.submit');

    Route::get('/courses/{course}/lessons/{lesson}/submissions/{submission}/download', [AssignmentSubmissionController::class, 'download'])
        ->name('assignments.download');

    
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

Route::get('/certificates/verify/{token}', [CertificateController::class, 'verify'])
    ->name('certificates.verify');

// ====================== NOTIFICATIONS ======================
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
});

// ====================== ATTENDANCE ======================
Route::middleware(['auth'])->group(function () {
    Route::post('/courses/{course}/lessons/{lesson}/attendance', [AttendanceController::class, 'store'])
        ->name('attendance.store');

    Route::get('/courses/{course}/lessons/{lesson}/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::post('/courses/{course}/attendance/live', [AttendanceController::class, 'liveStore'])
        ->name('attendance.live.store');

    Route::get('/courses/{course}/attendance/live', [AttendanceController::class, 'liveIndex'])
        ->name('attendance.live.index');
});

// ====================== ROLE DASHBOARDS ======================
Route::get('/student/dashboard', function () {
    $student = Auth::user();
    $courses = $student->coursesEnrolled()->get();

    $isDataAnalysisStudent = $courses->contains(function ($course) {
        return str_contains(strtolower($course->title ?? ''), 'data analysis');
    });

    $drabAttemptsQuery = $isDataAnalysisStudent
        ? \App\Models\DrabAttempt::where('user_id', $student->id)
        : \App\Models\DrabAttempt::whereRaw('1 = 0');

    $drabTotalAttempts = (clone $drabAttemptsQuery)->count();
    $drabAverageAccuracy = $drabTotalAttempts > 0 ? (float) (clone $drabAttemptsQuery)->avg('accuracy') : 0;
    $drabBestAccuracy = $drabTotalAttempts > 0 ? (float) (clone $drabAttemptsQuery)->max('accuracy') : 0;
    $drabRecentAttempts = (clone $drabAttemptsQuery)->with('lesson')->latest()->take(5)->get();

    $drabByDifficulty = [];
    foreach (['easy', 'medium', 'hard'] as $level) {
        $levelQuery = $isDataAnalysisStudent
            ? \App\Models\DrabAttempt::where('user_id', $student->id)->where('difficulty', $level)
            : \App\Models\DrabAttempt::whereRaw('1 = 0');

        $count = (clone $levelQuery)->count();

        $drabByDifficulty[$level] = [
            'attempts' => $count,
            'average_accuracy' => $count > 0 ? (float) (clone $levelQuery)->avg('accuracy') : 0,
            'best_accuracy' => $count > 0 ? (float) (clone $levelQuery)->max('accuracy') : 0,
        ];
    }

    $drabAttemptDates = $isDataAnalysisStudent
        ? \App\Models\DrabAttempt::where('user_id', $student->id)
            ->selectRaw('DATE(created_at) as attempt_date')
            ->distinct()
            ->orderByDesc('attempt_date')
            ->pluck('attempt_date')
            ->map(fn ($date) => \Carbon\Carbon::parse($date)->toDateString())
            ->values()
            ->all()
        : [];

    $drabCurrentStreak = 0;
    $drabBestStreak = 0;

    if (!empty($drabAttemptDates)) {
        $dateSet = array_flip($drabAttemptDates);

        $cursor = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        if (isset($dateSet[$cursor]) || isset($dateSet[$yesterday])) {
            while (isset($dateSet[$cursor])) {
                $drabCurrentStreak++;
                $cursor = \Carbon\Carbon::parse($cursor)->subDay()->toDateString();
            }

            if ($drabCurrentStreak === 0 && isset($dateSet[$yesterday])) {
                $cursor = $yesterday;
                while (isset($dateSet[$cursor])) {
                    $drabCurrentStreak++;
                    $cursor = \Carbon\Carbon::parse($cursor)->subDay()->toDateString();
                }
            }
        }

        $bestRun = 0;
        $currentRun = 0;
        $previousDate = null;

        $ascendingDates = array_reverse($drabAttemptDates);

        foreach ($ascendingDates as $date) {
            $parsedDate = \Carbon\Carbon::parse($date);

            if ($previousDate === null) {
                $currentRun = 1;
            } else {
                $previousParsed = \Carbon\Carbon::parse($previousDate);
                $diff = $previousParsed->diffInDays($parsedDate, false);

                if ($diff === 1) {
                    $currentRun++;
                } else {
                    $currentRun = 1;
                }
            }

            $bestRun = max($bestRun, $currentRun);
            $previousDate = $date;
        }

        $drabBestStreak = max($bestRun, $drabCurrentStreak);
    }

    $drabXpAttempts = $isDataAnalysisStudent
        ? \App\Models\DrabAttempt::where('user_id', $student->id)->get(['difficulty', 'accuracy'])
        : collect();

    $drabTotalXp = $drabXpAttempts->sum(function ($attempt) {
        $xp = 10;

        $difficultyBonus = match ($attempt->difficulty) {
            'easy' => 2,
            'medium' => 5,
            'hard' => 8,
            default => 0,
        };

        $accuracy = (float) $attempt->accuracy;
        $accuracyBonus = $accuracy >= 100 ? 5 : ($accuracy >= 60 ? 3 : 0);

        return $xp + $difficultyBonus + $accuracyBonus;
    });

    $drabLevel = max(1, (int) floor($drabTotalXp / 100) + 1);
    $drabCurrentLevelXp = $drabTotalXp % 100;
    $drabNextLevelXp = 100;
    $drabLevelProgressPercent = (int) round(($drabCurrentLevelXp / $drabNextLevelXp) * 100);

    $leaderboardUsers = $isDataAnalysisStudent
        ? \App\Models\User::where('role', 'student')
            ->whereHas('coursesEnrolled', function ($q) {
                $q->where('title', 'like', '%data analysis%');
            })
            ->whereHas('drabAttempts')
            ->get()
            ->map(function ($leaderUser) {
                $attempts = \App\Models\DrabAttempt::where('user_id', $leaderUser->id)
                    ->get(['difficulty', 'accuracy', 'created_at']);

                $totalAttempts = $attempts->count();

                $totalXp = $attempts->sum(function ($attempt) {
                    $xp = 10;

                    $difficultyBonus = match ($attempt->difficulty) {
                        'easy' => 2,
                        'medium' => 5,
                        'hard' => 8,
                        default => 0,
                    };

                    $accuracy = (float) $attempt->accuracy;
                    $accuracyBonus = $accuracy >= 100 ? 5 : ($accuracy >= 60 ? 3 : 0);

                    return $xp + $difficultyBonus + $accuracyBonus;
                });

                $attemptDates = $attempts
                    ->map(fn ($attempt) => \Carbon\Carbon::parse($attempt->created_at)->toDateString())
                    ->unique()
                    ->sort()
                    ->values()
                    ->all();

                $bestStreak = 0;
                $currentRun = 0;
                $previousDate = null;

                foreach ($attemptDates as $date) {
                    $parsedDate = \Carbon\Carbon::parse($date);

                    if ($previousDate === null) {
                        $currentRun = 1;
                    } else {
                        $previousParsed = \Carbon\Carbon::parse($previousDate);
                        $diff = $previousParsed->diffInDays($parsedDate, false);
                        $currentRun = $diff === 1 ? $currentRun + 1 : 1;
                    }

                    $bestStreak = max($bestStreak, $currentRun);
                    $previousDate = $date;
                }

                $level = max(1, (int) floor($totalXp / 100) + 1);

                return (object) [
                    'name' => $leaderUser->name,
                    'total_xp' => $totalXp,
                    'level' => $level,
                    'best_streak' => $bestStreak,
                    'total_attempts' => $totalAttempts,
                ];
            })
            ->sort(function ($a, $b) {
                return [$b->total_xp, $b->best_streak, $b->total_attempts]
                    <=> [$a->total_xp, $a->best_streak, $a->total_attempts];
            })
            ->values()
            ->take(10)
        : collect();

    return view('dashboards.student', compact(
        'courses',
        'drabTotalAttempts',
        'drabAverageAccuracy',
        'drabBestAccuracy',
        'drabRecentAttempts',
        'drabByDifficulty',
        'drabCurrentStreak',
        'drabBestStreak',
        'drabTotalXp',
        'drabLevel',
        'drabCurrentLevelXp',
        'drabNextLevelXp',
        'drabLevelProgressPercent',
        'leaderboardUsers',
        'isDataAnalysisStudent'
    ));
})->middleware(['auth'])->name('student.dashboard');


Route::get('/practice-lab', function () {
    $student = Auth::user();

    $drabAttemptsQuery = \App\Models\DrabAttempt::where('user_id', $student->id);

    $drabTotalAttempts = (clone $drabAttemptsQuery)->count();
    $drabAverageAccuracy = $drabTotalAttempts > 0 ? (float) (clone $drabAttemptsQuery)->avg('accuracy') : 0;
    $drabBestAccuracy = $drabTotalAttempts > 0 ? (float) (clone $drabAttemptsQuery)->max('accuracy') : 0;
    $drabRecentAttempts = (clone $drabAttemptsQuery)->with('lesson.course')->latest()->take(15)->get();

    $drabLessons = \App\Models\Lesson::with('course')
        ->where('enable_drab', true)
        ->orderByDesc('id')
        ->get();

    $drabByDifficulty = [];
    foreach (['easy', 'medium', 'hard'] as $level) {
        $levelQuery = \App\Models\DrabAttempt::where('user_id', $student->id)
            ->where('difficulty', $level);

        $count = (clone $levelQuery)->count();

        $drabByDifficulty[$level] = [
            'attempts' => $count,
            'average_accuracy' => $count > 0 ? (float) (clone $levelQuery)->avg('accuracy') : 0,
            'best_accuracy' => $count > 0 ? (float) (clone $levelQuery)->max('accuracy') : 0,
        ];
    }

    return view('drab.practice-lab', compact(
        'drabTotalAttempts',
        'drabAverageAccuracy',
        'drabBestAccuracy',
        'drabRecentAttempts',
        'drabLessons',
        'drabByDifficulty'
    ));
})->middleware(['auth'])->name('practice-lab.index');

// ====================== ADMIN PANEL ======================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
    Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');

    Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses.index');
    Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');

    Route::get('/enrollments', [AdminEnrollmentController::class, 'index'])->name('enrollments.index');

    Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/lessons', [AdminAttendanceController::class, 'lessons'])->name('attendance.lessons');
    Route::get('/attendance/live', [AdminAttendanceController::class, 'live'])->name('attendance.live');
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/export/csv', [AdminPaymentController::class, 'exportCsv'])->name('payments.export.csv');
    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::put('/coupons/{coupon}', [AdminCouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');

    Route::get('/certificates', [AdminCertificateController::class, 'index'])->name('certificates.index');
});

Route::post('/drab/{lesson}/timed/start', [\App\Http\Controllers\DrabController::class, 'startTimed'])
    ->middleware(['auth'])
    ->name('drab.timed.start');

Route::post('/drab/{lesson}/timed/reset', [\App\Http\Controllers\DrabController::class, 'resetTimed'])
    ->middleware(['auth'])
    ->name('drab.timed.reset');

Route::post('/drab/{lesson}/session/start', [\App\Http\Controllers\DrabController::class, 'startAdaptiveSession'])
    ->middleware(['auth'])
    ->name('drab.session.start');

Route::post('/drab/{lesson}/session/reset', [\App\Http\Controllers\DrabController::class, 'resetAdaptiveSession'])
    ->middleware(['auth'])
    ->name('drab.session.reset');

require __DIR__ . '/auth.php';


Route::middleware(['auth'])->group(function () {
    Route::post('/courses/{course}/pay', [PaymentController::class, 'initialize'])->name('payments.initialize');
    Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payments.callback');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
    Route::get('/payments/{payment}/receipt/pdf', [PaymentController::class, 'receiptPdf'])->name('payments.receipt.pdf');
});















/*
|--------------------------------------------------------------------------
| Lesson Comments (FINAL FIX)
|--------------------------------------------------------------------------
*/

Route::post('/courses/{course}/lessons/{lesson}/comments', [\App\Http\Controllers\LessonCommentController::class, 'store'])
    ->name('lessons.comments.store');

Route::delete('/courses/{course}/lessons/{lesson}/comments/{comment}', [\App\Http\Controllers\LessonCommentController::class, 'destroy'])
    ->name('lessons.comments.destroy');

