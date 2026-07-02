<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Assignment Submissions
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $course->title }} — {{ $lesson->title }}
                </p>
            </div>

            <a href="{{ route('lessons.show', [$course->id, $lesson->id]) }}"
               class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Lesson
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-semibold text-slate-800 text-lg">{{ $assignment->title }}</h3>
                <p class="text-sm text-slate-600 mt-2 whitespace-pre-line">{{ $assignment->instructions }}</p>

                <div class="mt-3 text-sm text-slate-500 flex flex-wrap gap-4">
                    <span>Max Score: {{ $assignment->max_score }}</span>
                    @if($assignment->due_at)
                        <span>Due: {{ $assignment->due_at->format('M j, Y g:i A') }}</span>
                    @endif
                    <span>Total Submissions: {{ $submissions->count() }}</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                @if($submissions->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-200 p-8 bg-slate-50 text-slate-500 text-center text-sm">
                        No submissions yet.
                    </div>
                @else
                    <div class="space-y-5">
                        @foreach($submissions as $submission)
                            <div class="rounded-xl border border-slate-200 p-5 space-y-4 hover:border-blue-200 transition">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ optional($submission->user)->name ?? 'Student' }}</div>
                                        <div class="text-sm text-slate-500">
                                            Submitted: {{ $submission->submitted_at ? $submission->submitted_at->format('M j, Y g:i A') : '—' }}
                                        </div>
                                    </div>

                                    @if($submission->file_path)
                                        <a href="{{ route('assignments.download', [$course->id, $lesson->id, $submission->id]) }}"
                                           class="inline-flex items-center rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                            Download Submission
                                        </a>
                                    @endif
                                </div>

                                @if($submission->student_note)
                                    <div class="text-sm text-slate-700 whitespace-pre-line rounded-xl bg-slate-50 border border-slate-200 p-3">
                                        <strong>Student Note:</strong><br>
                                        {{ $submission->student_note }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('assignments.grade', [$course->id, $lesson->id, $submission->id]) }}" class="space-y-4">
                                    @csrf

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Score</label>
                                        <input
                                            type="number"
                                            name="score"
                                            min="0"
                                            max="{{ $assignment->max_score }}"
                                            value="{{ old('score', $submission->score) }}"
                                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Instructor Feedback</label>
                                        <textarea
                                            name="instructor_feedback"
                                            rows="4"
                                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                                        >{{ old('instructor_feedback', $submission->instructor_feedback) }}</textarea>
                                    </div>

                                    <button type="submit"
                                            class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition">
                                        Save Grade
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>