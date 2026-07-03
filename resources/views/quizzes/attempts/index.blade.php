<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Quiz Attempt History
                </h2>
                <p class="text-sm text-slate-500 mt-1">{{ $quiz->title }} — {{ $course->title }}</p>
            </div>

            <a href="{{ route('quizzes.show', [$course->id, $quiz->id]) }}"
               class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Quiz
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                @if($attempts->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-200 p-6 bg-slate-50 text-slate-500 text-center text-sm">
                        You have not attempted this quiz yet.
                    </div>
                @else
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700 border-b border-slate-200">#</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700 border-b border-slate-200">Score</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700 border-b border-slate-200">Total</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700 border-b border-slate-200">Percentage</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700 border-b border-slate-200">Status</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700 border-b border-slate-200">Submitted</th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-700 border-b border-slate-200">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attempts as $index => $attempt)
                                    <tr class="border-b border-slate-100 last:border-b-0 hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 text-slate-800">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 text-slate-800">{{ $attempt->score ?? 0 }}</td>
                                        <td class="px-4 py-3 text-slate-800">{{ $attempt->total ?? 0 }}</td>
                                        <td class="px-4 py-3 text-slate-800">{{ number_format($attempt->percentage ?? 0, 2) }}%</td>
                                        <td class="px-4 py-3">
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
                                        <td class="px-4 py-3 text-slate-800">
                                            {{ optional($attempt->submitted_at)->format('d M Y, h:i A') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('quizzes.attempts.review', [$course->id, $quiz->id, $attempt->id]) }}"
                                               class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50 transition">
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