<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">

                <div>
                    <p class="text-gray-700">{{ $course->description }}</p>
                    <p class="text-sm text-gray-600 mt-2">
                        Instructor: {{ $course->instructor->name }}
                    </p>
                </div>

                {{-- Student enroll button --}}
                @if(auth()->user()->role === 'student')
                    <form method="POST" action="{{ route('courses.enroll', $course->id) }}">
                        @csrf
                        <x-primary-button>Enroll</x-primary-button>
                    </form>
                @endif

                <hr>

                <div>
                    <h3 class="font-semibold text-lg">Lessons</h3>

                    @if($course->lessons->isEmpty())
                        <p class="text-gray-600">No lessons yet.</p>
                    @else
                        <ul class="list-disc ml-6 mt-2 space-y-1">
                            @foreach($course->lessons as $lesson)
                                <li>
                                    <a class="underline" href="{{ route('lessons.show', [$course->id, $lesson->id]) }}">
                                        {{ $lesson->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if(auth()->user()->role === 'instructor' && $course->instructor_id === auth()->id())
                        <div class="mt-4">
                            <a class="underline font-semibold" href="{{ route('lessons.create', $course->id) }}">
                                + Add Lesson
                            </a>
                        </div>
                    @endif
		@if(auth()->user()->role === 'instructor' && $course->instructor_id === auth()->id())
    <hr class="my-4">

    <h3 class="font-semibold text-lg">Enrolled Students</h3>

    @if($course->students->isEmpty())
        <p class="text-gray-600">No students enrolled yet.</p>
    @else
        <ul class="list-disc ml-6 mt-2 space-y-1">
            @foreach($course->students as $student)
                <li>
                    {{ $student->name }} ({{ $student->email }}) - {{ $student->pivot->status }}
                </li>
            @endforeach
        </ul>
    @endif
@endif

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
