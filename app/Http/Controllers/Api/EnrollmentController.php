<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ]);

        $existing = Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $validated['course_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Already enrolled in this course.',
                'enrollment' => $existing,
            ], 200);
        }

        $enrollment = Enrollment::create([
            'user_id' => $request->user()->id,
            'course_id' => $validated['course_id'],
            'status' => 'enrolled',
        ]);

        return response()->json([
            'message' => 'Enrolled successfully.',
            'enrollment' => $enrollment,
        ], 201);
    }

    public function index(Request $request)
    {
        $enrollments = Enrollment::where('user_id', $request->user()->id)
            ->with(['course', 'course.modules' => function($q) { $q->orderBy('order'); }, 'course.modules.lessons' => function($q) { $q->orderBy('order'); }])
            ->get();

        return response()->json($enrollments);
    }

    public function check(Request $request, $courseId)
    {
        $enrollment = Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $courseId)
            ->first();

        return response()->json([
            'enrolled' => (bool) $enrollment,
            'status' => $enrollment->status ?? null,
        ]);
    }
}