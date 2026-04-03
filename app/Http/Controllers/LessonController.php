<?php

namespace App\Http\Controllers;

use App\Mail\CourseCompleted;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LessonController extends Controller
{
    public function create(Course $course)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $modules = $course->modules()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('lessons.create', [
            'course' => $course,
            'modules' => $modules,
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:50'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:1000'],
            'module_id' => ['nullable', 'integer', 'exists:modules,id'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (!empty($validated['module_id'])) {
            $moduleBelongsToCourse = $course->modules()
                ->where('id', $validated['module_id'])
                ->exists();

            if (!$moduleBelongsToCourse) {
                return back()
                    ->withErrors(['module_id' => 'Selected module does not belong to this course.'])
                    ->withInput();
            }
        }

        Lesson::create([
            'course_id' => $course->id,
            'module_id' => $validated['module_id'] ?? null,
            'title' => $validated['title'],
            'duration' => $validated['duration'] ?? null,
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'order' => $validated['order'] ?? 0,
        ]);

        return redirect()
            ->route('courses.show', $course->id)
            ->with('success', 'Lesson created successfully.');
    }

    public function show(Course $course, Lesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $user = Auth::user();

        $orderedLessons = $course->lessons()
            ->with('module')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $lessonIds = $orderedLessons->pluck('id')->values()->all();
        $totalLessons = count($lessonIds);

        $currentLessonIndex = array_search($lesson->id, $lessonIds, true);
        $lessonNumber = $currentLessonIndex !== false ? $currentLessonIndex + 1 : 1;

        $enrolled = false;
        $isCompleted = false;
        $completedLessons = 0;
        $progressPercent = 0;

        if ($user && $user->role === 'student') {
            $enrolled = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->exists();

            if (!$enrolled) {
                abort(403);
            }

            $isCompleted = LessonCompletion::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->exists();

            $completedLessons = LessonCompletion::where('user_id', $user->id)
                ->whereIn('lesson_id', $lessonIds)
                ->count();

            if ($totalLessons > 0) {
                $progressPercent = round(($completedLessons / $totalLessons) * 100);
            }

            if ($currentLessonIndex !== false && $currentLessonIndex > 0 && !$isCompleted) {
                $previousRequiredLessonId = $lessonIds[$currentLessonIndex - 1];

                $previousCompleted = LessonCompletion::where('user_id', $user->id)
                    ->where('lesson_id', $previousRequiredLessonId)
                    ->exists();

                if (!$previousCompleted) {
                    return redirect()
                        ->route('lessons.show', [$course->id, $previousRequiredLessonId])
                        ->with('error', 'Complete the previous lesson before unlocking this one.');
                }
            }
        }

        $previousLesson = null;
        $nextLesson = null;

        if ($currentLessonIndex !== false) {
            if ($currentLessonIndex > 0) {
                $previousLesson = $orderedLessons[$currentLessonIndex - 1];
            }

            if ($currentLessonIndex < ($totalLessons - 1)) {
                $nextLesson = $orderedLessons[$currentLessonIndex + 1];
            }
        }

        $lesson->load(['resources', 'assignment']);

        $assignmentPreviewHeaders = [];
        $assignmentPreviewRows = [];

        if ($lesson->assignment && $lesson->assignment->attachment_path) {
            $path = $lesson->assignment->attachment_path;
            $name = strtolower($lesson->assignment->attachment_name ?? '');

            $isCsv = str_ends_with($path, '.csv') || str_ends_with($name, '.csv');

            if ($isCsv && Storage::disk('public')->exists($path)) {
                $fullPath = Storage::disk('public')->path($path);
                $handle = @fopen($fullPath, 'r');

                if ($handle) {
                    $headers = fgetcsv($handle);

                    if (is_array($headers)) {
                        $assignmentPreviewHeaders = $headers;

                        $count = 0;
                        while (($row = fgetcsv($handle)) !== false && $count < 5) {
                            $assignmentPreviewRows[] = $row;
                            $count++;
                        }
                    }

                    fclose($handle);
                }
            }
        }

        return view('lessons.show', [
            'course' => $course,
            'lesson' => $lesson,
            'orderedLessons' => $orderedLessons,
            'isCompleted' => $isCompleted,
            'completedLessons' => $completedLessons,
            'totalLessons' => $totalLessons,
            'progressPercent' => $progressPercent,
            'previousLesson' => $previousLesson,
            'nextLesson' => $nextLesson,
            'lessonNumber' => $lessonNumber,
            'enrolled' => $enrolled,
            'assignmentPreviewHeaders' => $assignmentPreviewHeaders,
            'assignmentPreviewRows' => $assignmentPreviewRows,
        ]);
    }

    public function complete(Course $course, Lesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            abort(403);
        }

        $enrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if (!$enrolled) {
            abort(403);
        }

        LessonCompletion::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'completed_at' => now(),
            ]
        );

        Attendance::updateOrCreate(
            [
                'course_id' => $course->id,
                'lesson_id' => $lesson->id,
                'user_id' => $user->id,
            ],
            [
                'status' => 'present',
                'marked_at' => now(),
            ]
        );

        $orderedLessons = $course->lessons()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $lessonIds = $orderedLessons->pluck('id')->values();
        $totalLessons = $lessonIds->count();

        if ($totalLessons > 0) {
            $completedCount = LessonCompletion::where('user_id', $user->id)
                ->whereIn('lesson_id', $lessonIds)
                ->count();

            if ($completedCount >= $totalLessons) {
                $certificate = Certificate::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'serial' => 'CERT-' . strtoupper(Str::random(10)),
                        'verify_token' => Str::random(60),
                        'issued_at' => now(),
                    ]
                );

                if ($user->email) {
                    Mail::to($user->email)->queue(new CourseCompleted($course, $user, $certificate));
                }
            }
        }

        $currentIndex = $orderedLessons->search(fn($l) => $l->id === $lesson->id);
        $nextLesson = $currentIndex !== false && $currentIndex < ($orderedLessons->count() - 1)
            ? $orderedLessons[$currentIndex + 1]
            : null;

        if ($nextLesson) {
            return redirect()
                ->route('lessons.show', [$course->id, $nextLesson->id])
                ->with('success', 'Lesson marked as completed. Moving to the next lesson.');
        }

        return redirect()
            ->route('lessons.show', [$course->id, $lesson->id])
            ->with('success', 'Lesson marked as completed.');
    }
}
