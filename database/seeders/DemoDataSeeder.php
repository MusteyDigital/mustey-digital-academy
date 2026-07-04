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
        $instructor = User::firstOrCreate(
            ['email' => self::DEMO_INSTRUCTOR_EMAIL],
            ['name' => 'Demo Instructor', 'password' => Hash::make(Str::random(32)), 'role' => 'instructor']
        );

        $student = User::firstOrCreate(
            ['email' => self::DEMO_STUDENT_EMAIL],
            ['name' => 'Demo Student', 'password' => Hash::make(Str::random(32)), 'role' => 'student']
        );

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

        $course = Course::create([
            'title' => 'Web Development Fundamentals',
            'description' => 'A hands-on introduction to HTML, CSS, and JavaScript — build real projects from day one.',
            'price' => 15000,
            'instructor_id' => $instructor->id,
            'starts_at' => now()->subDays(10),
        ]);

        $module1 = Module::create(['course_id' => $course->id, 'title' => 'Getting Started', 'order' => 1]);
        $module2 = Module::create(['course_id' => $course->id, 'title' => 'Styling with CSS', 'order' => 2]);
        $module3 = Module::create(['course_id' => $course->id, 'title' => 'JavaScript Basics', 'order' => 3]);

        $lessonsData = [
            [$module1, 'What is Web Development?', 1, "Web development is the work involved in building and maintaining websites. It's split into two main areas: front-end (what users see and interact with — the layout, colors, buttons, and text) and back-end (the server, database, and logic that power the site behind the scenes).\n\nIn this course, we'll focus primarily on front-end fundamentals: HTML for structure, CSS for styling, and JavaScript for interactivity. By the end, you'll understand how these three languages work together to create the websites you use every day.\n\nWhy learn web development? It's one of the most in-demand skills in tech — websites and web apps power everything from small business storefronts to global platforms. Whether you want to freelance, build your own products, or land a developer job, this foundation is where every web developer starts."],
            [$module1, 'Setting Up Your Environment', 2, "Before writing any code, you need the right tools. Here's what we recommend:\n\n1. A code editor — Visual Studio Code (free, and the most widely used editor for web development).\n2. A modern browser — Chrome or Firefox, both with excellent built-in developer tools for inspecting and debugging your pages.\n3. A place to organize your files — create a dedicated folder for this course, e.g. 'web-dev-fundamentals', and keep every project inside it.\n\nOnce VS Code is installed, take a moment to explore the interface: the file explorer on the left, the editor in the center, and the integrated terminal at the bottom (View → Terminal). We'll use the terminal throughout this course.\n\nIn the next lesson, we'll write our very first line of HTML."],
            [$module1, 'Your First HTML Page', 3, "HTML (HyperText Markup Language) is the skeleton of every web page. It doesn't make things look pretty — that's CSS's job — but it defines the structure and meaning of your content.\n\nEvery HTML page starts with a basic structure:\n\n<!DOCTYPE html>\n<html>\n<head>\n  <title>My First Page</title>\n</head>\n<body>\n  <h1>Hello, World!</h1>\n  <p>This is my first web page.</p>\n</body>\n</html>\n\nLet's break this down:\n- <!DOCTYPE html> tells the browser this is a modern HTML5 document.\n- <html> wraps the entire page.\n- <head> holds metadata (like the page title) that isn't shown directly on the page.\n- <body> contains everything visible to the user — headings, paragraphs, images, links, and more.\n\nTry creating a file called index.html, paste this code in, and open it in your browser. Congratulations — you've built your first web page!\n\nWhen you're done, take the quiz below to check your understanding."],
            [$module2, 'CSS Selectors & Properties', 1, "CSS (Cascading Style Sheets) controls how your HTML looks — colors, spacing, fonts, layout, and more.\n\nA CSS rule has two parts: a selector (what you're styling) and a declaration block (how you're styling it).\n\nh1 {\n  color: blue;\n  font-size: 32px;\n}\n\nHere, h1 is the selector — it targets every <h1> element on the page. Inside the curly braces, we set two properties: color and font-size.\n\nCommon selector types:\n- Element selectors (h1, p, div) target all elements of that type.\n- Class selectors (.my-class) target elements with a specific class attribute — reusable across many elements.\n- ID selectors (#my-id) target a single, unique element.\n\nYou can link a CSS file to your HTML using: <link rel='stylesheet' href='styles.css'> inside the <head>. Try styling your first HTML page from the previous lesson — change the heading color, add some padding, experiment freely."],
            [$module2, 'Flexbox & Grid Layout', 2, "Positioning elements used to be one of the hardest parts of CSS. Flexbox and Grid solved that.\n\nFlexbox is ideal for one-dimensional layouts — arranging items in a row or column:\n\n.container {\n  display: flex;\n  justify-content: space-between;\n  align-items: center;\n}\n\nThis lines up child elements horizontally, spacing them evenly and centering them vertically.\n\nGrid is built for two-dimensional layouts — rows AND columns at once:\n\n.container {\n  display: grid;\n  grid-template-columns: repeat(3, 1fr);\n  gap: 16px;\n}\n\nThis creates a 3-column grid with even spacing between items — perfect for card layouts, image galleries, or dashboards.\n\nRule of thumb: reach for Flexbox when aligning items in a single direction, and Grid when you need full control over rows and columns together. Most real-world layouts actually use both."],
            [$module3, 'Variables & Data Types', 1, "JavaScript brings your web pages to life by adding interactivity — click handlers, form validation, dynamic content updates, and more.\n\nEvery JavaScript program starts with variables — containers for storing data:\n\nlet name = 'Mustapha';\nconst age = 25;\nvar isStudent = true;\n\n- let: a variable that can be reassigned later.\n- const: a variable that cannot be reassigned (use this by default).\n- var: the older way of declaring variables — avoid it in modern code.\n\nJavaScript has several core data types:\n- String: text, e.g. 'Hello'\n- Number: 42 or 3.14\n- Boolean: true or false\n- Array: a list, e.g. [1, 2, 3]\n- Object: a collection of key-value pairs, e.g. { name: 'Mustapha', age: 25 }\n\nTry opening your browser's developer console (F12 or right-click → Inspect → Console) and typing these examples directly — it's the fastest way to experiment with JavaScript."],
            [$module3, 'Functions & Events', 2, "Functions let you package reusable blocks of logic:\n\nfunction greet(name) {\n  return 'Hello, ' + name + '!';\n}\n\nconsole.log(greet('Mustapha')); // Hello, Mustapha!\n\nEvents let your page respond to user actions — clicks, key presses, form submissions:\n\ndocument.querySelector('button').addEventListener('click', function() {\n  alert('Button clicked!');\n});\n\nThis is the foundation of interactivity: HTML provides the structure, CSS makes it look good, and JavaScript makes it respond to the user.\n\nCongratulations — you now understand the three pillars of web development. From here, the best next step is to build small projects: a personal portfolio page, a to-do list app, or a simple calculator. Keep building, keep experimenting, and you'll grow fast."],
        ];

        $lessons = [];
        foreach ($lessonsData as [$module, $title, $order, $content]) {
            $lessons[] = Lesson::create([
                'course_id' => $course->id,
                'module_id' => $module->id,
                'title' => $title,
                'duration' => 15,
                'content' => $content,
                'order' => $order,
            ]);
        }

        // Quiz on the last lesson (Functions & Events) so it's the natural end of a walkthrough
        $quiz = Quiz::create([
            'course_id' => $course->id,
            'lesson_id' => $lessons[6]->id,
            'title' => 'JavaScript Basics Quiz',
            'pass_mark' => 60,
            'is_published' => true,
            'max_attempts' => 3,
        ]);

        $q1 = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Which keyword declares a variable that cannot be reassigned?',
            'option_a' => 'let',
            'option_b' => 'var',
            'option_c' => 'const',
            'option_d' => 'static',
            'correct_option' => 'C',
        ]);

        $q2 = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Which method attaches a click handler to a button?',
            'option_a' => 'button.onClick()',
            'option_b' => 'addEventListener',
            'option_c' => 'attachEvent',
            'option_d' => 'onButtonPress',
            'correct_option' => 'B',
        ]);

        Enrollment::firstOrCreate(['user_id' => $student->id, 'course_id' => $course->id], ['status' => 'enrolled']);

        foreach (array_slice($lessons, 0, 4) as $lesson) {
            LessonCompletion::firstOrCreate(
                ['user_id' => $student->id, 'lesson_id' => $lesson->id],
                ['completed_at' => now()->subDays(rand(1, 8))]
            );
        }

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

        QuizAttemptAnswer::create(['attempt_id' => $attempt->id, 'question_id' => $q1->id, 'selected_option' => 'C', 'is_correct' => true]);
        QuizAttemptAnswer::create(['attempt_id' => $attempt->id, 'question_id' => $q2->id, 'selected_option' => 'B', 'is_correct' => true]);

        Payment::create([
            'user_id' => $student->id, 'course_id' => $course->id,
            'reference' => 'DEMO-' . Str::upper(Str::random(10)),
            'amount' => 15000, 'currency' => 'NGN', 'gateway' => 'paystack',
            'status' => 'success', 'paid_at' => now()->subDays(5),
        ]);

        $secondStudent = User::firstOrCreate(
            ['email' => 'demo-student2@musteydigitalacademy.online'],
            ['name' => 'Aisha (Demo Student)', 'password' => Hash::make(Str::random(32)), 'role' => 'student']
        );

        Enrollment::firstOrCreate(['user_id' => $secondStudent->id, 'course_id' => $course->id], ['status' => 'enrolled']);

        Payment::create([
            'user_id' => $secondStudent->id, 'course_id' => $course->id,
            'reference' => 'DEMO-' . Str::upper(Str::random(10)),
            'amount' => 15000, 'currency' => 'NGN', 'gateway' => 'paystack',
            'status' => 'success', 'paid_at' => now()->subDays(2),
        ]);

        $this->command->info('Demo data seeded: 1 course, 3 modules, 7 lessons, 1 quiz, 2 enrollments, 2 payments.');
    }
}
