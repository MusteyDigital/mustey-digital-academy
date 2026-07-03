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
                        MDA Logic Benchmark
                    </h2>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Train how you think.
                    </p>
                </div>
            </div>

            <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if(isset($adaptiveSuggestedDifficulty))
                <div class="rounded-2xl p-5 sm:p-6 space-y-4 text-white shadow-lg shadow-blue-500/20" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-white/15 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">Smart Recommendation</h3>
                                <p class="text-sm text-blue-100 mt-0.5">
                                    {{ $adaptiveReason ?? 'Keep practicing and improving.' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-xs uppercase tracking-wide text-blue-200">Suggested Level</div>
                            <div class="text-2xl font-bold">{{ ucfirst($adaptiveSuggestedDifficulty ?? 'easy') }}</div>
                        </div>
                    </div>

                    @if(($adaptiveSuggestedDifficulty ?? $difficulty) !== ($difficulty ?? 'easy'))
                        <div>
                            <a href="{{ route('drab.index', ['lesson' => $lesson->id, 'difficulty' => $adaptiveSuggestedDifficulty]) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl font-semibold bg-white text-blue-700 shadow-sm hover:bg-blue-50 transition">
                                Switch to {{ ucfirst($adaptiveSuggestedDifficulty) }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    @endif

                    @if(isset($adaptiveAverageAccuracy) && $adaptiveAverageAccuracy !== null)
                        <div class="text-sm text-blue-100">
                            Recent 5-attempt average: <strong class="text-white">{{ number_format((float) $adaptiveAverageAccuracy, 2) }}%</strong>
                        </div>
                    @endif

                    @if(!empty($adaptiveWeakRuleType))
                        <div class="text-sm text-blue-100">
                            Current focus area: <strong class="text-white">{{ str_replace('_', ' ', ucfirst($adaptiveWeakRuleType)) }}</strong>
                        </div>
                    @endif
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">How this benchmark works</h3>
                    <p class="text-sm text-slate-600 mt-2">
                        This benchmark trains your ability to think like a problem solver and data analyst.
                        Each task gives you a rule and input. Your job is to apply logic correctly.
                    </p>
                    <p class="text-sm text-slate-600 mt-2">
                        This mirrors real-world systems:
                        <br>• Excel formulas
                        <br>• Programming logic
                        <br>• AI decision systems
                        <br>• Data pipelines
                    </p>
                    <p class="text-sm text-slate-600 mt-2">
                        You are not memorizing — you are learning how to think.
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a9 9 0 100 18 9 9 0 000-18zm0 4v5l3 3"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Focus Session</h3>
                            <p class="text-sm text-slate-500">
                                Complete 5 adaptive tasks and get a smart session summary.
                            </p>
                        </div>
                    </div>
                </div>

                @if(!empty($sessionMode))
                    <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
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
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-slate-200 rounded-xl text-slate-700 hover:bg-white transition">
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
                                class="inline-flex items-center px-4 py-2.5 rounded-xl font-semibold text-white shadow-sm hover:opacity-90 transition"
                                style="background:#4f46e5;">
                            Start Adaptive Session
                        </button>
                    </form>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Speed Challenge</h3>
                            <p class="text-sm text-slate-500">
                                Complete 5 tasks in 90 seconds for a speed challenge.
                            </p>
                        </div>
                    </div>
                </div>

                @if(!empty($timedMode))
                    <div class="rounded-xl border border-orange-200 bg-orange-50 p-4">
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
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-slate-200 rounded-xl text-slate-700 hover:bg-white transition">
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
                                class="inline-flex items-center px-4 py-2.5 rounded-xl font-semibold text-white shadow-sm hover:opacity-90 transition"
                                style="background:#ea580c;">
                            Start Timed Mode
                        </button>
                    </form>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-5">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Select Difficulty
                    </label>
                    <select
                        name="difficulty"
                        @if(empty($sessionMode))
                            onchange="window.location.href='{{ route('drab.index', $lesson->id) }}?difficulty=' + this.value"
                        @endif
                        class="w-full border border-slate-200 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
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
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <strong>Medium Unlock:</strong>
                            {{ $easyCorrectCount ?? 0 }}/3 correct Easy attempts
                            @if(($difficultyUnlocks['medium'] ?? false))
                                <span class="ml-2 inline-flex items-center rounded-full bg-green-100 text-green-800 px-2 py-0.5 text-xs font-semibold">Unlocked</span>
                            @else
                                <span class="ml-2 inline-flex items-center rounded-full bg-yellow-100 text-yellow-800 px-2 py-0.5 text-xs font-semibold">In Progress</span>
                            @endif
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
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
                    <h3 class="font-bold text-slate-800 text-lg">Current DRAB Task</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Task source: {{ strtoupper($taskSource ?? 'local') }}
                    </p>
                    <p class="text-sm text-slate-500 mt-1">
                        Think carefully, apply the rule, and enter only the final number.
                    </p>
                </div>

                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 space-y-2">
                    <div class="text-sm text-slate-700">
                        <strong>Rule:</strong> {{ $task['rule'] }}
                    </div>
                    <div class="text-sm text-slate-700">
                        <strong>Input:</strong> {{ $task['input'] }}
                    </div>
                </div>

                <form id="drabForm" method="POST" action="{{ route('drab.submit', $lesson->id) }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="difficulty" value="{{ $difficulty ?? 'easy' }}">

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Your Answer</label>
                        <input
                            type="number"
                            name="student_answer"
                            value="{{ old('student_answer') }}"
                            class="w-full border border-slate-200 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                            placeholder="Enter final number"
                            required
                        >
                        @error('student_answer')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl font-semibold text-white shadow-sm hover:opacity-90 transition"
                            style="background:#7c3aed;border:1px solid #6d28d9;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Submit DRAB Answer
                    </button>
                </form>
            </div>

            @if(isset($recentAttempts) && $recentAttempts->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
                    <h3 class="font-bold text-slate-800 text-lg mb-4">Recent Attempts</h3>

                    <div class="hidden sm:block w-full overflow-x-auto rounded-xl border border-slate-100">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-slate-100">
                                <tr>
                                    <th class="text-left p-3 align-top break-words font-semibold text-slate-600">Date</th>
                                    <th class="text-left p-3 align-top break-words font-semibold text-slate-600">Accuracy</th>
                                    <th class="text-left p-3 align-top break-words font-semibold text-slate-600">Correct</th>
                                    <th class="text-left p-3 align-top break-words font-semibold text-slate-600">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttempts as $attempt)
                                    <tr class="border-b border-slate-100">
                                        <td class="p-3 align-top break-words text-slate-700">{{ $attempt->created_at?->format('M j, Y g:i A') }}</td>
                                        <td class="p-3 font-semibold text-slate-800">{{ number_format((float) $attempt->accuracy, 2) }}%</td>
                                        <td class="p-3 align-top break-words text-slate-700">{{ $attempt->correct_tasks }}</td>
                                        <td class="p-3 align-top break-words text-slate-700">{{ $attempt->total_tasks }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="sm:hidden space-y-3">
                        @foreach($recentAttempts as $attempt)
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 space-y-1">
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