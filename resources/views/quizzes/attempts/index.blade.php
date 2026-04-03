<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Quiz Attempt History
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $quiz->title }} — {{ $course->title }}</p>
            </div>

            <a href="{{ route('quizzes.show', [$course->id, $quiz->id]) }}" class="underline text-gray-600">
                ← Back to Quiz
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg border p-6">
                @if($attempts->isEmpty())
                    <div class="rounded-lg border border-dashed p-6 bg-gray-50 text-gray-600">
                        You have not attempted this quiz yet.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">#</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Score</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Total</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Percentage</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Submitted</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attempts as $index => $attempt)
                                    <tr class="border-b">
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ $attempt->score ?? 0 }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ $attempt->total ?? 0 }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ number_format($attempt->percentage ?? 0, 2) }}%</td>
                                        <td class="px-4 py-3 text-sm text-gray-800">
                                            @if($attempt->status === 'submitted')
                                                <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 border border-green-200 px-3 py-1 text-xs font-semibold">
                                                    Submitted
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200 px-3 py-1 text-xs font-semibold">
                                                    {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-800">
                                            {{ optional($attempt->submitted_at)->format('d M Y, h:i A') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-800">
                                            <a href="{{ route('quizzes.attempts.review', [$course->id, $quiz->id, $attempt->id]) }}"
                                               class="inline-flex items-center px-3 py-1.5 text-sm border rounded-lg text-gray-700 hover:bg-gray-50">
                                                Review
                                            </a>
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
