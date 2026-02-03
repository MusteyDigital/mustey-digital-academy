<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    // ✅ Lesson attendance (records that have lesson_id)
    public function lessonIndex(Request $request)
{
    $q = (string) $request->query('q', '');

    $records = \App\Models\LessonCompletion::query()
        ->with(['user', 'lesson', 'lesson.course', 'lesson.course.instructor'])
        ->when($q !== '', function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                    })
                    ->orWhereHas('lesson', function ($l) use ($q) {
                        $l->where('title', 'like', "%{$q}%");
                    })
                    ->orWhereHas('lesson.course', function ($c) use ($q) {
                        $c->where('title', 'like', "%{$q}%");
                    })
                    ->orWhereHas('lesson.course.instructor', function ($i) use ($q) {
                        $i->where('name', 'like', "%{$q}%");
                    });
            });
        })
        ->orderByDesc('id')
        ->paginate(15)
        ->withQueryString();

    return view('admin.attendance.lessons', compact('records', 'q'));
}

    // ✅ Live attendance (records that have course_id, but NO lesson_id)
    public function liveIndex(Request $request)
    {
        $q = $request->get('q', '');

        $records = Attendance::query()
            ->whereNull('lesson_id')
            ->whereNotNull('course_id')
            ->with(['user', 'course', 'course.instructor'])
            ->when($q, function ($query) use ($q) {
                $query->whereHas('user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%");
                    })
                    ->orWhereHas('course', function ($c) use ($q) {
                        $c->where('title', 'like', "%{$q}%");
                    })
                    ->orWhereHas('course.instructor', function ($i) use ($q) {
                        $i->where('name', 'like', "%{$q}%");
                    });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.attendance.live', compact('records', 'q'));
    }
}
