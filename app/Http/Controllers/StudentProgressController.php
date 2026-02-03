<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentProgressController extends Controller
{
    /**
     * Student: overview of all enrolled courses progress
     * Route: progress.index
     */
    public function index()
    {
        $user = Auth::user();

        abort_unless($user->role === 'student', 403);

        // courses enrolled via enrollments pivot
        $courses = $user->coursesEnrolled()->with('lessons')->get();

        // lesson completions: table lesson_completions (lesson_id, user_id)
        $completedLessonIds = DB::table('lesson_completions')
            ->where('user_id', $user->id)
            ->pluck('lesson_id')
            ->toArray();

        // attendance counts (lesson-based + live-based)
        $lessonAttendance = Attendance::where('user_id', $user->id)
            ->whereNotNull('lesson_id')
            ->get()
            ->groupBy('course_id')
            ->map(fn ($rows) => $rows->count())
            ->toArray();

        $liveAttendance = Attendance::where('user_id', $user->id)
            ->whereNull('lesson_id')
            ->get()
            ->groupBy('course_id')
            ->map(fn ($rows) => $rows->count())
            ->toArray();

        return view('progress.index', compact(
            'courses',
            'completedLessonIds',
            'lessonAttendance',
            'liveAttendance'
        ));
    }

    /**
     * Student: detailed progress for one course
     * Route: progress.show
     */
    public function show(Course $course)
    {
        $user = Auth::user();

        abort_unless($user->role === 'student', 403);

        // must be enrolled
        $enrolled = $course->students()->where('users.id', $user->id)->exists();
        abort_unless($enrolled, 403);

        $course->load('lessons');

        $completedLessonIds = DB::table('lesson_completions')
            ->where('user_id', $user->id)
            ->pluck('lesson_id')
            ->toArray();

        // lesson attendance map: lesson_id => true
        $lessonAttendanceIds = Attendance::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereNotNull('lesson_id')
            ->pluck('lesson_id')
            ->toArray();

        $hasLiveAttendance = Attendance::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereNull('lesson_id')
            ->exists();

        return view('progress.show', compact(
            'course',
            'completedLessonIds',
            'lessonAttendanceIds',
            'hasLiveAttendance'
        ));
    }
}
