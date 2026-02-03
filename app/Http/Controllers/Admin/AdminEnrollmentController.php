<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class AdminEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');

        $enrollments = Enrollment::query()
            ->with(['user', 'course', 'course.instructor'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->whereHas('user', function ($u) use ($q) {
                            $u->where('name', 'like', "%{$q}%")
                              ->orWhere('email', 'like', "%{$q}%");
                        })
                        ->orWhereHas('course', function ($c) use ($q) {
                            $c->where('title', 'like', "%{$q}%");
                        })
                        ->orWhereHas('course.instructor', function ($i) use ($q) {
                            $i->where('name', 'like', "%{$q}%");
                        })
                        ->orWhere('status', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.enrollments.index', compact('enrollments', 'q'));
    }
}
