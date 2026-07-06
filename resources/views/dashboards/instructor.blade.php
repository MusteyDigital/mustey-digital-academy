<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Instructor Dashboard
            </h2>

            <a href="{{ route('courses.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 text-sm font-medium shadow-sm hover:bg-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Course
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome --}}
            <div class="rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-sm p-6 sm:p-8">
                <h3 class="text-xl font-semibold">
                    Welcome back, {{ auth()->user()->name }}
                </h3>
                <p class="text-blue-100 mt-1">
                    Manage your courses, modules, lessons and quizzes from one place.
                </p>
            </div>

            {{-- Top stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <x-stat-card label="Total Courses" :value="$courses->count()" color="primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </x-stat-card>
                <x-stat-card label="Total Modules" :value="$courses->sum('modules_count')" color="accent">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </x-stat-card>
                <x-stat-card label="Total Lessons" :value="$courses->sum('lessons_count')" color="purple">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                </x-stat-card>
                <x-stat-card label="Paid Enrollments" :value="$totalPaidEnrollments ?? 0" color="warning">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>
                </x-stat-card>
                <x-stat-card label="Total Revenue" value="₦{{ number_format($totalRevenue ?? 0) }}" color="success">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v-2m0-8c-1.11 0-2.08.402-2.599 1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </x-stat-card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                    <h3 class="font-semibold text-slate-800 text-lg mb-4">Top Earning Courses</h3>

                    @if(($topEarningCourses ?? collect())->isEmpty())
                        <div class="rounded-xl border border-dashed border-slate-200 p-6 bg-slate-50 text-slate-500 text-sm">
                            No payment revenue yet.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($topEarningCourses as $earningCourse)
                                <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 p-4">
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $earningCourse->title }}</div>
                                        <div class="text-sm text-slate-500">Course Revenue</div>
                                    </div>
                                    <div class="text-lg font-bold text-green-700">
                                        ₦{{ number_format($earningCourse->total_revenue ?? 0) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                    <h3 class="font-semibold text-slate-800 text-lg mb-4">Recent Payments</h3>

                    @if(($recentPayments ?? collect())->isEmpty())
                        <div class="rounded-xl border border-dashed border-slate-200 p-6 bg-slate-50 text-slate-500 text-sm">
                            No recent payments yet.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($recentPayments as $payment)
                                <div class="rounded-xl border border-slate-200 p-4">
                                    <div class="flex items-center justify-between gap-3 flex-wrap">
                                        <div>
                                            <div class="font-semibold text-slate-900">{{ $payment->course->title ?? 'Course' }}</div>
                                            <div class="text-sm text-slate-500">{{ $payment->user->name ?? 'Student' }} • {{ $payment->reference }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-green-700">₦{{ number_format($payment->amount) }}</div>
                                            <div class="text-xs text-slate-500">
                                                {{ $payment->paid_at ? $payment->paid_at->format('M j, Y g:i A') : $payment->created_at->format('M j, Y g:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Courses --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h3 class="font-semibold text-slate-800 text-lg">
                        Courses You Teach
                    </h3>

                    <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                        View All Courses
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>

                @if($courses->isEmpty())
                    <div class="mt-4 rounded-xl border border-dashed border-slate-200 p-8 bg-slate-50 text-slate-500 text-center">
                        <p class="font-semibold text-slate-700">No courses yet</p>
                        <p class="text-sm mt-1">You have not created any courses yet.</p>

                        <div class="mt-4">
                            <a href="{{ route('courses.create') }}"
                               class="inline-flex items-center rounded-xl bg-blue-600 text-white px-5 py-2.5 text-sm font-semibold shadow-sm hover:bg-blue-700 transition">
                                + Create Your First Course
                            </a>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mt-6">
                        @foreach($courses as $course)
                            <div class="rounded-2xl border border-slate-200 p-5 hover:shadow-md hover:border-blue-200 transition bg-white space-y-3">

                                <div>
                                    <a class="text-lg font-semibold text-slate-900 hover:text-blue-600 transition"
                                       href="{{ route('courses.show', $course->id) }}">
                                        {{ $course->title }}
                                    </a>

                                    <p class="text-sm text-slate-500 mt-1 line-clamp-2">
                                        {{ $course->description ?? 'No description yet.' }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 text-blue-700 px-3 py-1 text-xs font-medium">
                                        Instructor View
                                    </span>

                                    <span class="text-xs text-slate-500">
                                        Modules: {{ $course->modules_count ?? 0 }} • Lessons: {{ $course->lessons_count ?? 0 }}
                                    </span>

                                    <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 px-3 py-1 text-xs font-semibold">
                                        Revenue: ₦{{ number_format((int) (($revenueByCourse[$course->id]->total_revenue ?? 0))) }}
                                    </span>
                                </div>

                                {{-- ACTIONS --}}
                                <div class="flex flex-wrap gap-2 pt-2">

                                    <a href="{{ route('instructor.courses.edit', $course->id) }}"
                                       class="inline-flex items-center rounded-xl bg-blue-600 text-white px-4 py-2 text-sm font-medium hover:bg-blue-700 transition">
                                        Manage Course
                                    </a>

                                    <a href="{{ route('instructor.modules.index', $course->id) }}"
                                       class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 transition">
                                        Manage Modules
                                    </a>

                                    <a href="{{ route('courses.show', $course->id) }}"
                                       class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 transition">
                                        View Student Page
                                    </a>

                                    <form method="POST" action="{{ route('instructor.courses.destroy', $course->id) }}"
                                          onsubmit="return confirm('Delete this course? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="inline-flex items-center rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 hover:bg-red-100 transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>

                                <p class="text-xs text-slate-400 pt-1">
                                    Tip: Only the course owner (you) or an admin can delete.
                                </p>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>