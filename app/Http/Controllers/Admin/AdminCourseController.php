<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class AdminCourseController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Always define $q so the view never breaks
        $q = (string) $request->query('q', '');

        $courses = Course::query()
            ->with('instructor')
            ->when($q !== '', function ($query) use ($q) {
                // ✅ group OR conditions properly
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.courses.index', compact('courses', 'q'));
    }
}
