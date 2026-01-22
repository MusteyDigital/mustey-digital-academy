<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Quiz Result: {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-2">
                <p><strong>Course:</strong> {{ $course->title }}</p>
                <p><strong>Score:</strong> {{ $attempt->score }} / {{ $attempt->total }}</p>

                @php
                    $percent = $attempt->total > 0 ? round(($attempt->score / $attempt->total) * 100) : 0;
                @endphp

                <p><strong>Percentage:</strong> {{ $percent }}%</p>

                <a class="underline" href="{{ route('courses.show', $course->id) }}">Back to Course</a>
            </div>
        </div>
    </div>
</x-app-layout>
