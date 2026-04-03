<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CourseManageController extends Controller
{
    /**
     * Instructor dashboard listing courses they teach
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'instructor' && $user->role !== 'admin') {
            abort(403);
        }

        // show only instructor's courses (admin can see all)
        $query = Course::query()->with('instructor');

        if ($user->role === 'instructor') {
            $query->where('instructor_id', $user->id);
        }

        // counts for dashboard cards
        $courses = $query->withCount([
            'modules',
            'lessons',
        ])->latest()->get();

        $courseIds = $courses->pluck('id');

        $totalPaidEnrollments = 0;
        $totalRevenue = 0;
        $topEarningCourses = collect();
        $recentPayments = collect();
        $revenueByCourse = collect();

        if ($courseIds->isNotEmpty()) {
            $totalPaidEnrollments = Payment::whereIn('course_id', $courseIds)
                ->where('status', 'success')
                ->count();

            $totalRevenue = Payment::whereIn('course_id', $courseIds)
                ->where('status', 'success')
                ->sum('amount');

            $revenueByCourse = Payment::select('course_id', DB::raw('SUM(amount) as total_revenue'))
                ->whereIn('course_id', $courseIds)
                ->where('status', 'success')
                ->groupBy('course_id')
                ->get()
                ->keyBy('course_id');

            $topRevenueRows = Payment::select('course_id', DB::raw('SUM(amount) as total_revenue'))
                ->whereIn('course_id', $courseIds)
                ->where('status', 'success')
                ->groupBy('course_id')
                ->orderByDesc('total_revenue')
                ->limit(5)
                ->get();

            $topEarningCourses = Course::whereIn('id', $topRevenueRows->pluck('course_id'))
                ->get()
                ->map(function ($course) use ($topRevenueRows) {
                    $row = $topRevenueRows->firstWhere('course_id', $course->id);
                    $course->total_revenue = (int) ($row->total_revenue ?? 0);
                    return $course;
                })
                ->sortByDesc('total_revenue')
                ->values();

            $recentPayments = Payment::with(['user', 'course'])
                ->whereIn('course_id', $courseIds)
                ->where('status', 'success')
                ->latest()
                ->take(5)
                ->get();
        }

        return view('dashboards.instructor', compact(
            'courses',
            'totalPaidEnrollments',
            'totalRevenue',
            'topEarningCourses',
            'recentPayments',
            'revenueByCourse'
        ));
    }

    /**
     * Instructor manage/edit course
     * ✅ Reuse your existing working view: courses.edit
     */
    public function edit(Course $course)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        return view('courses.edit', compact('course'));
    }

    /**
     * Instructor update course (same validation as CourseController)
     */
    public function update(Request $request, Course $course)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }

            $data['thumbnail'] = $request->file('thumbnail')
                ->store('course-thumbnails', 'public');
        }

        $course->update($data);

        return redirect()
            ->route('courses.show', $course->id)
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Instructor delete course
     */
    public function destroy(Course $course)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()
            ->route('instructor.dashboard')
            ->with('success', 'Course deleted successfully.');
    }
}
