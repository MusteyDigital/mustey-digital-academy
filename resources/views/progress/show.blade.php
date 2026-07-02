<x-app-layout>
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <a href="{{ route('progress.index') }}"
               class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition">
                ← Back to My Progress
            </a>
            <h1 class="text-2xl font-bold text-slate-800 mt-1">{{ $course->title }}</h1>
        </div>
        <a href="{{ route('courses.show', $course->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition">
            Open Course
        </a>
    </div>

    @php
        $totalLessons = $course->lessons->count();
        $completedCount = $course->lessons->whereIn('id', $completedLessonIds)->count();
        $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
    @endphp

    {{-- Progress Summary --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between text-sm mb-2">
            <span class="font-semibold text-slate-800">Lesson Completion</span>
            <span class="font-bold text-slate-800">{{ $completedCount }}/{{ $totalLessons }} ({{ $percent }}%)</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
            <div class="h-2.5 rounded-full transition-all {{ $percent == 100 ? 'bg-green-500' : 'bg-blue-600' }}"
                 style="width: {{ $percent }}%;"></div>
        </div>
    </div>

    {{-- Live Session --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3">
        <h3 class="font-bold text-slate-800 text-lg">Live Session</h3>

        @if($course->meeting_url)
            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ $course->meeting_url }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm rounded-xl font-medium hover:bg-indigo-700 transition">
                    Join Live Class
                </a>

                @if($hasLiveAttendance)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-green-100 text-green-800 text-xs font-semibold">
                        ✅ Attendance Marked
                    </span>
                @else
                    <form method="POST" action="{{ route('attendance.live.store', $course->id) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-xl font-medium hover:bg-blue-700 transition">
                            Mark Live Attendance
                        </button>
                    </form>
                @endif
            </div>
        @else
            <p class="text-slate-500 text-sm">No live session scheduled yet.</p>
        @endif
    </div>

    {{-- Lessons --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="font-bold text-slate-800 text-lg">Lessons</h3>

        @if($course->lessons->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-200 p-8 bg-slate-50 text-center text-slate-500 text-sm">
                No lessons yet.
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-slate-700 border-b border-slate-200">Lesson</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-700 border-b border-slate-200">Completed</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-700 border-b border-slate-200">Attendance</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-700 border-b border-slate-200">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($course->lessons as $lesson)
                            @php
                                $done = in_array($lesson->id, $completedLessonIds);
                                $att = in_array($lesson->id, $lessonAttendanceIds);
                            @endphp
                            <tr class="border-b border-slate-100 last:border-b-0 hover:bg-slate-50 transition">
                                <td class="px-4 py-3 text-slate-800 font-medium">{{ $lesson->title }}</td>
                                <td class="px-4 py-3">
                                    @if($done)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">✅ Yes</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-medium">No</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($att)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">✅ Marked</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-medium">Not marked</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('lessons.show', [$course->id, $lesson->id]) }}"
                                       class="text-blue-600 hover:underline font-medium">
                                        Open Lesson
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
</x-app-layout>
