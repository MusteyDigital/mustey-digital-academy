<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonComment;
use Illuminate\Http\Request;

class LessonCommentController extends Controller
{
    public function store(Request $request, Course $course, Lesson $lesson)
    {
        abort_unless(auth()->check(), 403);

        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        \Log::info('lesson comment store debug', [
            'raw_parent_id' => $request->input('parent_id'),
            'validated_parent_id' => $validated['parent_id'] ?? null,
            'all' => $request->only(['body', 'parent_id']),
        ]);

        LessonComment::create([
            'user_id' => auth()->id(),
            'lesson_id' => $lesson->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => $validated['body'],
        ]);

        return redirect(route('lessons.show', [$course->id, $lesson->id]) . '#lesson-discussion')
            ->with('success', 'Comment posted successfully.');
    }

    public function destroy(Course $course, Lesson $lesson, LessonComment $comment)
    {
        abort_unless(auth()->check(), 403);

        if ($comment->lesson_id !== $lesson->id || $lesson->course_id !== $course->id) {
            abort(404);
        }

        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Comment removed.');
    }
}
