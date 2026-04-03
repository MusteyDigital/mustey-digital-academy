<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function index(Course $course)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $modules = $course->modules()
            ->with(['lessons' => function ($q) {
                $q->orderBy('order')->orderBy('id');
            }])
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('instructor.modules.index', compact('course', 'modules'));
    }

    public function store(Request $request, Course $course)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        Module::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'order' => ($course->modules()->max('order') ?? 0) + 1,
        ]);

        return back()->with('success', 'Module created successfully.');
    }

    public function update(Request $request, Course $course, Module $module)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        if ($module->course_id !== $course->id) {
            abort(404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $module->update([
            'title' => $request->title,
        ]);

        return back()->with('success', 'Module updated successfully.');
    }

    public function destroy(Course $course, Module $module)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $course->instructor_id !== $user->id) {
            abort(403);
        }

        if ($module->course_id !== $course->id) {
            abort(404);
        }

        $module->delete();

        return back()->with('success', 'Module deleted successfully.');
    }
}
