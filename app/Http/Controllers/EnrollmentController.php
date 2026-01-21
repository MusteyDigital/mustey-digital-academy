<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function enroll(Course $course)
    {
        $user = Auth::user();

        if ($user->role !== 'student') {
            abort(403);
        }

        $already = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($already) {
            return redirect()->back();
        }

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);

        return redirect()->back();
    }

    public function unenroll(Course $course)
    {
        $user = Auth::user();

        if ($user->role !== 'student') {
            abort(403);
        }

        Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->delete();

        return redirect()->route('enrollments.my-courses');
    }

    public function myCourses()
    {
        $user = Auth::user();

        // IMPORTANT: load lessons with each course
        $courses = $user->coursesEnrolled()->with('lessons')->get();

        $progress = [];

        foreach ($courses as $course) {
            $lessonIds = $course->lessons->pluck('id');

            $totalLessons = $lessonIds->count();

            $completedLessons = $totalLessons > 0
                ? LessonCompletion::where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessonIds)
                    ->count()
                : 0;

            $percent = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

            $progress[$course->id] = [
                'completed' => $completedLessons,
                'total' => $totalLessons,
                'percent' => $percent,
            ];
        }

        return view('enrollments.my-courses', compact('courses', 'progress'));
    }
}
