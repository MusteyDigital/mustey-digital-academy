<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                👨‍🏫 Instructor Dashboard
            </h2>

            <a href="{{ route('courses.create') }}"
               class="inline-flex items-center gap-2 rounded-md bg-gray-900 text-white px-4 py-2 text-sm hover:bg-black">
                ➕ Create Course
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800">
                    Welcome, Instructor {{ auth()->user()->name }} 👋
                </h3>
                <p class="text-gray-600 mt-1">
                    Manage your courses, modules, lessons and quizzes.
                </p>
            </div>

            {{-- Top stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white border rounded-lg p-5">
                    <div class="text-sm text-gray-500">Total Courses</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $courses->count() }}</div>
                </div>
                <div class="bg-white border rounded-lg p-5">
                    <div class="text-sm text-gray-500">Total Modules</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $courses->sum('modules_count') }}</div>
                </div>
                <div class="bg-white border rounded-lg p-5">
                    <div class="text-sm text-gray-500">Total Lessons</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $courses->sum('lessons_count') }}</div>
                </div>
                <div class="bg-white border rounded-lg p-5">
                    <div class="text-sm text-gray-500">Paid Enrollments</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $totalPaidEnrollments ?? 0 }}</div>
                </div>
                <div class="bg-white border rounded-lg p-5">
                    <div class="text-sm text-gray-500">Total Revenue</div>
                    <div class="text-2xl font-bold text-gray-900">₦{{ number_format($totalRevenue ?? 0) }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
                    <h3 class="font-semibold text-gray-800 text-lg mb-4">💰 Top Earning Courses</h3>

                    @if(($topEarningCourses ?? collect())->isEmpty())
                        <div class="rounded-lg border border-dashed p-6 bg-gray-50 text-gray-600">
                            No payment revenue yet.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($topEarningCourses as $earningCourse)
                                <div class="flex items-center justify-between gap-3 border rounded-lg p-4">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $earningCourse->title }}</div>
                                        <div class="text-sm text-gray-500">Course Revenue</div>
                                    </div>
                                    <div class="text-lg font-bold text-green-700">
                                        ₦{{ number_format($earningCourse->total_revenue ?? 0) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
                    <h3 class="font-semibold text-gray-800 text-lg mb-4">🧾 Recent Payments</h3>

                    @if(($recentPayments ?? collect())->isEmpty())
                        <div class="rounded-lg border border-dashed p-6 bg-gray-50 text-gray-600">
                            No recent payments yet.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($recentPayments as $payment)
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center justify-between gap-3 flex-wrap">
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $payment->course->title ?? 'Course' }}</div>
                                            <div class="text-sm text-gray-500">{{ $payment->user->name ?? 'Student' }} • {{ $payment->reference }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-green-700">₦{{ number_format($payment->amount) }}</div>
                                            <div class="text-xs text-gray-500">
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
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h3 class="font-semibold text-gray-800 text-lg">
                        📚 Courses You Teach
                    </h3>

                    <a href="{{ route('courses.index') }}" class="underline text-blue-600 text-sm">
                        View All Courses →
                    </a>
                </div>

                @if($courses->isEmpty())
                    <div class="mt-4 rounded-lg border border-dashed p-6 bg-gray-50 text-gray-600 text-center">
                        <p class="font-semibold">No courses yet</p>
                        <p class="text-sm mt-1">You have not created any courses yet.</p>

                        <div class="mt-4">
                            <a href="{{ route('courses.create') }}">
                                <x-primary-button>+ Create Your First Course</x-primary-button>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mt-6">
                        @foreach($courses as $course)
                            <div class="border rounded-lg p-5 hover:shadow-md transition bg-white space-y-3">

                                <div>
                                    <a class="text-lg font-semibold text-gray-900 underline"
                                       href="{{ route('courses.show', $course->id) }}">
                                        {{ $course->title }}
                                    </a>

                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                        {{ $course->description ?? 'No description yet.' }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-xs">
                                        Instructor View
                                    </span>

                                    <span class="text-xs text-gray-500">
                                        Modules: {{ $course->modules_count ?? 0 }} • Lessons: {{ $course->lessons_count ?? 0 }}
                                    </span>

                                    <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 px-3 py-1 text-xs font-semibold">
                                        Revenue: ₦{{ number_format((int) (($revenueByCourse[$course->id]->total_revenue ?? 0))) }}
                                    </span>
                                </div>

                                {{-- ACTIONS --}}
                                <div class="flex flex-wrap gap-2 pt-2">

                                    <a href="{{ route('instructor.courses.edit', $course->id) }}"
                                       class="inline-flex items-center rounded-md bg-gray-900 text-white px-4 py-2 text-sm">
                                        Manage Course
                                    </a>

                                    <a href="{{ route('instructor.modules.index', $course->id) }}"
                                       class="inline-flex items-center rounded-md border px-3 py-2 text-sm hover:bg-gray-50">
                                        Manage Modules
                                    </a>

                                    <a href="{{ route('courses.show', $course->id) }}"
                                       class="inline-flex items-center rounded-md border px-3 py-2 text-sm hover:bg-gray-50">
                                        View Student Page
                                    </a>

                                    <form method="POST" action="{{ route('instructor.courses.destroy', $course->id) }}"
                                          onsubmit="return confirm('Delete this course? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="inline-flex items-center rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 hover:bg-red-100">
                                            🗑 Delete
                                        </button>
                                    </form>
                                </div>

                                <p class="text-xs text-gray-500 pt-1">
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
