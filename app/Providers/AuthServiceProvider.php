<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\Quiz;
use App\Policies\QuizPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Quiz::class => QuizPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Used by instructor routes: can:manage-courses
        Gate::define('manage-courses', function ($user, ?Course $course = null) {
            if ($user->role === 'admin') return true;
            if ($user->role !== 'instructor') return false;

            // if course is provided, must belong to them
            if ($course) {
                return (int) $course->instructor_id === (int) $user->id;
            }

            return true;
        });
    }
}
