<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Progress
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">

                @if($courses->isEmpty())
                    <p class="text-gray-600">You have not enrolled in any course yet.</p>
                    <a class="underline text-blue-600" href="{{ route('courses.index') }}">Browse Courses</a>
                @else
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($courses as $course)
                            @php
                                $totalLessons = $course->lessons->count();
                                $completedCount = $course->lessons->whereIn('id', $completedLessonIds)->count();
                                $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

                                $lessonAttCount = $lessonAttendance[$course->id] ?? 0;
                                $liveAttCount = $liveAttendance[$course->id] ?? 0;
                            @endphp

                            <div class="border rounded-lg p-4 space-y-3">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="font-semibold text-lg">{{ $course->title }}</h3>
                                        <p class="text-sm text-gray-600">
                                            Instructor: {{ $course->instructor->name ?? '—' }}
                                        </p>
                                    </div>

                                    <a class="underline text-sm text-blue-600"
                                       href="{{ route('courses.show', $course->id) }}">
                                        Open
                                    </a>
                                </div>

                                <div>
                                    <p class="text-sm">
                                        <strong>Lessons:</strong> {{ $completedCount }}/{{ $totalLessons }} ({{ $percent }}%)
                                    </p>

                                    <div class="w-full bg-gray-200 rounded overflow-hidden mt-2">
                                        <div class="bg-green-500" style="width: {{ $percent }}%; height: 10px;"></div>
                                    </div>
                                </div>

                                <div class="text-sm text-gray-700">
                                    <p><strong>Lesson Attendance:</strong> {{ $lessonAttCount }}</p>
                                    <p><strong>Live Attendance:</strong> {{ $liveAttCount }}</p>
                                </div>

                                <div class="flex gap-3 flex-wrap">
                                    <a class="underline text-blue-600"
                                       href="{{ route('progress.show', $course->id) }}">
                                        View Details
                                    </a>

                                    @if($course->meeting_url)
                                        <a class="underline text-blue-600" href="{{ $course->meeting_url }}" target="_blank">
                                            Join Live Class
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
