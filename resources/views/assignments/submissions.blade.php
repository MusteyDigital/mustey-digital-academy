<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Assignment Submissions
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $course->title }} — {{ $lesson->title }}
                </p>
            </div>

            <a href="{{ route('lessons.show', [$course->id, $lesson->id]) }}" class="underline text-gray-600">
                ← Back to Lesson
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg border p-6">
                <h3 class="font-semibold text-gray-800 text-lg">{{ $assignment->title }}</h3>
                <p class="text-sm text-gray-600 mt-2 whitespace-pre-line">{{ $assignment->instructions }}</p>

                <div class="mt-3 text-sm text-gray-500 flex flex-wrap gap-4">
                    <span>Max Score: {{ $assignment->max_score }}</span>
                    @if($assignment->due_at)
                        <span>Due: {{ $assignment->due_at->format('M j, Y g:i A') }}</span>
                    @endif
                    <span>Total Submissions: {{ $submissions->count() }}</span>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg border p-6">
                @if($submissions->isEmpty())
                    <div class="rounded-lg border border-dashed p-6 bg-gray-50 text-gray-600">
                        No submissions yet.
                    </div>
                @else
                    <div class="space-y-5">
                        @foreach($submissions as $submission)
                            <div class="border rounded-xl p-5 space-y-4">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ optional($submission->user)->name ?? 'Student' }}</div>
                                        <div class="text-sm text-gray-500">
                                            Submitted: {{ $submission->submitted_at ? $submission->submitted_at->format('M j, Y g:i A') : '—' }}
                                        </div>
                                    </div>

                                    @if($submission->file_path)
                                        <a href="{{ route('assignments.download', [$course->id, $lesson->id, $submission->id]) }}"
                                           class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                                            Download Submission
                                        </a>
                                    @endif
                                </div>

                                @if($submission->student_note)
                                    <div class="text-sm text-gray-700 whitespace-pre-line">
                                        <strong>Student Note:</strong><br>
                                        {{ $submission->student_note }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('assignments.grade', [$course->id, $lesson->id, $submission->id]) }}" class="space-y-4">
                                    @csrf

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Score</label>
                                        <input
                                            type="number"
                                            name="score"
                                            min="0"
                                            max="{{ $assignment->max_score }}"
                                            value="{{ old('score', $submission->score) }}"
                                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Instructor Feedback</label>
                                        <textarea
                                            name="instructor_feedback"
                                            rows="4"
                                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                        >{{ old('instructor_feedback', $submission->instructor_feedback) }}</textarea>
                                    </div>

                                    <button type="submit"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
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
