<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    🧠 Practice Lab: Logic & Data Transformation
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Train your reasoning skills by applying rules to data. This simulates how real data analysis works.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 overflow-x-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6 space-y-4">
                <div>
                    <h3 class="font-semibold text-gray-800 text-lg">DRAB Overview</h3>
                    <p class="text-sm text-gray-500">
                        Dynamic Rule Adaptation Benchmark for testing cognitive flexibility and rule-based reasoning.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="rounded-xl border bg-gray-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-gray-500">Total Attempts</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">{{ $drabTotalAttempts ?? 0 }}</div>
                    </div>

                    <div class="rounded-xl border bg-gray-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-gray-500">Average Accuracy</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">{{ number_format((float) ($drabAverageAccuracy ?? 0), 2) }}%</div>
                    </div>

                    <div class="rounded-xl border bg-gray-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-gray-500">Best Accuracy</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">{{ number_format((float) ($drabBestAccuracy ?? 0), 2) }}%</div>
                    </div>
                </div>

                <div class="rounded-lg border border-dashed p-5 bg-gray-50 text-gray-700">
                    <p class="font-medium">Mustey Digital Academy doesn’t just teach tools — we train how you think.</p>
                </div>
            </div>

            @if(isset($drabByDifficulty))
                <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-800 text-lg mb-4">Performance by Difficulty</h3>

                    <div class="w-full overflow-x-auto rounded-lg">
                        <table class="min-w-full text-sm table-auto">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Difficulty</th>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Attempts</th>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Average Accuracy</th>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Best Accuracy</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard'] as $key => $label)
                                    <tr class="border-b">
                                        <td class="p-3 font-semibold">{{ $label }}</td>
                                        <td class="p-2 sm:p-3 align-top break-words">{{ $drabByDifficulty[$key]['attempts'] ?? 0 }}</td>
                                        <td class="p-2 sm:p-3 align-top break-words">{{ number_format((float) ($drabByDifficulty[$key]['average_accuracy'] ?? 0), 2) }}%</td>
                                        <td class="p-2 sm:p-3 align-top break-words">{{ number_format((float) ($drabByDifficulty[$key]['best_accuracy'] ?? 0), 2) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">Run DRAB from Lessons</h3>
                        <p class="text-sm text-gray-500">
                            Open any lesson with DRAB enabled and start an interactive reasoning practice.
                        </p>
                    </div>
                </div>

                @if(($drabLessons ?? collect())->isEmpty())
                    <div class="rounded-lg border border-dashed p-5 bg-gray-50 text-gray-600">
                        No DRAB-enabled lessons available yet.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($drabLessons as $lesson)
                            <div class="border rounded-xl p-4">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $lesson->title }}</div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            {{ $lesson->course->title ?? 'Course' }}
                                        </div>
                                    </div>

                                    <a href="{{ route('lessons.show', [$lesson->course_id, $lesson->id]) }}"
                                       class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                                        Open Lesson
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
