<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
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
            return redirect()->back();
        }

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);

        return redirect()->back();
    }

    public function myCourses()
    {
        $user = Auth::user();

        $courses = $user->coursesEnrolled;

        return view('enrollments.my-courses', compact('courses'));
    }
}
