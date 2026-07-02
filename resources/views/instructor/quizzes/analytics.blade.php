<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Quiz Analytics
                </h2>
                <p class="text-sm text-slate-500 mt-1">{{ $quiz->title }} — {{ $course->title }}</p>
            </div>

            <a href="{{ route('quizzes.show', [$course->id, $quiz->id]) }}"
               class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Back to Quiz
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <div class="text-sm text-slate-500">Total Attempts</div>
                    <div class="text-2xl font-bold text-slate-900 mt-0.5">{{ $totalAttempts }}</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>
                    </div>
                    <div class="text-sm text-slate-500">Students Attempted</div>
                    <div class="text-2xl font-bold text-slate-900 mt-0.5">{{ $uniqueStudents }}</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div class="text-sm text-slate-500">Average Score</div>
                    <div class="text-2xl font-bold text-slate-900 mt-0.5">{{ $averageScore }}</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <div class="text-sm text-slate-500">Average Percentage</div>
                    <div class="text-2xl font-bold text-slate-900 mt-0.5">{{ number_format($averagePercentage, 2) }}%</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                    <div class="w-9 h-9 rounded-xl bg-green-50 text-green-600 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="text-sm text-slate-500">Passed</div>
                    <div class="text-2xl font-bold text-green-700 mt-0.5">{{ $passedCount }}</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                    <div class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div class="text-sm text-slate-500">Failed</div>
                    <div class="text-2xl font-bold text-red-700 mt-0.5">{{ $failedCount }}</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div class="text-sm text-slate-500">Pass Rate</div>
                    <div class="text-2xl font-bold text-slate-900 mt-0.5">{{ number_format($passRate, 2) }}%</div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <h3 class="font-semibold text-lg text-slate-800">Recent Attempts</h3>
                    <span class="text-sm text-slate-500">Latest 20 submitted attempts</span>
                </div>

                @if($recentAttempts->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-200 p-8 bg-slate-50 text-slate-500 text-center text-sm">
                        No submitted attempts yet.
                    </div>
                @else
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700 border-b border-slate-200">Student</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700 border-b border-slate-200">Score</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700 border-b border-slate-200">Total</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700 border-b border-slate-200">Percentage</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700 border-b border-slate-200">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700 border-b border-slate-200">Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttempts as $attempt)
                                    <tr class="border-b border-slate-100 last:border-b-0 hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 text-sm text-slate-800">{{ optional($attempt->user)->name ?? 'Unknown User' }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-800">{{ $attempt->score }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-800">{{ $attempt->total }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-800">{{ number_format($attempt->percentage ?? 0, 2) }}%</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium
                                                {{ strtolower($attempt->status) === 'passed' ? 'bg-green-50 text-green-700' : (strtolower($attempt->status) === 'failed' ? 'bg-red-50 text-red-700' : 'bg-slate-100 text-slate-700') }}">
                                                {{ $attempt->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-500">
                                            {{ optional($attempt->submitted_at)->format('d M Y, h:i A') ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>