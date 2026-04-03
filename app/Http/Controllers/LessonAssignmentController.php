<?php

namespace App\Http\Controllers;

use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LessonAssignmentController extends Controller
{
    public function store(Request $request, Course $course, Lesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $user = Auth::user();

        if (!$user || !in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'due_at' => ['nullable', 'date'],
            'max_score' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'assignment_file' => ['nullable', 'file', 'max:20480'],
        ]);

        $assignment = LessonAssignment::firstOrNew([
            'lesson_id' => $lesson->id,
        ]);

        $assignment->title = $validated['title'];
        $assignment->instructions = $validated['instructions'] ?? null;
        $assignment->due_at = $validated['due_at'] ?? null;
        $assignment->max_score = $validated['max_score'] ?? 100;

        if ($request->hasFile('assignment_file')) {
            if ($assignment->attachment_path && Storage::disk('public')->exists($assignment->attachment_path)) {
                Storage::disk('public')->delete($assignment->attachment_path);
            }

            $file = $request->file('assignment_file');
            $storedPath = $file->store('assignment-files', 'public');

            $assignment->attachment_path = $storedPath;
            $assignment->attachment_name = $file->getClientOriginalName();
        }

        $assignment->save();

        return redirect()
            ->route('lessons.show', [$course->id, $lesson->id])
            ->with('success', 'Assignment saved successfully.');
    }

    public function downloadAttachment(Course $course, Lesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        if (!auth()->check()) {
            abort(403);
        }

        $assignment = $lesson->assignment;

        if (!$assignment || !$assignment->attachment_path) {
            return redirect()
                ->route('lessons.show', [$course->id, $lesson->id])
                ->with('error', 'No assignment file found.');
        }

        if (!Storage::disk('public')->exists($assignment->attachment_path)) {
            return redirect()
                ->route('lessons.show', [$course->id, $lesson->id])
                ->with('error', 'Assignment file is missing from storage.');
        }

        return Storage::disk('public')->download(
            $assignment->attachment_path,
            $assignment->attachment_name ?? basename($assignment->attachment_path)
        );
    }

    public function submissions(Course $course, Lesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $user = Auth::user();

        if (!$user || !in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $assignment = $lesson->assignment;

        if (!$assignment) {
            return redirect()
                ->route('lessons.show', [$course->id, $lesson->id])
                ->with('error', 'No assignment found for this lesson.');
        }

        $submissions = AssignmentSubmission::with('user')
            ->where('lesson_assignment_id', $assignment->id)
            ->latest()
            ->get();

        return view('assignments.submissions', compact('course', 'lesson', 'assignment', 'submissions'));
    }

    public function grade(Request $request, Course $course, Lesson $lesson, AssignmentSubmission $submission)
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $user = Auth::user();

        if (!$user || !in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $assignment = $lesson->assignment;

        if (!$assignment || $submission->lesson_assignment_id !== $assignment->id) {
            abort(404);
        }

        $validated = $request->validate([
            'score' => ['nullable', 'integer', 'min:0'],
            'instructor_feedback' => ['nullable', 'string'],
        ]);

        $submission->update([
            'score' => $validated['score'],
            'instructor_feedback' => $validated['instructor_feedback'] ?? null,
        ]);

        return redirect()
            ->route('assignments.submissions', [$course->id, $lesson->id])
            ->with('success', 'Submission graded successfully.');
    }
}
