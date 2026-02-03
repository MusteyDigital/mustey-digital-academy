<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $course->title }}
            </h2>

            <a href="{{ route('courses.index') }}" class="underline text-gray-600">
                ← Back to Courses
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- COURSE INFO --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-2">
                <p class="text-gray-700">{{ $course->description ?? 'No description yet.' }}</p>
                <p class="text-sm text-gray-600">
                    <strong>Instructor:</strong> {{ $course->instructor->name }}
                </p>
            </div>

            {{-- STUDENT: ENROLL BUTTON --}}
            @if(auth()->check() && auth()->user()->role === 'student')
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Enrollment</h3>

                    <form method="POST" action="{{ route('courses.enroll', $course->id) }}">
                        @csrf
                        <x-primary-button>Enroll</x-primary-button>
                    </form>

                    <p class="text-xs text-gray-500 mt-2">
                        If you already enrolled, the system may block duplicate enrollments (as per your tests).
                    </p>
                </div>
            @endif

            {{-- LIVE SESSION (UI UPDATED) --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">Live Session</h3>
                        <p class="text-sm text-gray-500">
                            Join the class when scheduled and mark your live attendance.
                        </p>
                    </div>

                    {{-- Instructor: set/update live session --}}
                    @if(auth()->check() && auth()->user()->role === 'instructor' && $course->instructor_id === auth()->id())
                        <a class="inline-flex items-center gap-2 rounded-md border px-3 py-2 text-sm hover:bg-gray-50"
                           href="{{ route('courses.session.edit', $course->id) }}">
                            ⚙ <span>Set/Update Live Session</span>
                        </a>
                    @endif
                </div>

                @if($course->meeting_url)
                    <div class="rounded-lg border bg-gray-50 p-4 space-y-3">

                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="text-sm text-gray-700">
                                <span class="font-semibold">Starts:</span>
                                <span class="ml-1 inline-flex items-center rounded-full bg-white border px-3 py-1">
                                    {{ $course->starts_at ? $course->starts_at->format('D, M j, Y g:i A') : 'Not set' }}
                                </span>
                            </div>

                            <div>
                                <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 px-3 py-1 text-xs font-semibold">
                                    ✅ Scheduled
                                </span>
                            </div>
                        </div>

                        <div class="text-sm text-gray-700 break-all">
                            <span class="font-semibold">Meeting URL:</span>
                            <a class="underline text-blue-600 ml-1" href="{{ $course->meeting_url }}" target="_blank">
                                {{ $course->meeting_url }}
                            </a>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-2">

                            {{-- Student join link --}}
                            @if(auth()->check() && auth()->user()->role === 'student')
                                <a href="{{ $course->meeting_url }}" target="_blank">
                                    <x-primary-button>✅ Join Class</x-primary-button>
                                </a>
                            @else
                                <a class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-white"
                                   href="{{ $course->meeting_url }}" target="_blank">
                                    Open Link
                                </a>
                            @endif

                            {{-- Student: mark live attendance --}}
                            @if(auth()->check() && auth()->user()->role === 'student')
                                <form method="POST" action="{{ route('attendance.live.store', $course->id) }}">
                                    @csrf
                                    <x-primary-button>Mark Live Attendance</x-primary-button>
                                </form>
                            @endif

                            {{-- Instructor/Admin: view live attendance list --}}
                            @if(auth()->check() && in_array(auth()->user()->role, ['instructor', 'admin']))
                                <a class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-white"
                                   href="{{ route('attendance.live.index', $course->id) }}">
                                    📌 View Live Attendance List
                                </a>
                            @endif

                        </div>
                    </div>
                @else
                    <div class="rounded-lg border border-dashed p-5 text-gray-600 bg-gray-50">
                        <p class="font-semibold">No live session scheduled yet.</p>
                        <p class="text-sm text-gray-500 mt-1">
                            The instructor will add a meeting link and start time here.
                        </p>
                    </div>
                @endif
            </div>

            {{-- QUIZZES --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-3">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h3 class="font-semibold text-gray-800">Quizzes</h3>

                    {{-- Instructor: create quiz --}}
                    @if(auth()->check() && auth()->user()->role === 'instructor' && $course->instructor_id === auth()->id())
                        <a href="{{ route('quizzes.create', $course->id) }}">
                            <x-primary-button>+ Create Quiz</x-primary-button>
                        </a>
                    @endif
                </div>

                @php
                    $latestQuiz = \App\Models\Quiz::where('course_id', $course->id)->latest()->first();
                @endphp

                @if($latestQuiz)
                    <a class="underline text-blue-600" href="{{ route('quizzes.show', [$course->id, $latestQuiz->id]) }}">
                        Open Latest Quiz: {{ $latestQuiz->title }}
                    </a>
                @else
                    <p class="text-gray-600">No quizzes yet.</p>
                @endif
            </div>

            {{-- LESSONS + PROGRESS --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h3 class="font-semibold text-gray-800">Lessons</h3>

                    {{-- Instructor: add lesson --}}
                    @if(auth()->check() && auth()->user()->role === 'instructor' && $course->instructor_id === auth()->id())
                        <a href="{{ route('lessons.create', $course->id) }}">
                            <x-primary-button>+ Add Lesson</x-primary-button>
                        </a>
                    @endif
                </div>

                @php
                    $totalLessons = $course->lessons->count();
                    $completedCount = isset($completedLessonIds) ? count($completedLessonIds) : 0;
                    $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
                @endphp

                {{-- Student: progress bar --}}
                @if(auth()->check() && auth()->user()->role === 'student')
                    <div class="space-y-3">
                        <div class="space-y-2">
                            <p class="text-sm text-gray-700">
                                <strong>Progress:</strong> {{ $completedCount }}/{{ $totalLessons }} ({{ $percent }}%)
                            </p>

                            <div class="w-full bg-gray-200 rounded overflow-hidden">
                                <div class="bg-green-500 text-white text-xs px-2 py-1" style="width: {{ $percent }}%;">
                                    {{ $percent }}%
                                </div>
                            </div>
                        </div>

                        {{-- ✅ CERTIFICATE BUTTON (only when 100%) --}}
                        @if(auth()->check() && auth()->user()->role === 'student')
    <div class="bg-white shadow-sm sm:rounded-lg p-6">
        <h3 class="font-semibold text-gray-800 mb-2">Certificate</h3>
        <a href="{{ route('certificates.download', $course->id) }}">
            <x-primary-button>Download Certificate (PDF)</x-primary-button>
        </a>
        <p class="text-xs text-gray-500 mt-2">
            You may need to complete all lessons first (based on your rule).
        </p>
    </div>
@endif

                    </div>
                @endif

                @if($course->lessons->isEmpty())
                    <p class="text-gray-600">No lessons yet.</p>
                @else
                    <ul class="space-y-3">
                        @foreach($course->lessons as $lesson)
                            @php
                                $done = isset($completedLessonIds) && in_array($lesson->id, $completedLessonIds);
                            @endphp

                            <li class="border rounded-lg p-4 flex items-start justify-between gap-4 flex-wrap">
                                <div>
                                    <a class="underline text-blue-600" href="{{ route('lessons.show', [$course->id, $lesson->id]) }}">
                                        {{ $lesson->title }}
                                    </a>

                                    @if(auth()->check() && auth()->user()->role === 'student')
                                        <div class="text-sm mt-1">
                                            @if($done)
                                                <span class="text-green-700 font-semibold">✅ Completed</span>
                                            @else
                                                <span class="text-gray-500">Not completed</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="text-sm text-gray-500">
                                    Lesson ID: {{ $lesson->id }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
