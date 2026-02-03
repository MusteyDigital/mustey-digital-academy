<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function store(Course $course, Lesson $lesson)
    {
        $user = Auth::user();

        if ($user->role !== 'student') {
            abort(403);
        }

        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        // ✅ enrolled? (support pivot attach OR Enrollment::create in tests)
        $enrolledViaPivot = $course->students()
            ->where('users.id', $user->id)
            ->exists();

        $enrolledViaTable = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if (!($enrolledViaPivot || $enrolledViaTable)) {
            abort(403);
        }

        $already = Attendance::where('lesson_id', $lesson->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($already) {
            return redirect()->back();
        }

        Attendance::create([
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
            'status' => 'present',
            'marked_at' => now(),
        ]);

        return redirect()->back();
    }

    public function index(Course $course, Lesson $lesson)
    {
        $user = Auth::user();

        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        if (!in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $attendances = Attendance::where('lesson_id', $lesson->id)
            ->with('user')
            ->get();

        return view('attendance.index', compact('course', 'lesson', 'attendances'));
    }

    public function liveStore(Course $course)
    {
        $user = Auth::user();

        if ($user->role !== 'student') {
            abort(403);
        }

        // ✅ enrolled? (support pivot attach OR Enrollment::create in tests)
        $enrolledViaPivot = $course->students()
            ->where('users.id', $user->id)
            ->exists();

        $enrolledViaTable = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if (!($enrolledViaPivot || $enrolledViaTable)) {
            abort(403);
        }

        // ✅ prevent duplicates (course_id + user_id + lesson_id NULL)
        $already = Attendance::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->whereNull('lesson_id')
            ->exists();

        if ($already) {
            return redirect()->back();
        }

        Attendance::create([
            'course_id' => $course->id,
            'lesson_id' => null,
            'user_id' => $user->id,
            'status' => 'present',
            'marked_at' => now(),
        ]);

        return redirect()->back();
    }

    public function liveIndex(Course $course)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $attendances = Attendance::where('course_id', $course->id)
            ->whereNull('lesson_id')
            ->with('user')
            ->get();

        return view('attendance.live', compact('course', 'attendances'));
    }
}
