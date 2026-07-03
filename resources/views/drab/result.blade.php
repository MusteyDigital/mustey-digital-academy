<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30 shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">
                        Benchmark Result
                    </h2>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $lesson->title }}</p>
                </div>
            </div>

            <a href="{{ route('lessons.show', [$lesson->course_id, $lesson->id]) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Lesson
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if(!empty($unlockMessage))
                <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800 font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a4 4 0 100-8 4 4 0 000 8zm0 0v6m-4-2.5L6 21m10-2.5l2 2.5"/></svg>
                    {{ $unlockMessage }}
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">What happened in this attempt</h3>
                    <p class="text-sm text-slate-600 mt-2">
                        DRAB gave you a rule-based task and checked whether your answer followed the logic correctly.
                    </p>
                    <p class="text-sm text-slate-600 mt-2">
                        <strong>Rule used:</strong> {{ $taskDescription ?? 'Apply the given rule to the input.' }}
                    </p>
                    @isset($studentAnswer)
                        <p class="text-sm text-slate-600 mt-2">
                            <strong>Your Answer:</strong> {{ $studentAnswer }} |
                            <strong>Expected Answer:</strong> {{ $expectedAnswer }}
                        </p>
                    @endisset
                </div>

                <div class="mt-4">
                    <a href="{{ route('drab.index', $lesson->id) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-semibold text-white shadow-sm hover:opacity-90 transition"
                       style="background:#2563eb;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Try Another Task
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
                <h3 class="font-bold text-slate-800 text-lg mb-4">Why this answer is correct</h3>

                @if(isset($explanationLines) && count($explanationLines))
                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                        <ul class="space-y-2 text-sm text-slate-700">
                            @foreach($explanationLines as $line)
                                <li>{{ $line }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            @if(isset($sessionCompleted) && ($sessionCompleted ?? 0) > 0)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/><circle cx="12" cy="12" r="9"/></svg>
                                Focus Session Report
                            </h3>
                            <p class="text-sm text-slate-500 mt-1">
                                Progress: <strong>{{ $sessionCompleted ?? 0 }}/{{ $sessionTarget ?? 5 }}</strong>
                            </p>
                            <p class="text-sm text-slate-500">
                                Correct: <strong>{{ $sessionCorrect ?? 0 }}</strong> |
                                Accuracy: <strong>{{ $sessionAccuracy ?? 0 }}%</strong>
                            </p>
                            <p class="text-sm text-slate-500">
                                XP Earned: <strong>{{ $sessionXp ?? 0 }}</strong>
                            </p>
                            @if(!empty($adaptiveWeakRuleType))
                                <p class="text-sm text-slate-500">
                                    Focus Area: <strong>{{ str_replace('_', ' ', ucfirst($adaptiveWeakRuleType)) }}</strong>
                                </p>
                            @endif

                            @if(!empty($sessionFinished))
                                <div class="mt-4 rounded-xl border border-indigo-200 bg-indigo-50 p-4 space-y-2">
                                    <div class="text-sm font-semibold text-indigo-900">
                                        Session completed.
                                    </div>
                                    <div class="text-sm text-indigo-800">
                                        {{ $sessionReportMessage ?? 'Keep improving.' }}
                                    </div>
                                    <div class="text-sm text-indigo-800">
                                        Recommended next level:
                                        <strong>{{ ucfirst($sessionRecommendedDifficulty ?? $difficulty ?? 'easy') }}</strong>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if(($sessionCompleted ?? 0) < ($sessionTarget ?? 5))
                            <a href="{{ route('drab.index', ['lesson' => $lesson->id, 'difficulty' => $difficulty]) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-semibold text-white shadow-sm hover:opacity-90 transition"
                               style="background:#4f46e5;">
                                Next Adaptive Task
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        @else
                            <a href="{{ route('drab.index', ['lesson' => $lesson->id, 'difficulty' => ($sessionRecommendedDifficulty ?? $difficulty ?? 'easy')]) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-semibold text-white shadow-sm hover:opacity-90 transition"
                               style="background:#4f46e5;">
                                Start Next Challenge
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            @if(!empty($timedMode))
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Timed Mode Summary
                            </h3>
                            <p class="text-sm text-slate-500 mt-1">
                                Progress: <strong>{{ $timedCompleted ?? 0 }}/{{ $timedTarget ?? 5 }}</strong>
                            </p>
                            <p class="text-sm text-slate-500">
                                Time Used: <strong>{{ $timedElapsed ?? 0 }}s</strong> |
                                Time Left: <strong>{{ $timedRemaining ?? 0 }}s</strong>
                            </p>
                            @if(!empty($timedFinished))
                                <p class="text-sm font-semibold text-orange-700 mt-2">
                                    Timed session completed.
                                </p>
                            @endif
                        </div>

                        @if(empty($timedFinished))
                            <a href="{{ route('drab.index', ['lesson' => $lesson->id, 'difficulty' => $difficulty]) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-semibold text-white shadow-sm hover:opacity-90 transition"
                               style="background:#ea580c;">
                                Next Timed Task
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Benchmark Summary</h3>
                        <p class="text-sm text-slate-500">
                            Difficulty: <strong>{{ ucfirst($difficulty ?? 'easy') }}</strong>
                        </p>
                        <p class="text-sm text-slate-500">
                            Task source: <strong>{{ strtoupper($taskSource ?? 'local') }}</strong>
                        </p>
                        <p class="text-sm text-slate-500">
                            DRAB evaluation for adaptive rule application.
                        </p>
                    </div>

                    <div class="text-right">
                        <div class="text-sm text-slate-500">Accuracy</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($accuracy, 2) }}%</div>
                    </div>
                </div>

                <div class="mt-4 text-sm text-slate-600">
                    Correct: <strong>{{ $correctTasks }}</strong> / {{ $totalTasks }}
                </div>
            </div>

            @if(isset($recentAttempts) && $recentAttempts->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
                    <h3 class="font-bold text-slate-800 text-lg mb-4">Recent Attempts</h3>

                    <div class="space-y-3">
                        @foreach($recentAttempts as $attempt)
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-2">
                                <div class="text-sm"><strong>Date:</strong> {{ $attempt->created_at?->format('M j, Y g:i A') }}</div>
                                <div class="text-sm">
                                    <strong>Difficulty:</strong>
                                    @php $d = $attempt->difficulty ?? 'easy'; @endphp
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full
                                        @if($d === 'easy') bg-green-100 text-green-800
                                        @elseif($d === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($d) }}
                                    </span>
                                </div>
                                <div class="text-sm"><strong>Accuracy:</strong> {{ number_format((float) $attempt->accuracy, 2) }}%</div>
                                <div class="text-sm"><strong>Correct:</strong> {{ $attempt->correct_tasks }}</div>
                                <div class="text-sm"><strong>Total:</strong> {{ $attempt->total_tasks }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
                <h3 class="font-bold text-slate-800 text-lg mb-4">Task Results</h3>

                <div class="space-y-3">
                    @foreach($results as $r)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-2">
                            <div class="text-sm"><strong>Input:</strong> {{ is_array($r['input']) ? json_encode($r['input']) : $r['input'] }}</div>
                            <div class="text-sm"><strong>Rule:</strong> {{ is_array($r['rule']) ? json_encode($r['rule']) : $r['rule'] }}</div>
                            <div class="text-sm"><strong>Expected:</strong> {{ is_array($r['expected']) ? json_encode($r['expected']) : $r['expected'] }}</div>
                            <div class="text-sm"><strong>Output:</strong> {{ is_array($r['output']) ? json_encode($r['output']) : $r['output'] }}</div>
                            <div class="text-sm flex items-center gap-2">
                                <strong>Status:</strong>
                                @if($r['correct'])
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-100 text-green-800 px-3 py-1 text-xs font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Correct
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-100 text-red-800 px-3 py-1 text-xs font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Wrong
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>