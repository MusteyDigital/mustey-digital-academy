<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Certificate;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ================== STATS ==================
        $totalUsers       = User::count();
        $totalStudents    = User::where('role', 'student')->count();
        $totalInstructors = User::where('role', 'instructor')->count();
        $totalAdmins      = User::where('role', 'admin')->count();

        $totalCourses = Course::count();

        // If you have enrollments table
        $totalEnrollments = DB::table('enrollments')->count();

        $totalCertificates = Certificate::count();

        $recentUsers        = User::latest()->take(5)->get();
        $recentCourses      = Course::with('instructor')->latest()->take(5)->get();
        $recentCertificates = Certificate::with(['user', 'course'])->latest()->take(5)->get();

        // ================== TOP COURSES BY ENROLLMENTS ==================
        // NOTE: This uses course_user pivot (many-to-many) table.
        // If your pivot is named differently, change "course_user" to your actual pivot table name.

// ...

$enrollmentsByCourse = DB::table('enrollments')
    ->select('course_id', DB::raw('COUNT(*) as total'))
    ->groupBy('course_id')
    ->orderByDesc('total')
    ->limit(5)
    ->get();

$topCourses = Course::whereIn('id', $enrollmentsByCourse->pluck('course_id'))
    ->get()
    ->keyBy('id');

        // ================== CHART DATA (LAST 7 DAYS) ==================
        $start = Carbon::today()->subDays(6); // 7 days including today
        $end   = Carbon::today()->endOfDay();

        // Labels: Mon, Tue, Wed...
        $days = collect(range(0, 6))
            ->map(fn ($i) => $start->copy()->addDays($i)->format('D'))
            ->toArray();

        // --- Users series ---
        $usersRaw = User::whereBetween('created_at', [$start, $end])
            ->selectRaw("DATE(created_at) as d, COUNT(*) as total")
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('total', 'd');

        $usersSeries = collect(range(0, 6))
            ->map(function ($i) use ($start, $usersRaw) {
                $dateKey = $start->copy()->addDays($i)->format('Y-m-d');
                return (int) ($usersRaw[$dateKey] ?? 0);
            })
            ->toArray();

        // --- Enrollments series ---
        // Uses enrollments table (created_at column must exist)
        $enrollmentsRaw = DB::table('enrollments')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("DATE(created_at) as d, COUNT(*) as total")
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('total', 'd');

        $enrollmentsSeries = collect(range(0, 6))
            ->map(function ($i) use ($start, $enrollmentsRaw) {
                $dateKey = $start->copy()->addDays($i)->format('Y-m-d');
                return (int) ($enrollmentsRaw[$dateKey] ?? 0);
            })
            ->toArray();

        // --- Certificates series ---
        $certsRaw = Certificate::whereBetween('created_at', [$start, $end])
            ->selectRaw("DATE(created_at) as d, COUNT(*) as total")
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('total', 'd');

        $certsSeries = collect(range(0, 6))
            ->map(function ($i) use ($start, $certsRaw) {
                $dateKey = $start->copy()->addDays($i)->format('Y-m-d');
                return (int) ($certsRaw[$dateKey] ?? 0);
            })
            ->toArray();

        // ================== RETURN VIEW ==================
        return view('dashboards.admin', compact(
            'totalUsers',
            'totalStudents',
            'totalInstructors',
            'totalAdmins',
            'totalCourses',
            'totalEnrollments',
            'totalCertificates',
            'recentUsers',
            'recentCourses',
            'recentCertificates',
            'enrollmentsByCourse',
            'topCourses',
            'days',
            'usersSeries',
            'enrollmentsSeries',
            'certsSeries'
        ));
    }
}
