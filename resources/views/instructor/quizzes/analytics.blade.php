<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Quiz Analytics
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $quiz->title }} — {{ $course->title }}</p>
            </div>

            <a href="{{ route('quizzes.show', [$course->id, $quiz->id]) }}" class="underline text-gray-600">
                ← Back to Quiz
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg border p-5">
                    <div class="text-sm text-gray-500">Total Attempts</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $totalAttempts }}</div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg border p-5">
                    <div class="text-sm text-gray-500">Students Attempted</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $uniqueStudents }}</div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg border p-5">
                    <div class="text-sm text-gray-500">Average Score</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $averageScore }}</div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg border p-5">
                    <div class="text-sm text-gray-500">Average Percentage</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($averagePercentage, 2) }}%</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg border p-5">
                    <div class="text-sm text-gray-500">Passed</div>
                    <div class="text-2xl font-bold text-green-700 mt-1">{{ $passedCount }}</div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg border p-5">
                    <div class="text-sm text-gray-500">Failed</div>
                    <div class="text-2xl font-bold text-red-700 mt-1">{{ $failedCount }}</div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg border p-5">
                    <div class="text-sm text-gray-500">Pass Rate</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($passRate, 2) }}%</div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg border p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-lg text-gray-800">Recent Attempts</h3>
                    <span class="text-sm text-gray-500">Latest 20 submitted attempts</span>
                </div>

                @if($recentAttempts->isEmpty())
                    <div class="rounded-lg border border-dashed p-6 bg-gray-50 text-gray-600">
                        No submitted attempts yet.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Student</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Score</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Total</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Percentage</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttempts as $attempt)
                                    <tr class="border-b">
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ optional($attempt->user)->name ?? 'Unknown User' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ $attempt->score }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ $attempt->total }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ number_format($attempt->percentage ?? 0, 2) }}%</td>
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ $attempt->status }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800">
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
