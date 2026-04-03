<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Quiz Result
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $quiz->title }}</p>
            </div>

            <a href="{{ route('courses.show', $course->id) }}" class="underline text-gray-600">
                ← Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border p-6 space-y-5">
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-gray-900">Your Quiz Result</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $course->title }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-xl border p-4 bg-gray-50 text-center">
                        <div class="text-sm text-gray-500">Score</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">{{ $attempt->score ?? 0 }}</div>
                    </div>

                    <div class="rounded-xl border p-4 bg-gray-50 text-center">
                        <div class="text-sm text-gray-500">Total Questions</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">{{ $totalQuestions }}</div>
                    </div>

                    <div class="rounded-xl border p-4 bg-gray-50 text-center">
                        <div class="text-sm text-gray-500">Percentage</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($percentage, 2) }}%</div>
                    </div>
                </div>

                <div class="text-center">
                    @if($passed)
                        <div class="inline-flex items-center rounded-full bg-green-50 text-green-700 border border-green-200 px-4 py-2 text-sm font-semibold">
                            ✅ Passed
                        </div>
                    @else
                        <div class="inline-flex items-center rounded-full bg-red-50 text-red-700 border border-red-200 px-4 py-2 text-sm font-semibold">
                            ❌ Not Passed
                        </div>
                    @endif
                </div>

                <div class="flex justify-center gap-3 flex-wrap">
                    <a href="{{ route('quizzes.attempts.review', [$course->id, $quiz->id, $attempt->id]) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Review Answers
                    </a>

                    <a href="{{ route('quizzes.attempts', [$course->id, $quiz->id]) }}"
                       class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                        Attempt History
                    </a>

                    <a href="{{ route('quizzes.show', [$course->id, $quiz->id]) }}"
                       class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                        Back to Quiz
                    </a>

                    <a href="{{ route('courses.show', $course->id) }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Back to Course
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
