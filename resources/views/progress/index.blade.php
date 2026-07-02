<x-app-layout>
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">My Progress</h1>
            <p class="text-sm text-slate-500 mt-1">Track your learning journey across all enrolled courses.</p>
        </div>
        <a href="{{ route('enrollments.my-courses') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            My Courses
        </a>
    </div>

    @if($courses->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-16 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-700 mb-2">No progress yet</h3>
            <p class="text-slate-500 text-sm mb-6">Enroll in a course to start tracking your progress.</p>
            <a href="{{ route('courses.index') }}"
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition">
                Browse Courses
            </a>
        </div>

    @else
        <div class="grid md:grid-cols-2 gap-5">
            @foreach($courses as $course)
                @php
                    $totalLessons = $course->lessons->count();
                    $completedCount = $course->lessons->whereIn('id', $completedLessonIds)->count();
                    $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
                    $lessonAttCount = $lessonAttendance[$course->id] ?? 0;
                    $liveAttCount = $liveAttendance[$course->id] ?? 0;
                @endphp

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">

                    {{-- Course Header --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-800 text-lg leading-tight truncate">{{ $course->title }}</h3>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $course->instructor->name ?? '—' }}
                            </p>
                        </div>
                        @if($percent === 100)
                            <span class="shrink-0 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">✅ Complete</span>
                        @else
                            <span class="shrink-0 bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">In Progress</span>
                        @endif
                    </div>

                    {{-- Progress Bar --}}
                    <div>
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-2">
                            <span>{{ $completedCount }} of {{ $totalLessons }} lessons completed</span>
                            <span class="font-bold text-slate-700 text-sm">{{ $percent }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div class="h-2.5 rounded-full transition-all {{ $percent === 100 ? 'bg-green-500' : 'bg-blue-600' }}"
                                 style="width: {{ $percent }}%;"></div>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                            <div class="text-xl font-bold text-slate-800">{{ $lessonAttCount }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">Lesson Attendance</div>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                            <div class="text-xl font-bold text-slate-800">{{ $liveAttCount }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">Live Attendance</div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-wrap gap-2 pt-1">
                        <a href="{{ route('progress.show', $course->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-xl font-medium hover:bg-blue-700 transition">
                            View Details
                        </a>
                        <a href="{{ route('courses.show', $course->id) }}"
                           class="inline-flex items-center px-4 py-2 border border-slate-200 text-slate-700 text-sm rounded-xl hover:bg-slate-50 transition">
                            Open Course
                        </a>
                        @if($course->meeting_url)
                            <a href="{{ $course->meeting_url }}" target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm rounded-xl font-medium hover:bg-indigo-700 transition">
                                Join Live
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
</x-app-layout>
