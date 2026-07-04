<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\DrabAttempt;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\LessonNote;
use App\Models\LessonVideoProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Console\Command;

class ResetDemoData extends Command
{
    protected $signature = 'demo:reset';
    protected $description = 'Reset the demo student\'s progress back to the polished showcase baseline';

    const DEMO_STUDENT_EMAIL = 'demo-student@musteydigitalacademy.online';
    const DEMO_INSTRUCTOR_EMAIL = 'demo-instructor@musteydigitalacademy.online';

    public function handle(): int
    {
        $student = User::where('email', self::DEMO_STUDENT_EMAIL)->first();
        $instructor = User::where('email', self::DEMO_INSTRUCTOR_EMAIL)->first();

        if (!$student || !$instructor) {
            $this->warn('Demo accounts not found — nothing to reset.');
            return self::SUCCESS;
        }

        $course = Course::where('instructor_id', $instructor->id)->first();

        if (!$course) {
            $this->warn('Demo course not found — nothing to reset.');
            return self::SUCCESS;
        }

        // Wipe the demo student's personal progress (never touches course structure,
        // enrollments, or payments — those stay exactly as seeded).
        LessonCompletion::where('user_id', $student->id)
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->delete();

        LessonVideoProgress::where('user_id', $student->id)
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->delete();

        LessonNote::where('user_id', $student->id)
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->delete();

        DrabAttempt::where('user_id', $student->id)->delete();

        $quizIds = $course->quizzes()->pluck('id');
        $attemptIds = QuizAttempt::where('user_id', $student->id)
            ->whereIn('quiz_id', $quizIds)
            ->pluck('id');

        QuizAttemptAnswer::whereIn('attempt_id', $attemptIds)->delete();
        QuizAttempt::whereIn('id', $attemptIds)->delete();

        // Re-seed the baseline showcase state: first 4 lessons complete
        $lessons = $course->lessons()->orderBy('order')->orderBy('id')->get();

        foreach ($lessons->take(4) as $lesson) {
            LessonCompletion::create([
                'user_id' => $student->id,
                'lesson_id' => $lesson->id,
                'completed_at' => now()->subDays(rand(1, 8)),
            ]);
        }

        // Re-seed the baseline 100% quiz attempt
        $quiz = $course->quizzes()->first();

        if ($quiz) {
            $questions = $quiz->questions;

            $attempt = QuizAttempt::create([
                'quiz_id' => $quiz->id,
                'user_id' => $student->id,
                'status' => 'submitted',
                'score' => $questions->count(),
                'total' => $questions->count(),
                'percentage' => 100,
                'started_at' => now()->subDays(5)->subMinutes(10),
                'submitted_at' => now()->subDays(5),
            ]);

            foreach ($questions as $question) {
                QuizAttemptAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'selected_option' => $question->correct_option,
                    'is_correct' => true,
                ]);
            }
        }

        $this->info('Demo data reset to baseline showcase state.');

        return self::SUCCESS;
    }
}
