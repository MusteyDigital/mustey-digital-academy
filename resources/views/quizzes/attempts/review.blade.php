<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Review Attempt #{{ $attempt->id }} — {{ $quiz->title }}
            </h2>

            <a href="{{ route('quizzes.attempts', [$course->id, $quiz->id]) }}"
               class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Attempts
            </a>
        </div>
    </x-slot>

    @php
        $percent = $attempt->total > 0 ? round(($attempt->score / $attempt->total) * 100) : 0;
    @endphp

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">

                <div class="text-sm text-slate-700 space-y-1">
                    <p><span class="font-semibold">Score:</span> {{ $attempt->score }} / {{ $attempt->total }} ({{ $percent }}%)</p>
                    <p><span class="font-semibold">Submitted:</span> {{ optional($attempt->submitted_at)->format('Y-m-d H:i') ?? '—' }}</p>
                </div>

                <div class="pt-2 border-t border-slate-200">
                    <h3 class="font-semibold text-slate-800 mb-3">Questions</h3>

                    <ol class="space-y-4">
                        @foreach($quiz->questions as $i => $q)
                            @php
                                $a = $answers[$q->id] ?? null;
                                $chosen = $a?->selected_option;
                                $correct = $q->correct_option;
                                $isCorrect = $a?->is_correct;
                            @endphp

                            <li class="rounded-xl border border-slate-200 p-4">
                                <div class="font-semibold text-slate-900">
                                    {{ $i + 1 }}. {{ $q->question }}
                                </div>

                                <div class="text-sm mt-2 space-y-1">
                                    <p>
                                        <span class="font-semibold">Your answer:</span>
                                        <span class="{{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $chosen ? strtoupper($chosen) : '—' }}
                                        </span>
                                    </p>
                                    <p>
                                        <span class="font-semibold">Correct answer:</span>
                                        <span class="text-slate-800">{{ strtoupper($correct) }}</span>
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>