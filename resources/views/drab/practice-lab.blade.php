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
                        Practice Lab: Logic & Data Transformation
                    </h2>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Train your reasoning skills by applying rules to data. This simulates how real data analysis works.
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 overflow-x-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">DRAB Overview</h3>
                    <p class="text-sm text-slate-500">
                        Dynamic Rule Adaptation Benchmark for testing cognitive flexibility and rule-based reasoning.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-slate-500">Total Attempts</div>
                        <div class="text-2xl font-bold text-slate-800 mt-2">{{ $drabTotalAttempts ?? 0 }}</div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-slate-500">Average Accuracy</div>
                        <div class="text-2xl font-bold text-slate-800 mt-2">{{ number_format((float) ($drabAverageAccuracy ?? 0), 2) }}%</div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-slate-500">Best Accuracy</div>
                        <div class="text-2xl font-bold text-slate-800 mt-2">{{ number_format((float) ($drabBestAccuracy ?? 0), 2) }}%</div>
                    </div>
                </div>

                <div class="rounded-xl border border-dashed border-slate-300 p-5 bg-slate-50 text-slate-700">
                    <p class="font-medium">Mustey Digital Academy doesn't just teach tools — we train how you think.</p>
                </div>
            </div>

            @if(isset($drabByDifficulty))
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
                    <h3 class="font-bold text-slate-800 text-lg mb-4">Performance by Difficulty</h3>

                    <div class="w-full overflow-x-auto rounded-xl border border-slate-100">
                        <table class="min-w-full text-sm table-auto">
                            <thead class="bg-slate-50 border-b border-slate-100">
                                <tr>
                                    <th class="text-left p-3 align-top break-words font-semibold text-slate-600">Difficulty</th>
                                    <th class="text-left p-3 align-top break-words font-semibold text-slate-600">Attempts</th>
                                    <th class="text-left p-3 align-top break-words font-semibold text-slate-600">Average Accuracy</th>
                                    <th class="text-left p-3 align-top break-words font-semibold text-slate-600">Best Accuracy</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard'] as $key => $label)
                                    <tr class="border-b border-slate-100">
                                        <td class="p-3 font-semibold text-slate-800">
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full
                                                @if($key === 'easy') bg-green-100 text-green-800
                                                @elseif($key === 'medium') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ $label }}
                                            </span>
                                        </td>
                                        <td class="p-3 align-top break-words text-slate-700">{{ $drabByDifficulty[$key]['attempts'] ?? 0 }}</td>
                                        <td class="p-3 align-top break-words text-slate-700">{{ number_format((float) ($drabByDifficulty[$key]['average_accuracy'] ?? 0), 2) }}%</td>
                                        <td class="p-3 align-top break-words text-slate-700">{{ number_format((float) ($drabByDifficulty[$key]['best_accuracy'] ?? 0), 2) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
                <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Run DRAB from Lessons</h3>
                            <p class="text-sm text-slate-500">
                                Open any lesson with DRAB enabled and start an interactive reasoning practice.
                            </p>
                        </div>
                    </div>
                </div>

                @if(($drabLessons ?? collect())->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-300 p-5 bg-slate-50 text-slate-600">
                        No DRAB-enabled lessons available yet.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($drabLessons as $lesson)
                            <div class="border border-slate-200 rounded-xl p-4 hover:border-slate-300 transition">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ $lesson->title }}</div>
                                        <div class="text-sm text-slate-500 mt-1">
                                            {{ $lesson->course->title ?? 'Course' }}
                                        </div>
                                    </div>

                                    <a href="{{ route('lessons.show', [$lesson->course_id, $lesson->id]) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 border border-slate-200 rounded-xl text-slate-700 hover:bg-slate-50 transition font-medium text-sm">
                                        Open Lesson
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>