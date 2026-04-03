<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $quiz->title }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $course->title }}</p>
            </div>

            <a href="{{ route('courses.show', $course->id) }}" class="underline text-gray-600">
                ← Back to Course
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
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg border p-6 space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">{{ $quiz->title }}</h3>
                    </div>

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
                    <div class="rounded-xl border p-4 bg-gray-50">
                        <div class="text-sm text-gray-500">Questions</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">{{ $quiz->questions->count() }}</div>
                    </div>

                    <div class="rounded-xl border p-4 bg-gray-50">
                        <div class="text-sm text-gray-500">Pass Mark</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">{{ $quiz->pass_mark ?? 0 }}%</div>
                    </div>

                    <div class="rounded-xl border p-4 bg-gray-50">
                        <div class="text-sm text-gray-500">Attempt Limit</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">
                            {{ is_null($quiz->max_attempts) ? '∞' : $quiz->max_attempts }}
                        </div>
                    </div>

                    <div class="rounded-xl border p-4 bg-gray-50">
                        <div class="text-sm text-gray-500">Time Limit</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">
                            {{ is_null($quiz->time_limit_minutes) ? 'No limit' : $quiz->time_limit_minutes . ' min' }}
                        </div>
                    </div>
                </div>

                @if($isInstructorOrAdmin)
                    <div class="flex flex-wrap gap-3 pt-2">
                        <a href="{{ route('quiz-questions.create', [$course->id, $quiz->id]) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            + Add Question
                        </a>

                        <a href="{{ route('instructor.quizzes.analytics', [$course->id, $quiz->id]) }}"
                           class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                            View Analytics
                        </a>

                        <form method="POST" action="{{ route('quizzes.toggle-publish', [$course->id, $quiz->id]) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 rounded-lg text-white {{ $quiz->is_published ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                                {{ $quiz->is_published ? 'Unpublish Quiz' : 'Publish Quiz' }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            @if($isInstructorOrAdmin)
                <div class="bg-white shadow-sm sm:rounded-lg border p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800 text-lg">Quiz Questions</h3>
                        <span class="text-sm text-gray-500">Total: {{ $quiz->questions->count() }}</span>
                    </div>

                    @if($quiz->questions->isEmpty())
                        <div class="rounded-lg border border-dashed p-6 bg-gray-50 text-gray-600">
                            No questions added yet.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($quiz->questions as $index => $question)
                                <div class="border rounded-xl p-4">
                                    <div class="font-semibold text-gray-900">
                                        {{ $index + 1 }}. {{ $question->question }}
                                    </div>

                                    <div class="mt-3 space-y-2 text-sm text-gray-700">
                                        <div>A. {{ $question->option_a }}</div>
                                        <div>B. {{ $question->option_b }}</div>
                                        <div>C. {{ $question->option_c }}</div>
                                        <div>D. {{ $question->option_d }}</div>
                                    </div>

                                    <div class="mt-3">
                                        <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 border border-green-200 px-3 py-1 text-xs font-semibold">
<div class="mt-3 flex items-center justify-between flex-wrap gap-3">
    <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 border border-green-200 px-3 py-1 text-xs font-semibold">
        Correct Option: {{ strtoupper($question->correct_option) }}
    </span>
    <div class="flex items-center gap-2">
        <a href="{{ route('quiz-questions.edit', [$course->id, $quiz->id, $question->id]) }}"
           class="inline-flex items-center px-3 py-1.5 text-sm border rounded-lg text-gray-700 hover:bg-gray-50">
            Edit
        </a>
        <form method="POST" action="{{ route('quiz-questions.destroy', [$course->id, $quiz->id, $question->id]) }}"
              onsubmit="return confirm('Are you sure you want to delete this question?' );">
            @csrf
            @method("DELETE")
            <button type="submit"
                    class="inline-flex items-center px-3 py-1.5 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700">
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

            @if(auth()->check() && auth()->user()->role === 'student')
                <div class="bg-white shadow-sm sm:rounded-lg border p-6 space-y-4">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <h3 class="font-semibold text-gray-800 text-lg">Your Quiz Status</h3>

                        @if(!is_null($remainingAttempts))
                            <span class="inline-flex items-center rounded-full bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 text-sm font-semibold">
                                Remaining Attempts: {{ $remainingAttempts }}
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded-xl border p-4 bg-gray-50">
                            <div class="text-sm text-gray-500">Attempts Used</div>
                            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $studentAttemptsCount }}</div>
                        </div>

                        <div class="rounded-xl border p-4 bg-gray-50">
                            <div class="text-sm text-gray-500">Best Score</div>
                            <div class="text-2xl font-bold text-gray-900 mt-1">
                                {{ is_null($studentBestScore) ? '—' : $studentBestScore }}
                            </div>
                        </div>

                        <div class="rounded-xl border p-4 bg-gray-50">
                            <div class="text-sm text-gray-500">Best Percentage</div>
                            <div class="text-2xl font-bold text-gray-900 mt-1">
                                {{ is_null($studentBestPercentage) ? '—' : number_format($studentBestPercentage, 2) . '%' }}
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('quizzes.attempts', [$course->id, $quiz->id]) }}"
                           class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                            Attempt History
                        </a>

                        @if(!$activeAttempt && (is_null($remainingAttempts) || $remainingAttempts > 0))
                            <form method="POST" action="{{ route('quizzes.start', [$course->id, $quiz->id]) }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Start Quiz
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            @if($activeAttempt)
                <div class="bg-white shadow-sm sm:rounded-lg border p-6 space-y-5">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-lg">Quiz Attempt</h3>
                            <p class="text-sm text-gray-500">Answer all questions and submit before time runs out.</p>
                        </div>

                        @if(!is_null($timeRemainingSeconds))
                            <div id="quiz-timer"
                                 data-seconds="{{ $timeRemainingSeconds }}"
                                 class="inline-flex items-center rounded-full bg-red-50 text-red-700 border border-red-200 px-4 py-2 text-sm font-semibold">
                                Time Left: --
                            </div>
                        @endif
                    </div>

                    <form id="quiz-submit-form" method="POST" action="{{ route('quizzes.submit', [$course->id, $quiz->id]) }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="attempt_id" value="{{ $activeAttempt->id }}">

                        @foreach($quiz->questions as $index => $question)
                            <div class="border rounded-xl p-5">
                                <div class="font-semibold text-gray-900 mb-3">
                                    {{ $index + 1 }}. {{ $question->question }}
                                </div>

                                <div class="space-y-3">
                                    <label class="flex items-center gap-3">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="a" class="text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span class="text-gray-700">A. {{ $question->option_a }}</span>
                                    </label>

                                    <label class="flex items-center gap-3">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="b" class="text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span class="text-gray-700">B. {{ $question->option_b }}</span>
                                    </label>

                                    <label class="flex items-center gap-3">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="c" class="text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span class="text-gray-700">C. {{ $question->option_c }}</span>
                                    </label>

                                    <label class="flex items-center gap-3">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="d" class="text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span class="text-gray-700">D. {{ $question->option_d }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach

                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Submit Quiz
                        </button>
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

                if (!timerElement || !form) return;

                let remaining = parseInt(timerElement.dataset.seconds || '0', 10);

                function formatTime(seconds) {
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                }

                function updateTimer() {
                    timerElement.textContent = 'Time Left: ' + formatTime(remaining);

                    if (remaining <= 0) {
                        form.submit();
                        return;
                    }

                    remaining--;
                }

                updateTimer();
                setInterval(updateTimer, 1000);
            })();
        </script>
    @endif
</x-app-layout>
