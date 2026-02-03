<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Quiz: {{ $quiz->title }} — {{ $course->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Manage questions (instructor) or view quiz details.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('courses.show', $course->id) }}"
                   class="text-sm text-gray-600 hover:text-gray-900 underline">
                    ← Back to Course
                </a>

                @if(auth()->check() && auth()->user()->role === 'instructor' && $course->instructor_id === auth()->id())
                    <a href="{{ route('quiz-questions.create', [$course->id, $quiz->id]) }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-md text-sm font-semibold hover:bg-gray-700 transition">
                        + Add Question
                    </a>
                @endif

                @if(auth()->check() && auth()->user()->role === 'admin')
                    <a href="{{ route('quiz-questions.create', [$course->id, $quiz->id]) }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-md text-sm font-semibold hover:bg-gray-700 transition">
                        + Add Question
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">

                {{-- Flash message --}}
                @if(session('success'))
                    <div class="rounded-md bg-green-50 p-4 text-green-800 border border-green-200">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-lg text-gray-800">Questions</h3>

                    <span class="text-sm text-gray-600">
                        Total: <span class="font-semibold">{{ $quiz->questions->count() }}</span>
                    </span>
                </div>

                @if($quiz->questions->isEmpty())
                    <div class="rounded-md border border-dashed p-6 text-center text-gray-600">
                        <p class="font-semibold">No questions yet.</p>
                        <p class="text-sm mt-1">Add questions to make this quiz available for students.</p>

                        @if(auth()->check() && ((auth()->user()->role === 'instructor' && $course->instructor_id === auth()->id()) || auth()->user()->role === 'admin'))
                            <div class="mt-4">
                                <a href="{{ route('quiz-questions.create', [$course->id, $quiz->id]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-gray-900 text-black rounded-md text-sm font-semibold hover:bg-gray-700 transition">
                                    + Add First Question
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    <ol class="space-y-4">
                        @foreach($quiz->questions as $index => $q)
                            <li class="rounded-lg border p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-sm text-gray-500 font-semibold">Question {{ $index + 1 }}</div>
                                        <div class="font-semibold text-gray-900 mt-1">
                                            {{ $q->question }}
                                        </div>
                                    </div>

                                    <div class="text-xs rounded-full px-3 py-1 bg-gray-100 text-gray-800 whitespace-nowrap">
                                        Correct: <span class="font-semibold">{{ strtoupper($q->correct_option) }}</span>
                                    </div>
                                </div>

                                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-700">
                                    <div class="rounded-md bg-gray-50 p-2">A: {{ $q->option_a }}</div>
                                    <div class="rounded-md bg-gray-50 p-2">B: {{ $q->option_b }}</div>
                                    <div class="rounded-md bg-gray-50 p-2">C: {{ $q->option_c }}</div>
                                    <div class="rounded-md bg-gray-50 p-2">D: {{ $q->option_d }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
