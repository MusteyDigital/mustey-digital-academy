<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    🧠 Benchmark Result
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $lesson->title }}</p>
            </div>

            <a href="{{ route('lessons.show', [$lesson->course_id, $lesson->id]) }}" class="underline text-gray-600">
                ← Back to Lesson
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if(!empty($unlockMessage))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800 font-semibold">
                    {{ $unlockMessage }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6 space-y-4">
                <div>
                    <h3 class="font-semibold text-gray-800 text-lg">What happened in this attempt</h3>
                    <p class="text-sm text-gray-600 mt-2">
                        DRAB gave you a rule-based task and checked whether your answer followed the logic correctly.
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        <strong>Rule used:</strong> {{ $taskDescription ?? 'Apply the given rule to the input.' }}
                    </p>
                    @isset($studentAnswer)
                        <p class="text-sm text-gray-600 mt-2">
                            <strong>Your Answer:</strong> {{ $studentAnswer }} |
                            <strong>Expected Answer:</strong> {{ $expectedAnswer }}
                        </p>
                    @endisset
                </div>

                <div class="mt-4">
                    <a href="{{ route('drab.index', $lesson->id) }}"
                       class="inline-flex items-center px-4 py-2 rounded-lg font-semibold"
                       style="background:#2563eb;color:#ffffff;">
                        🔁 Try Another Task
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                <h3 class="font-semibold text-gray-800 text-lg mb-4">Why this answer is correct</h3>

                @if(isset($explanationLines) && count($explanationLines))
                    <div class="rounded-lg bg-gray-50 border p-4">
                        <ul class="space-y-2 text-sm text-gray-700">
                            @foreach($explanationLines as $line)
                                <li>{{ $line }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            @if(isset($sessionCompleted) && ($sessionCompleted ?? 0) > 0)
                <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-lg">🎯 Focus Session Report</h3>
                            <p class="text-sm text-gray-500">
                                Progress: <strong>{{ $sessionCompleted ?? 0 }}/{{ $sessionTarget ?? 5 }}</strong>
                            </p>
                            <p class="text-sm text-gray-500">
                                Correct: <strong>{{ $sessionCorrect ?? 0 }}</strong> |
                                Accuracy: <strong>{{ $sessionAccuracy ?? 0 }}%</strong>
                            </p>
                            <p class="text-sm text-gray-500">
                                XP Earned: <strong>{{ $sessionXp ?? 0 }}</strong>
                            </p>
                            @if(!empty($adaptiveWeakRuleType))
                                <p class="text-sm text-gray-500">
                                    Focus Area: <strong>{{ str_replace('_', ' ', ucfirst($adaptiveWeakRuleType)) }}</strong>
                                </p>
                            @endif

                            @if(!empty($sessionFinished))
                                <div class="mt-4 rounded-lg border border-indigo-200 bg-indigo-50 p-4 space-y-2">
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
                               class="inline-flex items-center px-4 py-2 rounded-lg font-semibold"
                               style="background:#4f46e5;color:#ffffff;">
                                Next Adaptive Task
                            </a>
                        @else
                            <a href="{{ route('drab.index', ['lesson' => $lesson->id, 'difficulty' => ($sessionRecommendedDifficulty ?? $difficulty ?? 'easy')]) }}"
                               class="inline-flex items-center px-4 py-2 rounded-lg font-semibold"
                               style="background:#4f46e5;color:#ffffff;">
                                Start Next Challenge
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            @if(!empty($timedMode))
                <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-lg">⏱ Timed Mode Summary</h3>
                            <p class="text-sm text-gray-500">
                                Progress: <strong>{{ $timedCompleted ?? 0 }}/{{ $timedTarget ?? 5 }}</strong>
                            </p>
                            <p class="text-sm text-gray-500">
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
                               class="inline-flex items-center px-4 py-2 rounded-lg font-semibold"
                               style="background:#ea580c;color:#ffffff;">
                                Next Timed Task
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">Benchmark Summary</h3>
                        <p class="text-sm text-gray-500">
                            Difficulty: <strong>{{ ucfirst($difficulty ?? 'easy') }}</strong>
                        </p>
                        <p class="text-sm text-gray-500">
                            Task source: <strong>{{ strtoupper($taskSource ?? 'local') }}</strong>
                        </p>
                        <p class="text-sm text-gray-500">
                            DRAB evaluation for adaptive rule application.
                        </p>
                    </div>

                    <div class="text-right">
                        <div class="text-sm text-gray-500">Accuracy</div>
                        <div class="text-3xl font-bold text-gray-900">{{ number_format($accuracy, 2) }}%</div>
                    </div>
                </div>

                <div class="mt-4 text-sm text-gray-600">
                    Correct: <strong>{{ $correctTasks }}</strong> / {{ $totalTasks }}
                </div>
            </div>

            @if(isset($recentAttempts) && $recentAttempts->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-800 text-lg mb-4">Recent Attempts</h3>

                    <div class="space-y-3">
                        @foreach($recentAttempts as $attempt)
                            <div class="rounded-lg border bg-gray-50 p-4 space-y-2">
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

            <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                <h3 class="font-semibold text-gray-800 text-lg mb-4">Task Results</h3>

                <div class="space-y-3">
                    @foreach($results as $r)
                        <div class="rounded-lg border bg-gray-50 p-4 space-y-2">
                            <div class="text-sm"><strong>Input:</strong> {{ is_array($r['input']) ? json_encode($r['input']) : $r['input'] }}</div>
                            <div class="text-sm"><strong>Rule:</strong> {{ is_array($r['rule']) ? json_encode($r['rule']) : $r['rule'] }}</div>
                            <div class="text-sm"><strong>Expected:</strong> {{ is_array($r['expected']) ? json_encode($r['expected']) : $r['expected'] }}</div>
                            <div class="text-sm"><strong>Output:</strong> {{ is_array($r['output']) ? json_encode($r['output']) : $r['output'] }}</div>
                            <div class="text-sm">
                                <strong>Status:</strong>
                                @if($r['correct'])
                                    <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 px-3 py-1 text-xs font-semibold">✔ Correct</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 text-red-800 px-3 py-1 text-xs font-semibold">✘ Wrong</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
