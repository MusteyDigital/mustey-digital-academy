<x-app-layout>
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Quiz Result</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $quiz->title }} · {{ $course->title }}</p>
        </div>
        <a href="{{ route('courses.show', $course->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition">
            ← Back to Course
        </a>
    </div>

    {{-- Result Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 text-center space-y-6">

        {{-- Pass/Fail Icon --}}
        @if($passed)
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-green-600">Congratulations!</h2>
                <p class="text-slate-500 mt-1 text-sm">You passed this quiz.</p>
            </div>
        @else
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-red-500">Not Passed</h2>
                <p class="text-slate-500 mt-1 text-sm">Keep practicing — you'll get it next time!</p>
            </div>
        @endif

        {{-- Score Stats --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4">
                <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Score</div>
                <div class="text-3xl font-bold text-slate-800">{{ $attempt->score ?? 0 }}</div>
            </div>
            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4">
                <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Questions</div>
                <div class="text-3xl font-bold text-slate-800">{{ $totalQuestions }}</div>
            </div>
            <div class="bg-{{ $passed ? 'green' : 'red' }}-50 rounded-2xl border border-{{ $passed ? 'green' : 'red' }}-200 p-4">
                <div class="text-xs text-{{ $passed ? 'green' : 'red' }}-600 uppercase tracking-wide mb-1">Percentage</div>
                <div class="text-3xl font-bold text-{{ $passed ? 'green' : 'red' }}-700">{{ number_format($percentage, 1) }}%</div>
            </div>
        </div>

        @if(!is_null($quiz->pass_mark))
            <p class="text-sm text-slate-500">Pass mark: <strong>{{ $quiz->pass_mark }}%</strong></p>
        @endif

        {{-- Actions --}}
        <div class="flex flex-wrap justify-center gap-3 pt-2">
            <a href="{{ route('quizzes.attempts.review', [$course->id, $quiz->id, $attempt->id]) }}"
               class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm rounded-xl font-medium hover:bg-indigo-700 transition">
                Review Answers
            </a>
            <a href="{{ route('quizzes.attempts', [$course->id, $quiz->id]) }}"
               class="inline-flex items-center px-5 py-2.5 border border-slate-200 text-slate-700 text-sm rounded-xl hover:bg-slate-50 transition">
                Attempt History
            </a>
            <a href="{{ route('quizzes.show', [$course->id, $quiz->id]) }}"
               class="inline-flex items-center px-5 py-2.5 border border-slate-200 text-slate-700 text-sm rounded-xl hover:bg-slate-50 transition">
                Back to Quiz
            </a>
            <a href="{{ route('courses.show', $course->id) }}"
               class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm rounded-xl font-medium hover:bg-blue-700 transition">
                Back to Course
            </a>
        </div>
    </div>

</div>
</x-app-layout>
