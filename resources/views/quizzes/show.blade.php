<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    {{ $quiz->title }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">{{ $course->title }}</p>
            </div>

            <a href="{{ route('courses.show', $course->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Course
            </a>
        </div>
    </x-slot>

    @php
        $attemptId = request('attempt');
        $activeAttempt = null;

        if ($attemptId && auth()->check() && auth()->user()->role === 'student') {
            $activeAttempt = \App\Models\QuizAttempt::where('id', $attemptId)
                ->where('quiz_id', $quiz->id)
                ->where('user_id', auth()->id())
                ->first();
        }

        $totalQuestions = max($quiz->questions->count(), 1);
        $timeRemainingSeconds = null;

        if ($activeAttempt && !is_null($quiz->time_limit_minutes)) {
            $endTime = $activeAttempt->created_at->copy()->addMinutes($quiz->time_limit_minutes);
            $timeRemainingSeconds = max(now()->diffInSeconds($endTime, false), 0);
        }

        $isInstructorOrAdmin = auth()->check() && in_array(auth()->user()->role, ['instructor', 'admin']);
    @endphp

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Quiz Overview Card --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <h3 class="font-semibold text-slate-800 text-lg">{{ $quiz->title }}</h3>

                    <div class="flex flex-wrap gap-2">
                        @if($quiz->is_published)
                            <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 border border-green-200 px-3 py-1 text-sm font-semibold">
                                Published
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200 px-3 py-1 text-sm font-semibold">
                                Draft
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="rounded-xl border border-slate-200 p-4 bg-slate-50">
                        <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Questions</div>
                        <div class="text-2xl font-bold text-slate-800">{{ $quiz->questions->count() }}</div>
                    </div>

                    <div class="rounded-xl border border-slate-200 p-4 bg-slate-50">
                        <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Pass Mark</div>
                        <div class="text-2xl font-bold text-slate-800">{{ $quiz->pass_mark ?? 0 }}%</div>
                    </div>

                    <div class="rounded-xl border border-slate-200 p-4 bg-slate-50">
                        <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Attempt Limit</div>
                        <div class="text-2xl font-bold text-slate-800">
                            {{ is_null($quiz->max_attempts) ? '∞' : $quiz->max_attempts }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 p-4 bg-slate-50">
                        <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Time Limit</div>
                        <div class="text-2xl font-bold text-slate-800">
                            {{ is_null($quiz->time_limit_minutes) ? 'No limit' : $quiz->time_limit_minutes . ' min' }}
                        </div>
                    </div>
                </div>

                @if($isInstructorOrAdmin)
                    <div class="flex flex-wrap gap-3 pt-2">
                        <a href="{{ route('quiz-questions.create', [$course->id, $quiz->id]) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Question
                        </a>

                        <a href="{{ route('instructor.quizzes.analytics', [$course->id, $quiz->id]) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 border border-slate-200 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            View Analytics
                        </a>

                        <form method="POST" action="{{ route('quizzes.toggle-publish', [$course->id, $quiz->id]) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl text-white transition {{ $quiz->is_published ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                                {{ $quiz->is_published ? 'Unpublish Quiz' : 'Publish Quiz' }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Instructor: Question List --}}
            @if($isInstructorOrAdmin)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-slate-800 text-lg">Quiz Questions</h3>
                        <span class="text-sm text-slate-500">Total: {{ $quiz->questions->count() }}</span>
                    </div>

                    @if($quiz->questions->isEmpty())
                        <div class="rounded-xl border border-dashed border-slate-300 p-6 bg-slate-50 text-slate-600 text-sm text-center">
                            No questions added yet.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($quiz->questions as $index => $question)
                                <div class="border border-slate-200 rounded-xl p-4">
                                    <div class="font-semibold text-slate-800">
                                        {{ $index + 1 }}. {{ $question->question }}
                                    </div>

                                    <div class="mt-3 space-y-1 text-sm text-slate-600">
                                        <div>A. {{ $question->option_a }}</div>
                                        <div>B. {{ $question->option_b }}</div>
                                        <div>C. {{ $question->option_c }}</div>
                                        <div>D. {{ $question->option_d }}</div>
                                    </div>

                                    <div class="mt-3 flex items-center justify-between flex-wrap gap-3">
                                        <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 border border-green-200 px-3 py-1 text-xs font-semibold">
                                            Correct Option: {{ strtoupper($question->correct_option) }}
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('quiz-questions.edit', [$course->id, $quiz->id, $question->id]) }}"
                                               class="inline-flex items-center px-3 py-1.5 text-sm border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-50 transition">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('quiz-questions.destroy', [$course->id, $quiz->id, $question->id]) }}"
                                                  onsubmit="return confirm('Are you sure you want to delete this question?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1.5 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- Student: Quiz Status --}}
            @if(auth()->check() && auth()->user()->role === 'student')
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <h3 class="font-semibold text-slate-800 text-lg">Your Quiz Status</h3>

                        @if(!is_null($remainingAttempts))
                            <span class="inline-flex items-center rounded-full bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 text-sm font-semibold">
                                Remaining Attempts: {{ $remainingAttempts }}
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded-xl border border-slate-200 p-4 bg-slate-50">
                            <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Attempts Used</div>
                            <div class="text-2xl font-bold text-slate-800">{{ $studentAttemptsCount }}</div>
                        </div>

                        <div class="rounded-xl border border-slate-200 p-4 bg-slate-50">
                            <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Best Score</div>
                            <div class="text-2xl font-bold text-slate-800">
                                {{ is_null($studentBestScore) ? '—' : $studentBestScore }}
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 p-4 bg-slate-50">
                            <div class="text-xs text-slate-500 uppercase tracking-wide mb-1">Best Percentage</div>
                            <div class="text-2xl font-bold text-slate-800">
                                {{ is_null($studentBestPercentage) ? '—' : number_format($studentBestPercentage, 2) . '%' }}
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('quizzes.attempts', [$course->id, $quiz->id]) }}"
                           class="inline-flex items-center px-4 py-2 border border-slate-200 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 transition">
                            Attempt History
                        </a>

                        @if(!$activeAttempt && (is_null($remainingAttempts) || $remainingAttempts > 0))
                            <form method="POST" action="{{ route('quizzes.start', [$course->id, $quiz->id]) }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                                    Start Quiz
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Active Attempt --}}
            @if($activeAttempt)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h3 class="font-semibold text-slate-800 text-lg">Quiz Attempt</h3>
                            <p class="text-sm text-slate-500">Answer all questions and submit before time runs out.</p>
                        </div>

                        @if(!is_null($timeRemainingSeconds))
                            <div id="quiz-timer"
                                 data-seconds="{{ $timeRemainingSeconds }}"
                                 class="inline-flex items-center rounded-full bg-red-50 text-red-700 border border-red-200 px-4 py-2 text-sm font-semibold">
                                Time Left: --
                            </div>
                        @endif
                    </div>

                    <form id="quiz-submit-form" method="POST" action="{{ route('quizzes.submit', [$course->id, $quiz->id]) }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="attempt_id" value="{{ $activeAttempt->id }}">

                        @foreach($quiz->questions as $index => $question)
                            <div class="border border-slate-200 rounded-xl p-5">
                                <div class="font-semibold text-slate-800 mb-3">
                                    {{ $index + 1 }}. {{ $question->question }}
                                </div>

                                <div class="space-y-2">
                                    @foreach(['a','b','c','d'] as $opt)
                                        @php $label = 'option_'.$opt; @endphp
                                        @if(!empty($question->$label))
                                            <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500">
                                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $opt }}" required
                                                       class="mt-0.5 accent-blue-600">
                                                <span class="text-sm text-slate-700">
                                                    <span class="font-semibold text-blue-600">{{ strtoupper($opt) }}.</span>
                                                    {{ $question->$label }}
                                                </span>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="flex justify-end">
                            <button type="submit" id="quiz-submit-btn"
                                    class="inline-flex items-center gap-2 px-8 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed">
                                <span id="quiz-submit-label">Submit Quiz</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>

    @if($activeAttempt && !is_null($timeRemainingSeconds))
        <script>
            (function () {
                const timerElement = document.getElementById('quiz-timer');
                const form = document.getElementById('quiz-submit-form');
                const submitBtn = document.getElementById('quiz-submit-btn');
                const submitLabel = document.getElementById('quiz-submit-label');

                if (!timerElement || !form) return;

                let remaining = parseInt(timerElement.dataset.seconds || '0', 10);
                let intervalId = null;
                let hasSubmitted = false;

                function formatTime(seconds) {
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                }

                function autoSubmit() {
                    if (hasSubmitted) return;
                    hasSubmitted = true;
                    clearInterval(intervalId);
                    if (submitBtn) submitBtn.disabled = true;
                    if (submitLabel) submitLabel.textContent = 'Time\'s up — submitting...';
                    form.submit();
                }

                function updateTimer() {
                    timerElement.textContent = 'Time Left: ' + formatTime(Math.max(remaining, 0));

                    if (remaining <= 0) {
                        autoSubmit();
                        return;
                    }

                    remaining--;
                }

                updateTimer();
                intervalId = setInterval(updateTimer, 1000);

                // Guard against double submission if the student clicks Submit manually
                form.addEventListener('submit', function () {
                    if (hasSubmitted) return;
                    hasSubmitted = true;
                    clearInterval(intervalId);
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        if (submitLabel) submitLabel.textContent = 'Submitting...';
                    }
                });
            })();
        </script>
    @endif
</x-app-layout>