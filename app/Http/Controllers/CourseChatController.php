<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseChatMessage;
use Illuminate\Http\Request;

class CourseChatController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $user = auth()->user();
        abort_unless($user, 403);

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
            'parent_id' => ['nullable', 'integer', 'exists:course_chat_messages,id'],
        ]);

        if (!empty($data['parent_id'])) {
            $parent = CourseChatMessage::findOrFail($data['parent_id']);
            abort_unless($parent->course_id === $course->id, 422);
        }

        CourseChatMessage::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body' => $data['body'],
        ]);

        return back()->with('success', 'Course chat message posted.');
    }

    public function destroy(Course $course, CourseChatMessage $message)
    {
        $user = auth()->user();
        abort_unless($user, 403);

        abort_unless($message->course_id === $course->id, 404);

        $canDelete =
            $user->role === 'admin' ||
            $message->user_id === $user->id ||
            ($user->role === 'instructor' && $course->instructor_id === $user->id);

        abort_unless($canDelete, 403);

        $message->delete();

        return back()->with('success', 'Course chat message deleted.');
    }

    public function pin(Course $course, CourseChatMessage $message)
    {
        $user = auth()->user();
        abort_unless($user && in_array($user->role, ['admin', 'instructor']), 403);

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        abort_unless($message->course_id === $course->id, 404);

        $message->update([
            'is_pinned' => !$message->is_pinned,
        ]);

        return back()->with('success', 'Course chat pin updated.');
    }
}
