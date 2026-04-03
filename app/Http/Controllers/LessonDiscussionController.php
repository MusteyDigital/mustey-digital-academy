<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonDiscussionMessage;
use Illuminate\Http\Request;

class LessonDiscussionController extends Controller
{
    public function store(Request $request, Course $course, Lesson $lesson)
    {
        $user = auth()->user();
        abort_unless($user, 403);

        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        if ($user->role === 'student') {
            $isEnrolled = $user->coursesEnrolled()
                ->where('courses.id', $course->id)
                ->exists();

            abort_unless($isEnrolled, 403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
            'parent_id' => ['nullable', 'integer', 'exists:lesson_discussion_messages,id'],
        ]);

        if (!empty($data['parent_id'])) {
            $parent = LessonDiscussionMessage::findOrFail($data['parent_id']);

            abort_unless($parent->lesson_id === $lesson->id, 422);
        }

        LessonDiscussionMessage::create([
            'lesson_id' => $lesson->id,
            'course_id' => $course->id,
            'user_id' => $user->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body' => $data['body'],
        ]);

        return back()->with('success', 'Discussion posted.');
    }

    public function destroy(Course $course, Lesson $lesson, LessonDiscussionMessage $message)
    {
        $user = auth()->user();
        abort_unless($user, 403);

        if ($message->lesson_id !== $lesson->id || $lesson->course_id !== $course->id) {
            abort(404);
        }

        $canDelete =
            $user->role === 'admin' ||
            $message->user_id === $user->id ||
            ($user->role === 'instructor' && $course->instructor_id === $user->id);

        abort_unless($canDelete, 403);

        $message->delete();

        return back()->with('success', 'Discussion deleted.');
    }

    public function pin(Course $course, Lesson $lesson, LessonDiscussionMessage $message)
    {
        $user = auth()->user();
        abort_unless($user && in_array($user->role, ['admin', 'instructor']), 403);

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        abort_unless($message->lesson_id === $lesson->id && $lesson->course_id === $course->id, 404);

        $message->update([
            'is_pinned' => !$message->is_pinned,
        ]);

        return back()->with('success', 'Pin updated.');
    }

    public function markAnswer(Course $course, Lesson $lesson, LessonDiscussionMessage $message)
    {
        $user = auth()->user();
        abort_unless($user && in_array($user->role, ['admin', 'instructor']), 403);

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        abort_unless($message->lesson_id === $lesson->id && $lesson->course_id === $course->id, 404);

        LessonDiscussionMessage::where('lesson_id', $lesson->id)
            ->update(['is_answer' => false]);

        $message->update([
            'is_answer' => true,
        ]);

        return back()->with('success', 'Answer marked.');
    }
}
