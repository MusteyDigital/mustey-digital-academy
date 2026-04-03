<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    🧠 MDA Logic Benchmark
                </h2>
                <p class="text-sm text-gray-500 mt-1">
    Train how you think.
</p>
            </div>

            <a href="{{ url()->previous() }}" class="underline text-gray-600">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if(isset($adaptiveSuggestedDifficulty))
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 sm:p-6 space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h3 class="font-semibold text-blue-900 text-lg">🧠 Smart Recommendation</h3>
                            <p class="text-sm text-blue-800">
                                {{ $adaptiveReason ?? 'Keep practicing and improving.' }}
                            </p>
                        </div>

                        <div class="text-right">
                            <div class="text-xs uppercase tracking-wide text-blue-700">Suggested Level</div>
                            <div class="text-2xl font-bold text-blue-900">{{ ucfirst($adaptiveSuggestedDifficulty ?? 'easy') }}</div>
                        </div>
                    </div>

                    @if(($adaptiveSuggestedDifficulty ?? $difficulty) !== ($difficulty ?? 'easy'))
                        <div>
                            <a href="{{ route('drab.index', ['lesson' => $lesson->id, 'difficulty' => $adaptiveSuggestedDifficulty]) }}"
                               class="inline-flex items-center px-4 py-2 rounded-lg font-semibold"
                               style="background:#2563eb;color:#ffffff;">
                                Switch to {{ ucfirst($adaptiveSuggestedDifficulty) }}
                            </a>
                        </div>
                    @endif

                    @if(isset($adaptiveAverageAccuracy) && $adaptiveAverageAccuracy !== null)
                        <div class="text-sm text-blue-800">
                            Recent 5-attempt average: <strong>{{ number_format((float) $adaptiveAverageAccuracy, 2) }}%</strong>
                        </div>
                    @endif

                    @if(!empty($adaptiveWeakRuleType))
                        <div class="text-sm text-blue-800">
                            Current focus area: <strong>{{ str_replace('_', ' ', ucfirst($adaptiveWeakRuleType)) }}</strong>
                        </div>
                    @endif
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6 space-y-4">
                <div>
                    <h3 class="font-semibold text-gray-800 text-lg">How this benchmark works</h3>
                    <p class="text-sm text-gray-600 mt-2">
                        This benchmark trains your ability to think like a problem solver and data analyst.
                        Each task gives you a rule and input. Your job is to apply logic correctly.
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        This mirrors real-world systems:
                        <br>• Excel formulas
                        <br>• Programming logic
                        <br>• AI decision systems
                        <br>• Data pipelines
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        You are not memorizing — you are learning how to think.
                    </p>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6 space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">🎯 Focus Session</h3>
                        <p class="text-sm text-gray-600">
                            Complete 5 adaptive tasks and get a smart session summary.
                        </p>
                    </div>
                </div>

                @if(!empty($sessionMode))
                    <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-4">
                        <div class="mb-3 text-sm text-indigo-800">
                            Session difficulty is locked to <strong>{{ ucfirst($sessionLockedDifficulty ?? $difficulty ?? 'easy') }}</strong> until this 5-task session ends.
                        </div>
                        <div class="grid grid-cols-3 gap-3 text-sm">
                            <div>
                                <div class="text-xs uppercase tracking-wide text-indigo-700">Mode</div>
                                <div class="font-semibold text-indigo-900">Adaptive</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-indigo-700">Progress</div>
                                <div class="font-semibold text-indigo-900">{{ $sessionCompleted ?? 0 }}/{{ $sessionTarget ?? 5 }}</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-indigo-700">XP</div>
                                <div class="font-semibold text-indigo-900">{{ $sessionXp ?? 0 }}</div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <form method="POST" action="{{ route('drab.session.reset', $lesson->id) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-white">
                                    Stop Adaptive Session
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('drab.session.start', $lesson->id) }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="difficulty" value="{{ $difficulty ?? 'easy' }}">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 rounded-lg font-semibold"
                                style="background:#4f46e5;color:#ffffff;">
                            Start Adaptive Session
                        </button>
                    </form>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6 space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">⏱ Speed Challenge</h3>
                        <p class="text-sm text-gray-600">
                            Complete 5 tasks in 90 seconds for a speed challenge.
                        </p>
                    </div>
                </div>

                @if(!empty($timedMode))
                    <div class="rounded-lg border border-orange-200 bg-orange-50 p-4">
                        <div class="grid grid-cols-3 gap-3 text-sm">
                            <div>
                                <div class="text-xs uppercase tracking-wide text-orange-700">Mode</div>
                                <div class="font-semibold text-orange-900">Timed</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-orange-700">Progress</div>
                                <div class="font-semibold text-orange-900">{{ $timedCompleted ?? 0 }}/{{ $timedTarget ?? 5 }}</div>
                            </div>
                            <div>
                                <div class="text-xs uppercase tracking-wide text-orange-700">Time Left</div>
                                <div class="font-semibold text-orange-900" id="timed-remaining" data-seconds="{{ $timedRemaining ?? 0 }}">{{ $timedRemaining ?? 0 }}s</div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <form method="POST" action="{{ route('drab.timed.reset', $lesson->id) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-white">
                                    Stop Timed Mode
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('drab.timed.start', $lesson->id) }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="difficulty" value="{{ $difficulty ?? 'easy' }}">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 rounded-lg font-semibold"
                                style="background:#ea580c;color:#ffffff;">
                            Start Timed Mode
                        </button>
                    </form>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6 space-y-5">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select Difficulty
                    </label>
                    <select
                        name="difficulty"
                        @if(empty($sessionMode))
                            onchange="window.location.href='{{ route('drab.index', $lesson->id) }}?difficulty=' + this.value"
                        @endif
                        class="w-full border rounded p-2"
                        {{ !empty($sessionMode) ? 'disabled' : '' }}>
                        <option value="easy" {{ ($difficulty ?? 'easy') === 'easy' ? 'selected' : '' }}>
                            Foundation Thinking — Unlocked
                        </option>
                        <option value="medium"
                            {{ ($difficulty ?? 'easy') === 'medium' ? 'selected' : '' }}
                            {{ !($difficultyUnlocks['medium'] ?? false) ? 'disabled' : '' }}>
                            Applied Reasoning — {{ ($difficultyUnlocks['medium'] ?? false) ? 'Unlocked' : 'Locked' }}
                        </option>
                        <option value="hard"
                            {{ ($difficulty ?? 'easy') === 'hard' ? 'selected' : '' }}
                            {{ !($difficultyUnlocks['hard'] ?? false) ? 'disabled' : '' }}>
                            Advanced Logic — {{ ($difficultyUnlocks['hard'] ?? false) ? 'Unlocked' : 'Locked' }}
                        </option>
                    </select>

                    <div class="mt-3 space-y-2 text-sm">
                        <div class="rounded-lg border bg-gray-50 p-3">
                            <strong>Medium Unlock:</strong>
                            {{ $easyCorrectCount ?? 0 }}/3 correct Easy attempts
                            @if(($difficultyUnlocks['medium'] ?? false))
                                <span class="ml-2 inline-flex items-center rounded-full bg-green-100 text-green-800 px-2 py-0.5 text-xs font-semibold">Unlocked</span>
                            @else
                                <span class="ml-2 inline-flex items-center rounded-full bg-yellow-100 text-yellow-800 px-2 py-0.5 text-xs font-semibold">In Progress</span>
                            @endif
                        </div>

                        <div class="rounded-lg border bg-gray-50 p-3">
                            <strong>Hard Unlock:</strong>
                            {{ $mediumCorrectCount ?? 0 }}/3 correct Medium attempts
                            @if(($difficultyUnlocks['hard'] ?? false))
                                <span class="ml-2 inline-flex items-center rounded-full bg-green-100 text-green-800 px-2 py-0.5 text-xs font-semibold">Unlocked</span>
                            @else
                                <span class="ml-2 inline-flex items-center rounded-full bg-yellow-100 text-yellow-800 px-2 py-0.5 text-xs font-semibold">In Progress</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-800 text-lg">Current DRAB Task</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Task source: {{ strtoupper($taskSource ?? 'local') }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        Think carefully, apply the rule, and enter only the final number.
                    </p>
                </div>

                <div class="rounded-lg bg-gray-50 border p-4 space-y-2">
                    <div class="text-sm text-gray-700">
                        <strong>Rule:</strong> {{ $task['rule'] }}
                    </div>
                    <div class="text-sm text-gray-700">
                        <strong>Input:</strong> {{ $task['input'] }}
                    </div>
                </div>

                <form id="drabForm" method="POST" action="{{ route('drab.submit', $lesson->id) }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="difficulty" value="{{ $difficulty ?? 'easy' }}">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Your Answer</label>
                        <input
                            type="number"
                            name="student_answer"
                            value="{{ old('student_answer') }}"
                            class="w-full border rounded p-2"
                            placeholder="Enter final number"
                            required
                        >
                        @error('student_answer')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 rounded-lg font-semibold"
                            style="background:#7c3aed;color:#ffffff;border:1px solid #6d28d9;">
                        Submit DRAB Answer
                    </button>
                </form>
            </div>

            @if(isset($recentAttempts) && $recentAttempts->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-800 text-lg mb-4">Recent Attempts</h3>

                    <div class="hidden sm:block w-full overflow-x-auto rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Date</th>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Accuracy</th>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Correct</th>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttempts as $attempt)
                                    <tr class="border-b">
                                        <td class="p-2 sm:p-3 align-top break-words">{{ $attempt->created_at?->format('M j, Y g:i A') }}</td>
                                        <td class="p-3 font-semibold">{{ number_format((float) $attempt->accuracy, 2) }}%</td>
                                        <td class="p-2 sm:p-3 align-top break-words">{{ $attempt->correct_tasks }}</td>
                                        <td class="p-2 sm:p-3 align-top break-words">{{ $attempt->total_tasks }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="sm:hidden space-y-3">
                        @foreach($recentAttempts as $attempt)
                            <div class="rounded-lg border bg-gray-50 p-3 space-y-1">
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

        </div>
    </div>

    @if(!empty($timedMode))
        <script>
            (function () {
                const el = document.getElementById('timed-remaining');
                if (!el) return;
                let seconds = parseInt(el.dataset.seconds || '0', 10);
                const tick = () => {
                    if (seconds <= 0) {
                        el.textContent = '0s';
                        window.location.reload();
                        return;
                    }
                    el.textContent = seconds + 's';
                    seconds -= 1;
                    setTimeout(tick, 1000);
                };
                tick();
            })();
        </script>
    @endif
</x-app-layout>
