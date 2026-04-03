<?php

namespace App\Http\Controllers;

use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmissionController extends Controller
{
    public function store(Request $request, Course $course, Lesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $assignment = $lesson->assignment;

        if (!$assignment) {
            return redirect()
                ->route('lessons.show', [$course->id, $lesson->id])
                ->with('error', 'No assignment is available for this lesson yet.');
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

        $validated = $request->validate([
            'submission_file' => ['required', 'file', 'max:20480'],
            'student_note' => ['nullable', 'string'],
        ]);

        $file = $validated['submission_file'];
        $path = $file->store('assignment-submissions', 'public');

        $existing = AssignmentSubmission::where('lesson_assignment_id', $assignment->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing && $existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
            Storage::disk('public')->delete($existing->file_path);
        }

        AssignmentSubmission::updateOrCreate(
            [
                'lesson_assignment_id' => $assignment->id,
                'user_id' => $user->id,
            ],
            [
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'student_note' => $validated['student_note'] ?? null,
                'submitted_at' => now(),
            ]
        );

        return redirect()
            ->route('lessons.show', [$course->id, $lesson->id])
            ->with('success', 'Assignment submitted successfully.');
    }

    public function download(Course $course, Lesson $lesson, AssignmentSubmission $submission)
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $assignment = $lesson->assignment;

        if (!$assignment || $submission->lesson_assignment_id !== $assignment->id) {
            abort(404);
        }

        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $allowed = false;

        if ($user->role === 'admin') {
            $allowed = true;
        }

        if ($user->role === 'instructor' && $course->instructor_id === $user->id) {
            $allowed = true;
        }

        if ($user->role === 'student' && $submission->user_id === $user->id) {
            $allowed = true;
        }

        if (!$allowed) {
            abort(403);
        }

        if (!$submission->file_path || !Storage::disk('public')->exists($submission->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $submission->file_path,
            $submission->file_name ?: basename($submission->file_path)
        );
    }
}
