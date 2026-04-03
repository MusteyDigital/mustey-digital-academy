<?php

namespace App\Http\Controllers;

use App\Models\Course;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCourses = Course::where('is_featured', true)
            ->latest()
            ->take(6)
            ->get();

        $latestCourses = Course::latest()
            ->take(9)
            ->get();

        return view('home', compact('featuredCourses', 'latestCourses'));
    }
}
