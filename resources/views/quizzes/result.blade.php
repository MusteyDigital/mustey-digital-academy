<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Quiz Result: {{ $quiz->title }}
            </h2>

            <a href="{{ route('courses.show', $course->id) }}" class="underline text-gray-600">
                ← Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">

                <div class="space-y-1">
                    <p class="text-gray-700"><span class="font-semibold">Course:</span> {{ $course->title }}</p>
                    <p class="text-gray-700"><span class="font-semibold">Score:</span> {{ $attempt->score }} / {{ $attempt->total }}</p>

                    @php
                        $percent = $attempt->total > 0 ? round(($attempt->score / $attempt->total) * 100) : 0;
                    @endphp

                    <p class="text-gray-700"><span class="font-semibold">Percentage:</span> {{ $percent }}%</p>
                </div>

                <div class="pt-2">
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="h-3 bg-green-500" style="width: {{ $percent }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Performance bar</p>
                </div>

                <div class="pt-2">
                    <a class="underline text-blue-600" href="{{ route('courses.show', $course->id) }}">
                        Back to Course
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
