<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Progress — {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-5">

                <div class="flex items-center justify-between">
                    <a class="underline text-blue-600" href="{{ route('progress.index') }}">
                        ← Back to My Progress
                    </a>

                    <a class="underline text-blue-600" href="{{ route('courses.show', $course->id) }}">
                        Open Course
                    </a>
                </div>

                @php
                    $totalLessons = $course->lessons->count();
                    $completedCount = $course->lessons->whereIn('id', $completedLessonIds)->count();
                    $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
                @endphp

                <div>
                    <p><strong>Lesson Completion:</strong> {{ $completedCount }}/{{ $totalLessons }} ({{ $percent }}%)</p>
                    <div class="w-full bg-gray-200 rounded overflow-hidden mt-2">
                        <div class="bg-green-500" style="width: {{ $percent }}%; height: 10px;"></div>
                    </div>
                </div>

                <hr>

                <div class="space-y-2">
                    <h3 class="font-semibold text-lg">Live Session</h3>

                    @if($course->meeting_url)
                        <div class="flex items-center gap-3 flex-wrap">
                            <a class="underline text-blue-600" href="{{ $course->meeting_url }}" target="_blank">
                                Join Live Class
                            </a>

                            @if($hasLiveAttendance)
                                <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-sm">✅ Attendance Marked</span>
                            @else
                                <form method="POST" action="{{ route('attendance.live.store', $course->id) }}">
                                    @csrf
                                    <x-primary-button>Mark Live Attendance</x-primary-button>
                                </form>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-600">No live session scheduled yet.</p>
                    @endif
                </div>

                <hr>

                <div class="space-y-3">
                    <h3 class="font-semibold text-lg">Lessons</h3>

                    @if($course->lessons->isEmpty())
                        <p class="text-gray-600">No lessons yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border px-4 py-2 text-left">Lesson</th>
                                        <th class="border px-4 py-2 text-left">Completed</th>
                                        <th class="border px-4 py-2 text-left">Attendance</th>
                                        <th class="border px-4 py-2 text-left">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($course->lessons as $lesson)
                                        @php
                                            $done = in_array($lesson->id, $completedLessonIds);
                                            $att = in_array($lesson->id, $lessonAttendanceIds);
                                        @endphp
                                        <tr>
                                            <td class="border px-4 py-2">{{ $lesson->title }}</td>
                                            <td class="border px-4 py-2">
                                                @if($done)
                                                    <span class="text-green-700 font-semibold">✅ Yes</span>
                                                @else
                                                    <span class="text-gray-500">No</span>
                                                @endif
                                            </td>
                                            <td class="border px-4 py-2">
                                                @if($att)
                                                    <span class="text-green-700 font-semibold">✅ Marked</span>
                                                @else
                                                    <span class="text-gray-500">Not marked</span>
                                                @endif
                                            </td>
                                            <td class="border px-4 py-2">
                                                <a class="underline text-blue-600"
                                                   href="{{ route('lessons.show', [$course->id, $lesson->id]) }}">
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
        </div>
    </div>
</x-app-layout>
