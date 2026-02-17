<?php

namespace App\Http\Controllers;

use App\Mail\EnrollmentConfirmed;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EnrollmentController extends Controller
{
    /**
     * Enroll a student into a course
     */
    public function enroll(Course $course)
    {
        $user = Auth::user();

        // Only students can enroll
        if ($user->role !== 'student') {
            abort(403);
        }

        // Prevent duplicate enrollment
        $already = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($already) {
            return redirect()
                ->back()
                ->with('success', 'You are already enrolled in this course.');
        }

        // Create enrollment
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);

        /**
         * Send enrollment email
         * Uses QUEUE (database driver)
         * Mailpit will capture it locally
         */
        Mail::to($user->email)
            ->queue(new EnrollmentConfirmed($user, $course));

        return redirect()
            ->back()
            ->with('success', 'Enrollment successful! A confirmation email has been sent.');
    }

    /**
     * Unenroll a student
     */
    public function unenroll(Course $course)
    {
        $user = Auth::user();

        if ($user->role !== 'student') {
            abort(403);
        }

        Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->delete();

        return redirect()
            ->route('enrollments.my-courses')
            ->with('success', 'You have unenrolled successfully.');
    }

    /**
     * Student enrolled courses + progress
     */
    public function myCourses()
    {
        $user = Auth::user();

        $courses = $user->coursesEnrolled()
            ->with('lessons')
            ->get();

        $progress = [];

        foreach ($courses as $course) {
            $lessonIds = $course->lessons->pluck('id');
            $totalLessons = $lessonIds->count();

            $completedLessons = $totalLessons > 0
                ? LessonCompletion::where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessonIds)
                    ->count()
                : 0;

            $percent = $totalLessons > 0
                ? round(($completedLessons / $totalLessons) * 100)
                : 0;

            $progress[$course->id] = [
                'completed' => $completedLessons,
                'total' => $totalLessons,
                'percent' => $percent,
            ];
        }

        return view('enrollments.my-courses', compact('courses', 'progress'));
    }
}
