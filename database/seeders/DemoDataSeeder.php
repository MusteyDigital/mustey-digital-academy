<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\Module;
use App\Models\Payment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    const DEMO_STUDENT_EMAIL = 'demo-student@musteydigitalacademy.online';
    const DEMO_INSTRUCTOR_EMAIL = 'demo-instructor@musteydigitalacademy.online';

    public function run(): void
    {
        // 1. Create or fetch the demo accounts
        $instructor = User::firstOrCreate(
            ['email' => self::DEMO_INSTRUCTOR_EMAIL],
            [
                'name' => 'Demo Instructor',
                'password' => Hash::make(Str::random(32)),
                'role' => 'instructor',
            ]
        );

        $student = User::firstOrCreate(
            ['email' => self::DEMO_STUDENT_EMAIL],
            [
                'name' => 'Demo Student',
                'password' => Hash::make(Str::random(32)),
                'role' => 'student',
            ]
        );

        // 2. Wipe any previous demo course owned by the demo instructor (idempotent re-runs)
        Course::where('instructor_id', $instructor->id)->get()->each(function ($course) {
            $course->quizzes()->each(function ($quiz) {
                QuizAttemptAnswer::whereIn('attempt_id', $quiz->attempts()->pluck('id'))->delete();
                $quiz->attempts()->delete();
                $quiz->questions()->delete();
            });
            $course->quizzes()->delete();
            LessonCompletion::whereIn('lesson_id', $course->lessons()->pluck('id'))->delete();
            $course->lessons()->delete();
            $course->modules()->delete();
            Payment::where('course_id', $course->id)->delete();
            Enrollment::where('course_id', $course->id)->delete();
            $course->delete();
        });

        // 3. Create the demo course
        $course = Course::create([
            'title' => 'Web Development Fundamentals',
            'description' => 'A hands-on introduction to HTML, CSS, and JavaScript — build real projects from day one.',
            'price' => 15000,
            'instructor_id' => $instructor->id,
            'starts_at' => now()->subDays(10),
        ]);

        // 4. Modules + lessons
        $module1 = Module::create(['course_id' => $course->id, 'title' => 'Getting Started', 'order' => 1]);
        $module2 = Module::create(['course_id' => $course->id, 'title' => 'Styling with CSS', 'order' => 2]);
        $module3 = Module::create(['course_id' => $course->id, 'title' => 'JavaScript Basics', 'order' => 3]);

        $lessonsData = [
            [$module1, 'What is Web Development?', 1],
            [$module1, 'Setting Up Your Environment', 2],
            [$module1, 'Your First HTML Page', 3],
            [$module2, 'CSS Selectors & Properties', 1],
            [$module2, 'Flexbox & Grid Layout', 2],
            [$module3, 'Variables & Data Types', 1],
            [$module3, 'Functions & Events', 2],
        ];

        $lessons = [];
        foreach ($lessonsData as [$module, $title, $order]) {
            $lessons[] = Lesson::create([
                'course_id' => $course->id,
                'module_id' => $module->id,
                'title' => $title,
                'duration' => 15,
                'content' => "Sample lesson content for \"{$title}\".",
                'order' => $order,
            ]);
        }

        // 5. A quiz on the first module's last lesson
        $quiz = Quiz::create([
            'course_id' => $course->id,
            'lesson_id' => $lessons[2]->id,
            'title' => 'HTML Basics Quiz',
            'pass_mark' => 60,
            'is_published' => true,
            'max_attempts' => 3,
        ]);

        $q1 = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'What does HTML stand for?',
            'option_a' => 'Hyper Trainer Marking Language',
            'option_b' => 'HyperText Markup Language',
            'option_c' => 'HighText Machine Language',
            'option_d' => 'Hyperlink Text Markup Language',
            'correct_option' => 'B',
        ]);

        $q2 = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Which tag is used to link a CSS file?',
            'option_a' => '<style>',
            'option_b' => '<script>',
            'option_c' => '<link>',
            'option_d' => '<css>',
            'correct_option' => 'C',
        ]);

        // 6. Enroll the demo student
        Enrollment::firstOrCreate([
            'user_id' => $student->id,
            'course_id' => $course->id,
        ], [
            'status' => 'enrolled',
        ]);

        // 7. Mark first 4 lessons complete for the demo student
        foreach (array_slice($lessons, 0, 4) as $lesson) {
            LessonCompletion::firstOrCreate([
                'user_id' => $student->id,
                'lesson_id' => $lesson->id,
            ], [
                'completed_at' => now()->subDays(rand(1, 8)),
            ]);
        }

        // 8. A completed quiz attempt for the demo student
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $student->id,
            'status' => 'submitted',
            'score' => 2,
            'total' => 2,
            'percentage' => 100,
            'started_at' => now()->subDays(5)->subMinutes(10),
            'submitted_at' => now()->subDays(5),
        ]);

        QuizAttemptAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $q1->id,
            'selected_option' => 'B',
            'is_correct' => true,
        ]);

        QuizAttemptAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $q2->id,
            'selected_option' => 'C',
            'is_correct' => true,
        ]);

        // 9. Fake successful payments so revenue/analytics aren't empty
        Payment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'reference' => 'DEMO-' . Str::upper(Str::random(10)),
            'amount' => 15000,
            'currency' => 'NGN',
            'gateway' => 'paystack',
            'status' => 'success',
            'paid_at' => now()->subDays(5),
        ]);

        // A second demo student who also paid, to make revenue/enrollment numbers look real
        $secondStudent = User::firstOrCreate(
            ['email' => 'demo-student2@musteydigitalacademy.online'],
            [
                'name' => 'Aisha (Demo Student)',
                'password' => Hash::make(Str::random(32)),
                'role' => 'student',
            ]
        );

        Enrollment::firstOrCreate([
            'user_id' => $secondStudent->id,
            'course_id' => $course->id,
        ], ['status' => 'enrolled']);

        Payment::create([
            'user_id' => $secondStudent->id,
            'course_id' => $course->id,
            'reference' => 'DEMO-' . Str::upper(Str::random(10)),
            'amount' => 15000,
            'currency' => 'NGN',
            'gateway' => 'paystack',
            'status' => 'success',
            'paid_at' => now()->subDays(2),
        ]);

        $this->command->info('Demo data seeded: 1 course, 3 modules, 7 lessons, 1 quiz, 2 enrollments, 2 payments.');
    }
}
