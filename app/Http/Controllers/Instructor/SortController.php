<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\Request;

class SortController extends Controller
{
    public function modules(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        foreach ($data['ids'] as $index => $id) {
            Module::where('id', $id)
                ->where('course_id', $course->id)
                ->update(['order' => $index + 1]);
        }

        return response()->json(['ok' => true]);
    }

    public function lessons(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $data = $request->validate([
            'module_id' => ['nullable', 'integer'],
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        foreach ($data['ids'] as $index => $id) {
            Lesson::where('id', $id)
                ->where('course_id', $course->id)
                ->update([
                    'module_id' => $data['module_id'],
                    'order' => $index + 1,
                ]);
        }

        return response()->json(['ok' => true]);
    }

    private function authorizeCourse(Course $course): void
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return;
        }

        abort_if($user->role !== 'instructor', 403);
        abort_if($course->instructor_id !== $user->id, 403);
    }
}
