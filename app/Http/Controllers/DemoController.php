<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LessonCompletion;
use App\Models\LessonVideoProgress;
use App\Models\LessonNote;
use App\Models\AssignmentSubmission;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\DrabAttempt;
use Illuminate\Support\Facades\Auth;

class DemoController extends Controller
{
    const DEMO_STUDENT_EMAIL    = 'demo-student@musteydigitalacademy.online';
    const DEMO_INSTRUCTOR_EMAIL = 'demo-instructor@musteydigitalacademy.online';

    public function loginAsStudent()
    {
        return $this->loginAs(self::DEMO_STUDENT_EMAIL, 'student');
    }

    public function loginAsInstructor()
    {
        return $this->loginAs(self::DEMO_INSTRUCTOR_EMAIL, 'instructor');
    }

    private function loginAs(string $email, string $role)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('home')
                ->with('error', 'Demo account not set up yet. Please contact the admin.');
        }

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        Auth::login($user, false);
        request()->session()->regenerate();

        $redirect = $role === 'instructor'
            ? route('instructor.dashboard')
            : route('student.dashboard');

        return redirect($redirect)
            ->with('info', '👋 You are browsing as a demo ' . $role . '. Some actions are restricted.');
    }

    public static function resetDemoData(): void
    {
        $student = User::where('email', self::DEMO_STUDENT_EMAIL)->first();
        if (!$student) return;

        LessonCompletion::where('user_id', $student->id)->delete();
        LessonVideoProgress::where('user_id', $student->id)->delete();
        LessonNote::where('user_id', $student->id)->delete();
        DrabAttempt::where('user_id', $student->id)->delete();

        $attemptIds = QuizAttempt::where('user_id', $student->id)->pluck('id');
        QuizAttemptAnswer::whereIn('attempt_id', $attemptIds)->delete();
        QuizAttempt::where('user_id', $student->id)->delete();

        AssignmentSubmission::where('user_id', $student->id)->delete();
    }
}