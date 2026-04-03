<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;

class QuizPolicy
{
    /**
     * Anyone logged in can view the quiz page (you can tighten this later).
     */
    public function view(User $user, Quiz $quiz): bool
    {
        return in_array($user->role, ['student', 'instructor', 'admin'], true);
    }

    /**
     * Only students can actually TAKE the quiz (start/submit),
     * and only if not locked/exhausted.
     */
    public function take(User $user, Quiz $quiz): bool
    {
        if ($user->role !== 'student') {
            return false;
        }

        // must be published to take (optional)
        if (!$quiz->is_published) {
            return false;
        }

        // lock after pass: if they already passed once, block new attempts
        if ($quiz->lock_after_pass) {
            $passedOnce = $quiz->attempts()
                ->where('user_id', $user->id)
                ->where('status', 'submitted')
                ->where('percentage', '>=', (int)($quiz->pass_mark ?? 0))
                ->exists();

            if ($passedOnce) return false;
        }

        // max attempts: block new attempts if exhausted
        if (!is_null($quiz->max_attempts)) {
            $submittedCount = $quiz->attempts()
                ->where('user_id', $user->id)
                ->where('status', 'submitted')
                ->where('total', '>', 0)
                ->count();

            if ($submittedCount >= $quiz->max_attempts) {
                return false;
            }
        }

        return true;
    }

    /**
     * Students can view their own result/attempt history even when take() is false.
     */
    public function viewAttempts(User $user, Quiz $quiz): bool
    {
        return $user->role === 'student';
    }

    /**
     * Instructors/admin can manage quizzes (analytics/add questions etc.)
     */
    public function manage(User $user, Quiz $quiz): bool
    {
        return in_array($user->role, ['instructor', 'admin'], true);
    }
}
