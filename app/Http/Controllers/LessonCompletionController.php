<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonCompletion;
use Illuminate\Http\Request;

class LessonCompletionController extends Controller
{
    public function store(Request $request, Lesson $lesson)
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'student') {
            abort(403);
        }

        LessonCompletion::firstOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'completed_at' => now(),
            ]
        );

        return back()->with('success', 'Lesson marked as completed.');
    }
}
