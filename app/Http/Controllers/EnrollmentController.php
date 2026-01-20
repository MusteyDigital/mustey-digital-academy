<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    <?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Enroll the authenticated student in a course
    public function enroll(Course $course)
    {
        $user = Auth::user();

        // Prevent duplicate enrollment
        if ($user->coursesEnrolled()->where('course_id', $course->id)->exists()) {
            return redirect()->back()->with('error', 'You are already enrolled in this course.');
        }

        $user->coursesEnrolled()->attach($course->id, ['status' => 'enrolled']);

        return redirect()->back()->with('success', 'Enrolled successfully!');
    }

    // Show all courses student is enrolled in
    public function myCourses()
    {
        $user = Auth::user();
        $courses = $user->coursesEnrolled;

        return view('enrollments.my-courses', compact('courses'));
    }
}

}
