<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $course->title }} — {{ $lesson->title }}
            </h2>

            <a href="{{ route('courses.show', $course->id) }}" class="underline text-gray-600">
                ← Back to Course
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

            {{-- Lesson Content --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <h3 class="font-semibold text-gray-800 text-lg">
                    Lesson Content
                </h3>

                <div class="text-gray-700 whitespace-pre-line leading-relaxed">
                    {{ $lesson->content ?? 'No content yet.' }}
                </div>

                @if(!empty($lesson->video_url))
                    <div class="pt-2">
                        <a class="underline text-blue-600" href="{{ $lesson->video_url }}" target="_blank">
                            ▶ Watch Video
                        </a>
                    </div>
                @endif
            </div>

            {{-- Student Actions --}}
            @if(auth()->check() && auth()->user()->role === 'student')
                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-5">
                    <h3 class="font-semibold text-gray-800 text-lg">
                        Student Actions
                    </h3>

                    {{-- Completed --}}
                    @if(!empty($isCompleted) && $isCompleted)
                        <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800 font-semibold">
                            ✅ Completed
                        </div>
                    @else
                        <form method="POST" action="{{ route('lessons.complete', [$course->id, $lesson->id]) }}">
                            @csrf
                            <x-primary-button>Mark as Completed</x-primary-button>
                        </form>
                    @endif

                    <hr>

                    {{-- Mark Attendance --}}
                    <form method="POST" action="{{ route('attendance.store', [$course->id, $lesson->id]) }}">
                        @csrf
                        <x-primary-button>✅ Mark Attendance</x-primary-button>
                    </form>

                    <p class="text-xs text-gray-500">
                        Attendance is recorded once per student per lesson.
                    </p>
                </div>
            @endif

            {{-- Instructor/Admin Actions --}}
            @if(auth()->check() && in_array(auth()->user()->role, ['instructor', 'admin']))
                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-3">
                    <h3 class="font-semibold text-gray-800 text-lg">
                        Instructor/Admin Tools
                    </h3>

                    <a class="underline text-blue-600" href="{{ route('attendance.index', [$course->id, $lesson->id]) }}">
                        📌 View Attendance List
                    </a>

                    @if(auth()->user()->role === 'instructor')
                        <p class="text-xs text-gray-500">
                            You can view attendance only for your own course.
                        </p>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
