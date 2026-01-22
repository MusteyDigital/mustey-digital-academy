<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // Student marks attendance for a lesson
    public function store(Course $course, Lesson $lesson)
    {
        // Ensure lesson belongs to course
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $user = Auth::user();

        // Only students can mark attendance
        if ($user->role !== 'student') {
            abort(403);
        }

        // Student must be enrolled
        $enrolled = $user->coursesEnrolled()
            ->where('courses.id', $course->id)
            ->exists();

        if (!$enrolled) {
            abort(403);
        }

        // Create if not exists (unique constraint ensures no duplicates)
        Attendance::firstOrCreate(
            [
                'lesson_id' => $lesson->id,
                'user_id' => $user->id,
            ],
            [
                'status' => 'present',
                'marked_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Attendance marked successfully.');
    }

    // Instructor/Admin views attendance list for a lesson
    public function index(Course $course, Lesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $user = Auth::user();

        // Instructor must own course OR admin
        if ($user->role === 'instructor' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        if (!in_array($user->role, ['instructor', 'admin'])) {
            abort(403);
        }

        $attendances = Attendance::with('user')
            ->where('lesson_id', $lesson->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('attendances.index', compact('course', 'lesson', 'attendances'));
    }
}
