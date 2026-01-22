<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Quiz: {{ $quiz->title }} — {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">

                <a class="underline" href="{{ route('quiz-questions.create', [$course->id, $quiz->id]) }}">
                    + Add Question
                </a>

                <h3 class="font-semibold">Questions</h3>

                @if($quiz->questions->isEmpty())
                    <p>No questions yet.</p>
                @else
                    <ol class="list-decimal pl-6 space-y-3">
                        @foreach($quiz->questions as $q)
                            <li>
                                <div class="font-semibold">{{ $q->question }}</div>
                                <div class="text-sm text-gray-600">
                                    A: {{ $q->option_a }} |
                                    B: {{ $q->option_b }} |
                                    C: {{ $q->option_c }} |
                                    D: {{ $q->option_d }}
                                    <br>
                                    Correct: {{ strtoupper($q->correct_option) }}
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
