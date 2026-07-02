<x-app-layout>

    @php
        $resumeSeconds = 0;

        if (auth()->check() && auth()->user()->role === 'student') {
            $videoProgress = \App\Models\LessonVideoProgress::where('lesson_id', $lesson->id)
                ->where('user_id', auth()->id())
                ->first();

            $resumeSeconds = (int) ($videoProgress?->watched_seconds ?? 0);
        }

        $iframeSrc = $lesson->video_url ?? '';

        if (!empty($iframeSrc)) {
            $separator = str_contains($iframeSrc, '?') ? '&' : '?';
            $iframeSrc .= $separator . 'enablejsapi=1&rel=0&origin=' . urlencode(url('/'));

            if (str_contains($iframeSrc, 'youtube.com/embed/')) {
                $iframeSrc .= '&modestbranding=1&controls=1';
            }

            if ($resumeSeconds > 5 && !($isCompleted ?? false)) {
                $iframeSrc .= '&start=' . $resumeSeconds;
            }
        }

        $parseDurationToMinutes = function ($duration) {
            if (empty($duration)) {
                return 0;
            }

            $duration = strtolower(trim($duration));
            $minutes = 0;

            if (preg_match('/(\d+)\s*hr/', $duration, $matches)) {
                $minutes += ((int) $matches[1]) * 60;
            }

            if (preg_match('/(\d+)\s*min/', $duration, $matches)) {
                $minutes += (int) $matches[1];
            }

            return $minutes;
        };

        $totalCourseMinutes = 0;
        foreach ($orderedLessons as $courseLessonForDuration) {
            $totalCourseMinutes += $parseDurationToMinutes($courseLessonForDuration->duration ?? null);
        }

        $totalCourseDurationHuman = null;
        if ($totalCourseMinutes > 0) {
            $hours = intdiv($totalCourseMinutes, 60);
            $minutes = $totalCourseMinutes % 60;

            if ($hours > 0 && $minutes > 0) {
                $totalCourseDurationHuman = $hours . ' hr ' . $minutes . ' min';
            } elseif ($hours > 0) {
                $totalCourseDurationHuman = $hours . ' hr';
            } else {
                $totalCourseDurationHuman = $minutes . ' min';
            }
        }

        $allIds = $orderedLessons->pluck('id')->values()->all();
        $lessonResources = $lesson->resources;
        $studentNote = null;
        $assignment = $lesson->assignment;
        $studentSubmission = null;

        $lessonQuizzesQuery = $lesson->quizzes()
            ->where('course_id', $course->id)
            ->withCount('questions');

        if (auth()->check() && auth()->user()->role === 'student') {
            $lessonQuizzesQuery->where('is_published', true);
        }

        $lessonQuizzes = $lessonQuizzesQuery->get();

        $lessonComments = $lesson->discussionMessages()
            ->paginate(10);

        if (auth()->check() && auth()->user()->role === 'student') {
            $studentNote = \App\Models\LessonNote::where('lesson_id', $lesson->id)
                ->where('user_id', auth()->id())
                ->first();

            if ($assignment) {
                $studentSubmission = \App\Models\AssignmentSubmission::where('lesson_assignment_id', $assignment->id)
                    ->where('user_id', auth()->id())
                    ->first();
            }
        }
    @endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $course->title }} — {{ $lesson->title }}</h1>
            <div class="flex flex-wrap items-center gap-2 mt-1.5 text-sm text-slate-500">
                <span>Lesson {{ $lessonNumber }} of {{ $totalLessons }}</span>
                @if(!empty($lesson->module_id) && isset($lesson->module) && $lesson->module)
                    <span>·</span>
                    <span>Module: <span class="font-medium text-slate-600">{{ $lesson->module->title }}</span></span>
                @endif
                @if(!empty($lesson->duration))
                    <span>·</span>
                    <span>{{ $lesson->duration }}</span>
                @endif
            </div>
        </div>
        <a href="{{ route('courses.show', $course->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition">
            ← Back to Course
        </a>
    </div>

    {{-- Session / Error Alerts --}}
    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 p-4">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <div class="font-semibold text-green-800">✅ {{ session('success') }}</div>
                    <div class="text-sm text-green-700 mt-1">Your progress or lesson action has been updated successfully.</div>
                </div>
                @if($nextLesson)
                    <a href="{{ route('lessons.show', [$course->id, $nextLesson->id]) }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-xl font-medium hover:bg-green-700 transition">
                        Continue to Next Lesson →
                    </a>
                @endif
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
            <ul class="list-disc pl-5 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            {{-- Lesson Content --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Lesson Content</h3>
                        <p class="text-sm text-slate-500 mt-0.5">Lesson {{ $lessonNumber }} of {{ $totalLessons }}</p>
                    </div>
                    @if(!empty($lesson->duration))
                        <span class="inline-flex items-center rounded-full bg-slate-100 border border-slate-200 px-3 py-1 text-sm text-slate-700">
                            Duration: {{ $lesson->duration }}
                        </span>
                    @endif
                </div>

                @if(!empty($lesson->video_url))
                    @if($resumeSeconds > 5 && !($isCompleted ?? false))
                        <div class="rounded-xl border border-blue-200 bg-blue-50 p-3 text-blue-800 text-sm">
                            Resume available: this lesson should continue from around {{ gmdate('i:s', $resumeSeconds) }}.
                        </div>
                    @endif

                    <div class="mt-2">
                        <iframe
                            id="lesson-video-player"
                            width="100%"
                            height="450"
                            src="{{ $iframeSrc }}"
                            title="{{ $lesson->title }}"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                            style="border-radius:14px;">
                        </iframe>
                    </div>
                @endif

                <div class="text-slate-700 whitespace-pre-line leading-relaxed">
                    {{ $lesson->content ?? 'No content yet.' }}
                </div>
            </div>

            {{-- Assignment --}}
            @if($assignment)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Lesson Assignment</h3>
                            <p class="text-sm text-slate-500 mt-0.5">Submit your work for this lesson.</p>

                            @if(auth()->check() && auth()->user()->role === 'student')
                                @php
                                    $status = 'pending';
                                    if ($studentSubmission) {
                                        $status = !is_null($studentSubmission->score) ? 'graded' : 'submitted';
                                    }
                                @endphp
                                <div class="mt-2">
                                    @if($status === 'pending')
                                        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">⏳ Pending Submission</span>
                                    @elseif($status === 'submitted')
                                        <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold">📤 Submitted – Awaiting Grade</span>
                                    @elseif($status === 'graded')
                                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">✅ Graded</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if(auth()->check() && in_array(auth()->user()->role, ['instructor', 'admin']))
                            <a href="{{ route('assignments.submissions', [$course->id, $lesson->id]) }}"
                               class="inline-flex items-center px-4 py-2 border border-slate-200 rounded-xl text-sm text-slate-700 hover:bg-slate-50 transition">
                                View Submissions
                            </a>
                        @endif
                    </div>

                    <div class="border border-slate-200 rounded-xl p-4 bg-slate-50">
                        <div class="font-semibold text-slate-800">{{ $assignment->title }}</div>

                        @if($assignment->due_at)
                            <div class="mt-2 text-sm text-red-600 font-medium">⏰ Due: {{ $assignment->due_at->format('M j, Y g:i A') }}</div>
                        @endif

                        @if($assignment->instructions)
                            <div class="mt-2 text-sm text-slate-700 whitespace-pre-line">{{ $assignment->instructions }}</div>
                        @endif

                        <div class="mt-3 text-sm text-slate-500 flex flex-wrap gap-4">
                            <span>Max Score: {{ $assignment->max_score }}</span>
                            @if($assignment->due_at)
                                <span>Due: {{ $assignment->due_at->format('M j, Y g:i A') }}</span>
                            @endif
                        </div>

                        @if($assignment->attachment_path)
                            <div class="mt-4">
                                <a href="{{ route('assignments.attachment.download', [$course->id, $lesson->id]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm rounded-xl font-medium hover:bg-indigo-700 transition">
                                    Download Assignment File
                                </a>
                                @if($assignment->attachment_name)
                                    <div class="text-xs text-slate-500 mt-2">File: {{ $assignment->attachment_name }}</div>
                                @endif
                            </div>

                            @if(!empty($assignmentPreviewHeaders) && !empty($assignmentPreviewRows))
                                <div class="mt-5 rounded-xl border border-slate-200 bg-white overflow-hidden">
                                    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                                        <h4 class="font-semibold text-slate-800 text-sm">Dataset Preview</h4>
                                        <p class="text-xs text-slate-500 mt-1">Showing the first 5 rows of the attached CSV file.</p>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead class="bg-slate-50">
                                                <tr>
                                                    @foreach($assignmentPreviewHeaders as $header)
                                                        <th class="px-4 py-3 text-left font-semibold text-slate-700 border-b border-slate-200">{{ $header }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($assignmentPreviewRows as $row)
                                                    <tr class="border-b border-slate-100 last:border-b-0">
                                                        @foreach($row as $cell)
                                                            <td class="px-4 py-3 text-slate-700">{{ $cell }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    @if(auth()->check() && auth()->user()->role === 'student')
                        @if($studentSubmission)
                            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">
                                <div class="font-semibold">✅ Assignment Submitted</div>
                                <div class="text-sm mt-1">
                                    Submitted {{ $studentSubmission->submitted_at ? $studentSubmission->submitted_at->format('M j, Y g:i A') : '' }}
                                </div>
                                <div class="mt-3 flex flex-wrap gap-3">
                                    <a href="{{ route('assignments.download', [$course->id, $lesson->id, $studentSubmission->id]) }}"
                                       class="inline-flex items-center px-4 py-2 border border-green-300 rounded-xl text-sm text-green-800 hover:bg-white transition">
                                        Download My Submission
                                    </a>
                                </div>
                                @if(!is_null($studentSubmission->score))
                                    <div class="mt-3 text-sm"><strong>Score:</strong> {{ $studentSubmission->score }}/{{ $assignment->max_score }}</div>
                                @endif
                                @if(!empty($studentSubmission->instructor_feedback))
                                    <div class="mt-2 text-sm whitespace-pre-line"><strong>Feedback:</strong><br>{{ $studentSubmission->instructor_feedback }}</div>
                                @endif
                            </div>
                        @endif

                        <details>
                            <summary class="cursor-pointer text-sm text-blue-600 hover:underline list-none font-medium">
                                {{ $studentSubmission ? 'Resubmit Assignment' : 'Submit Assignment' }}
                            </summary>

                            <div class="mt-4">
                                <form method="POST"
                                      action="{{ route('assignments.submit', [$course->id, $lesson->id]) }}"
                                      enctype="multipart/form-data"
                                      class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="type" value="general">

                                    <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 bg-slate-50">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Upload Assignment File</label>
                                        <input type="file" name="submission_file" required
                                               class="block w-full text-sm text-slate-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        <p class="text-xs text-slate-500 mt-2">Maximum file size: 20MB</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Student Note</label>
                                        <textarea name="student_note" rows="4"
                                                  class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                                                  placeholder="Optional note for your instructor...">{{ old('student_note', $studentSubmission->student_note ?? '') }}</textarea>
                                    </div>

                                    <button type="submit"
                                            class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl shadow-sm transition hover:bg-blue-700">
                                        {{ $studentSubmission ? 'Resubmit Assignment' : 'Submit Assignment' }}
                                    </button>
                                </form>
                            </div>
                        </details>
                    @endif
                </div>
            @endif

            {{-- Resources --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="font-bold text-slate-800 text-lg">Lesson Resources</h3>

                @if($lessonResources->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-200 p-5 bg-slate-50 text-slate-500 text-sm text-center">
                        No downloadable resources for this lesson yet.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($lessonResources as $resource)
                            <div class="border border-slate-200 rounded-xl p-4 flex items-center justify-between flex-wrap gap-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <div class="font-semibold text-slate-800">{{ $resource->title }}</div>
                                        <span class="inline-flex items-center rounded-full bg-blue-50 border border-blue-200 px-2 py-0.5 text-xs text-blue-700">
                                            {{ $resource->simple_type }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1 flex flex-wrap gap-3">
                                        @if($resource->file_name)
                                            <span>{{ $resource->file_name }}</span>
                                        @endif
                                        <span>{{ $resource->human_file_size }}</span>
                                        <span>Downloads: {{ $resource->download_count }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('lesson-resources.download', [$course->id, $lesson->id, $resource->id]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-slate-800 text-white text-sm rounded-xl hover:bg-slate-900 transition">
                                    Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Quizzes --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Lesson Quiz</h3>
                        <p class="text-sm text-slate-500 mt-0.5">Test understanding before moving to the next lesson.</p>
                    </div>
                    @if(auth()->check() && in_array(auth()->user()->role, ['instructor', 'admin']))
                        <a href="{{ route('quizzes.create', $course->id) }}?lesson_id={{ $lesson->id }}"
                           class="inline-flex items-center px-4 py-2 border border-slate-200 rounded-xl text-sm text-slate-700 hover:bg-slate-50 transition">
                            + Create Lesson Quiz
                        </a>
                    @endif
                </div>

                @if($lessonQuizzes->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-200 p-5 bg-slate-50 text-slate-500 text-sm text-center">
                        No quiz has been attached to this lesson yet.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($lessonQuizzes as $lessonQuiz)
                            @php
                                $attemptsCount = 0;
                                $bestPercentage = null;

                                if (auth()->check() && auth()->user()->role === 'student') {
                                    $studentQuizAttempts = $lessonQuiz->attempts()
                                        ->where('user_id', auth()->id())
                                        ->where('status', 'submitted')
                                        ->get();

                                    $attemptsCount = $studentQuizAttempts->count();

                                    if ($studentQuizAttempts->isNotEmpty()) {
                                        $bestAttempt = $studentQuizAttempts->sortByDesc(function ($attempt) {
                                            return $attempt->percentage ?? 0;
                                        })->first();

                                        if (!is_null($bestAttempt->percentage)) {
                                            $bestPercentage = $bestAttempt->percentage;
                                        } elseif (
                                            isset($bestAttempt->score) &&
                                            isset($bestAttempt->total) &&
                                            (int) $bestAttempt->total > 0
                                        ) {
                                            $bestPercentage = ($bestAttempt->score / $bestAttempt->total) * 100;
                                        }
                                    }
                                }
                            @endphp

                            <div class="border border-slate-200 rounded-xl p-4 bg-slate-50">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ $lessonQuiz->title }}</div>
                                        <div class="text-sm text-slate-500 mt-1">
                                            Questions: {{ $lessonQuiz->questions_count ?? 0 }}
                                            @if(!is_null($lessonQuiz->pass_mark))
                                                • Pass Mark: {{ rtrim(rtrim(number_format($lessonQuiz->pass_mark, 2), '0'), '.') }}%
                                            @endif
                                            @if(!empty($lessonQuiz->time_limit_minutes))
                                                • Time Limit: {{ $lessonQuiz->time_limit_minutes }} min
                                            @endif
                                            @if(!is_null($lessonQuiz->max_attempts))
                                                • Attempts Allowed: {{ $lessonQuiz->max_attempts }}
                                            @endif
                                        </div>

                                        @if(auth()->check() && auth()->user()->role === 'student')
                                            <div class="text-xs text-slate-500 mt-2">
                                                Attempts used: {{ $attemptsCount }}
                                                @if(!is_null($bestPercentage))
                                                    • Best Score: {{ rtrim(rtrim(number_format($bestPercentage, 2), '0'), '.') }}%
                                                @endif
                                            </div>
                                        @endif

                                        @if(auth()->check() && in_array(auth()->user()->role, ['instructor', 'admin']) && !$lessonQuiz->is_published)
                                            <div class="mt-2">
                                                <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-800 px-3 py-1 text-xs font-semibold">
                                                    Draft / Unpublished
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('quizzes.show', [$course->id, $lessonQuiz->id]) }}"
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-xl font-medium shadow-sm transition hover:bg-blue-700">
                                            {{ auth()->check() && auth()->user()->role === 'student' ? 'Take Quiz' : 'Open Quiz' }}
                                        </a>

                                        @if(auth()->check() && in_array(auth()->user()->role, ['instructor', 'admin']))
                                            <a href="{{ route('quizzes.edit', [$course->id, $lessonQuiz->id]) }}"
                                               class="inline-flex items-center px-4 py-2 border border-slate-200 rounded-xl text-sm text-slate-700 hover:bg-white transition">
                                                Edit Quiz
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('quizzes.destroy', [$course->id, $lessonQuiz->id]) }}"
                                                  onsubmit="return confirm('Delete this quiz? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm rounded-xl hover:bg-red-700 transition">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- DRAB Banner --}}
            @if($lesson->enable_drab)
                <div class="rounded-2xl p-6 shadow-lg" style="background:linear-gradient(135deg,#312e81 0%,#5b21b6 100%);">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <h3 class="font-bold text-xl text-white">🧠 MDA Logic Benchmark</h3>
                            <p class="text-sm mt-2 text-indigo-100">This is where you train how to think like a data analyst.</p>
                            <div class="mt-3 text-sm text-indigo-100 space-y-1">
                                <div>• Apply real-world logic</div>
                                <div>• Improve speed & accuracy</div>
                                <div>• Unlock higher reasoning levels</div>
                            </div>
                            <div class="mt-3 text-xs text-white font-semibold">Not optional. This is how you build real skill.</div>
                            <div class="mt-4 text-xs text-white/90 bg-white/10 px-3 py-2 rounded-lg inline-block">
                                Recommended after completing this lesson
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <a href="{{ route('drab.index', $lesson->id) }}"
                               class="inline-flex items-center px-5 py-3 rounded-xl font-bold text-sm bg-white text-indigo-700 shadow hover:bg-indigo-50 transition">
                                🚀 Start Benchmark
                            </a>
                            <div class="text-xs text-indigo-100 mt-2">~2 mins per session</div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Discussion --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6" id="lesson-discussion">
                <details>
                    <summary class="cursor-pointer list-none">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">
                                Lesson Discussion
                                <span class="ml-2 text-sm font-normal text-slate-500">
                                    ({{ method_exists($lessonComments, 'total') ? $lessonComments->total() : $lessonComments->count() }})
                                </span>
                            </h3>
                            <p class="text-sm text-slate-500 mt-1">Ask questions, share ideas, and interact with your instructor and classmates.</p>
                        </div>
                    </summary>

                    <div class="mt-5 space-y-5">
                        @if(session('success'))
                            <div class="rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800 text-sm">
                                {{ session('success') }}
                            </div>
                        @endif

                        @auth
                            <form method="POST" action="{{ route('lessons.discussion.store', [$course->id, $lesson->id]) }}" class="space-y-4 rounded-2xl border border-blue-100 bg-blue-50/40 p-6">
                                @csrf
                                <input type="hidden" name="parent_id" id="parent_id" value="">
                                @if(request('reply_to'))
                                    @php
                                        $replyTarget = $lessonComments->firstWhere('id', (int) request('reply_to'));
                                    @endphp
                                    <div class="rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-700">
                                        Reply thread: {{ optional(optional($replyTarget)->user)->name ?? 'this comment' }}
                                        <a href="{{ route('lessons.show', [$course->id, $lesson->id]) }}#lesson-discussion" class="ml-2 underline">Cancel</a>
                                    </div>
                                @endif

                                <div id="replyingToLabel" class="hidden rounded-xl border border-blue-300 bg-blue-100 px-4 py-3 text-sm font-semibold text-blue-800"></div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Join the discussion</label>
                                    <textarea name="body" rows="4" required
                                              class="w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="Write a comment or question...">{{ old('body') }}</textarea>
                                </div>

                                <button type="submit"
                                        class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl shadow-sm transition hover:bg-blue-700">
                                    Post Comment
                                </button>
                            </form>
                        @endauth

                        @if($lessonComments->isEmpty())
                            <div class="rounded-2xl border border-dashed border-slate-200 p-8 bg-slate-50 text-center text-slate-500 text-sm">
                                No discussion yet. Be the first to ask a question or share an idea.
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($lessonComments as $comment)
                                    @php
                                        $commentUser = optional($comment->user);
                                        $commentName = $commentUser->name ?? 'User';
                                        $commentRole = strtolower($commentUser->role ?? 'member');
                                        $initials = collect(explode(' ', trim($commentName)))
                                            ->filter()
                                            ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                                            ->take(2)
                                            ->implode('');
                                        $commentInitials = $initials ?: 'U';
                                    @endphp

                                    <div id="comment-{{ $comment->id }}"
                                         class="comment-item rounded-2xl p-5 border transition {{ $commentRole === 'instructor' ? 'border-l-4 border-blue-500 bg-blue-50' : 'border-slate-200 bg-white' }}">
                                        <div class="flex items-start gap-3 flex-wrap">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-indigo-200 text-blue-800 flex items-center justify-center font-bold text-sm shrink-0">
                                                {{ $commentInitials }}
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="font-semibold text-slate-800">{{ $commentName }}</span>

                                                    @if($commentRole === 'instructor')
                                                        <span class="inline-flex items-center rounded-full border border-blue-200 bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700">
                                                            Instructor
                                                        </span>
                                                    @endif

                                                    @if($comment->is_pinned)
                                                        <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">
                                                            📌 Pinned
                                                        </span>
                                                    @endif

                                                    @if($comment->is_answer)
                                                        <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 border border-green-200 px-2.5 py-0.5 text-xs font-semibold">
                                                            ✅ Answered
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="text-xs text-slate-500 mt-0.5">
                                                    {{ ucfirst($commentRole) }} • {{ $comment->created_at?->diffForHumans() }}
                                                </div>

                                                <div class="mt-2 text-sm text-slate-700 whitespace-pre-line leading-6">
                                                    {{ $comment->body }}
                                                </div>

                                                @if($comment->replies->count())
                                                    <details class="mt-4" open>
                                                        <summary class="cursor-pointer list-none mb-2">
                                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-blue-800 border border-blue-200">
                                                                {{ $comment->replies->count() }} {{ $comment->replies->count() === 1 ? 'Reply' : 'Replies' }}
                                                            </span>
                                                        </summary>
                                                        <div class="mt-3 space-y-3 border-l-2 border-blue-200 pl-4">
                                                            @foreach($comment->replies as $reply)
                                                                @php
                                                                    $replyUser = optional($reply->user);
                                                                    $replyName = $replyUser->name ?? 'User';
                                                                @endphp
                                                                <div class="rounded-xl border border-blue-100 bg-blue-50/60 p-4">
                                                                    <div class="text-sm font-semibold text-slate-800">{{ $replyName }}</div>
                                                                    <div class="text-xs text-slate-500 mb-1">{{ $reply->created_at?->diffForHumans() }}</div>
                                                                    <div class="text-sm text-slate-700 whitespace-pre-line">{{ $reply->body }}</div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </details>
                                                @endif

                                                <div class="mt-3 flex items-center gap-2 flex-wrap text-sm">
                                                    @if(auth()->check() && in_array(auth()->user()->role, ['instructor', 'admin']))
                                                        <form method="POST" action="{{ route('lessons.discussion.pin', [$course->id, $lesson->id, $comment->id]) }}">
                                                            @csrf
                                                            <button class="inline-flex items-center rounded-xl bg-amber-100 text-amber-900 border border-amber-200 px-3 py-1.5 hover:bg-amber-200 transition">
                                                                {{ $comment->is_pinned ? 'Unpin' : 'Pin' }}
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('lessons.discussion.mark-answer', [$course->id, $lesson->id, $comment->id]) }}">
                                                            @csrf
                                                            <button class="inline-flex items-center rounded-xl bg-green-100 text-green-900 border border-green-200 px-3 py-1.5 hover:bg-green-200 transition">
                                                                {{ $comment->is_answer ? 'Unmark Answer' : 'Mark Answer' }}
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <a href="{{ route('lessons.show', [$course->id, $lesson->id]) }}?reply_to={{ $comment->id }}#lesson-discussion"
                                                       onclick="replyToComment({{ $comment->id }}, '{{ addslashes($commentName) }}'); return true;"
                                                       class="inline-flex items-center rounded-xl bg-blue-600 text-white px-3 py-1.5 hover:bg-blue-700 transition">
                                                        Reply
                                                    </a>

                                                    @if(auth()->check() && auth()->id() === $comment->user_id)
                                                        <form method="POST" action="{{ route('lessons.discussion.destroy', [$course->id, $lesson->id, $comment->id]) }}"
                                                              onsubmit="return confirm('Delete this comment?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="inline-flex items-center rounded-full bg-red-50 text-red-700 border border-red-200 px-3 py-1 hover:bg-red-100 transition">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6">
                                {{ $lessonComments->links() }}
                            </div>
                        @endif
                    </div>
                </details>
            </div>

            {{-- Student Notes --}}
            @if(auth()->check() && auth()->user()->role === 'student')
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">My Lesson Notes</h3>
                        <p class="text-sm text-slate-500 mt-0.5">Save private notes for this lesson.</p>
                    </div>

                    <details>
                        <summary class="cursor-pointer text-sm text-blue-600 hover:underline list-none font-medium">Show Notes</summary>

                        <div class="mt-4">
                            <form method="POST" action="{{ route('lessons.notes.store', [$course->id, $lesson->id]) }}" class="space-y-4">
                                @csrf
                                <textarea name="note" rows="8"
                                          class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Write your lesson notes here...">{{ old('note', $studentNote->note ?? '') }}</textarea>

                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    <p class="text-xs text-slate-500">Your notes are private and only visible to you.</p>
                                    <button type="submit"
                                            class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl shadow-sm transition hover:bg-blue-700">
                                        Save Notes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </details>
                </div>
            @endif

            {{-- Prev / Next --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        @if($previousLesson)
                            <a href="{{ route('lessons.show', [$course->id, $previousLesson->id]) }}"
                               class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-800 text-sm rounded-xl hover:bg-slate-200 transition">
                                ← Previous Lesson
                            </a>
                        @endif
                    </div>
                    <div>
                        @if($nextLesson)
                            <a href="{{ route('lessons.show', [$course->id, $nextLesson->id]) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-xl font-medium shadow-sm transition hover:bg-blue-700">
                                Next Lesson →
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <div class="lg:sticky lg:top-24 space-y-6">

                @if(auth()->check() && auth()->user()->role === 'student')
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-slate-800 text-lg">Course Progress</h3>
                            <span class="text-sm text-slate-500">{{ $completedLessons ?? 0 }} / {{ $totalLessons ?? 0 }}</span>
                        </div>

                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $progressPercent ?? 0 }}%;"></div>
                        </div>

                        <div class="text-sm text-slate-600 space-y-1">
                            <div>Progress: {{ $progressPercent ?? 0 }}%</div>
                            <div>Current: Lesson {{ $lessonNumber }}</div>
                            @if($totalCourseDurationHuman)
                                <div>Total Course Duration: {{ $totalCourseDurationHuman }}</div>
                            @endif
                        </div>
                    </div>
                @endif

                @if(auth()->check() && in_array(auth()->user()->role, ['instructor', 'admin']))
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                        <div class="flex items-start justify-between gap-3 flex-wrap">
                            <div>
                                <h3 class="font-bold text-slate-800 text-lg">Instructor/Admin Assignment Tools</h3>
                                <p class="text-sm text-slate-500 mt-0.5">Create, update, and manage this lesson assignment.</p>
                            </div>
                            @if($assignment)
                                <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 border border-green-200 px-3 py-1 text-xs font-semibold">
                                    Active Assignment
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1 text-xs font-semibold">
                                    No Assignment Yet
                                </span>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('assignments.store', [$course->id, $lesson->id]) }}" enctype="multipart/form-data" class="space-y-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Assignment Title</label>
                                <input type="text" name="title" value="{{ old('title', $assignment->title ?? '') }}" required
                                       class="w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="e.g. Excel Sales Analysis Assignment">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Instructions</label>
                                <textarea name="instructions" rows="6"
                                          class="w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Write assignment instructions...">{{ old('instructions', $assignment->instructions ?? '') }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Due Date</label>
                                    <input type="datetime-local" name="due_at"
                                           value="{{ old('due_at', isset($assignment) && $assignment->due_at ? $assignment->due_at->format('Y-m-d\TH:i') : '') }}"
                                           class="w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Max Score</label>
                                    <input type="number" name="max_score" min="1" max="1000"
                                           value="{{ old('max_score', $assignment->max_score ?? 100) }}"
                                           class="w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Assignment File</label>
                                <input type="file" name="assignment_file"
                                       class="w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="text-xs text-slate-500 mt-1">Optional. Upload dataset, PDF brief, Word file, CSV, or reference material (max 20MB).</p>
                                @if($assignment && $assignment->attachment_name)
                                    <div class="mt-2 text-sm text-slate-600">Current file: <span class="font-medium">{{ $assignment->attachment_name }}</span></div>
                                @endif
                            </div>

                            @if($assignment)
                                <div class="flex flex-wrap gap-2 text-xs text-slate-600">
                                    <span class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1">Max Score: {{ $assignment->max_score }}</span>
                                    @if($assignment->due_at)
                                        <span class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1">Due: {{ $assignment->due_at->format('M j, Y g:i A') }}</span>
                                    @endif
                                    @if($assignment->attachment_name)
                                        <span class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3 py-1">File: {{ $assignment->attachment_name }}</span>
                                    @endif
                                </div>
                            @endif

                            <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl shadow-sm transition hover:bg-blue-700">
                                {{ $assignment ? 'Update Assignment' : 'Create Assignment' }}
                            </button>
                        </form>

                        <div class="flex flex-wrap gap-2">
                            @if($assignment)
                                <a href="{{ route('assignments.submissions', [$course->id, $lesson->id]) }}"
                                   class="inline-flex items-center px-4 py-2 border border-slate-200 rounded-xl text-sm text-slate-700 hover:bg-slate-50 transition">
                                    View Assignment Submissions
                                </a>
                            @endif
                            <a href="{{ route('attendance.index', [$course->id, $lesson->id]) }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm rounded-xl hover:bg-indigo-700 transition">
                                View Attendance List
                            </a>
                        </div>
                    </div>
                @endif

                @if(auth()->check() && auth()->user()->role === 'student')
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                        <h3 class="font-bold text-slate-800 text-lg">Student Actions</h3>

                        @if($isCompleted ?? false)
                            <div class="rounded-xl border border-green-200 bg-green-50 p-3 text-green-800 font-semibold text-sm">
                                ✅ Lesson Completed
                            </div>
                            @if($nextLesson)
                                <a href="{{ route('lessons.show', [$course->id, $nextLesson->id]) }}"
                                   class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl shadow-sm transition hover:bg-blue-700">
                                    Go to Next Lesson →
                                </a>
                            @endif
                        @else
                            <div class="rounded-xl border border-blue-200 bg-blue-50 p-3 text-blue-800 text-sm">
                                Watch this lesson video to the end. Completion and attendance will be recorded automatically.
                            </div>
                            <form id="lesson-complete-form" method="POST" action="{{ route('lessons.complete', [$course->id, $lesson->id]) }}" class="hidden">
                                @csrf
                            </form>
                        @endif

                        <p class="text-xs text-slate-500">Attendance will be recorded automatically when you complete the lesson video.</p>
                    </div>
                @endif

            </div>
        </div>
    </div>

</div>

@if(auth()->check() && auth()->user()->role === 'student' && !empty($lesson->video_url))
    <script src="https://www.youtube.com/iframe_api"></script>
    <script>
        let lessonPlayer = null;
        let lessonAlreadySubmitted = false;
        let lessonProgressSaveTimer = null;
        let lessonLastSavedSeconds = 0;

        function saveLessonProgress(seconds) {
            const rounded = Math.max(0, Math.floor(seconds || 0));

            if (rounded < 0) return;
            if (rounded === lessonLastSavedSeconds) return;

            lessonLastSavedSeconds = rounded;

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('watched_seconds', rounded);

            fetch("{{ route('lessons.video-progress.store', [$course->id, $lesson->id]) }}", {
                method: "POST",
                body: formData,
                headers: {
                    "Accept": "application/json"
                }
            }).catch(error => {
                console.error('Video progress save failed:', error);
            });
        }

        function startLessonProgressTracking() {
            if (lessonProgressSaveTimer) {
                clearInterval(lessonProgressSaveTimer);
            }

            lessonProgressSaveTimer = setInterval(() => {
                if (!lessonPlayer || typeof lessonPlayer.getCurrentTime !== 'function') return;

                try {
                    const current = lessonPlayer.getCurrentTime();
                    saveLessonProgress(current);
                } catch (e) {
                    console.error('Progress timer error:', e);
                }
            }, 5000);
        }

        window.onYouTubeIframeAPIReady = function () {
            const iframe = document.getElementById('lesson-video-player');
            if (!iframe) return;

            lessonPlayer = new YT.Player('lesson-video-player', {
                events: {
                    onReady: function () {
                        startLessonProgressTracking();
                    },
                    onStateChange: function (event) {
                        if (event.data === YT.PlayerState.PLAYING) {
                            startLessonProgressTracking();
                        }

                        if (event.data === YT.PlayerState.PAUSED) {
                            try {
                                saveLessonProgress(lessonPlayer.getCurrentTime());
                            } catch (e) {
                                console.error('Pause save error:', e);
                            }
                        }

                        if (event.data === YT.PlayerState.ENDED && !lessonAlreadySubmitted) {
                            lessonAlreadySubmitted = true;

                            try {
                                saveLessonProgress(0);
                            } catch (e) {
                                console.error('End save error:', e);
                            }

                            const form = document.getElementById('lesson-complete-form');
                            if (form) {
                                form.submit();
                            }
                        }
                    }
                }
            });
        };

        window.addEventListener('beforeunload', function () {
            if (!lessonPlayer || typeof lessonPlayer.getCurrentTime !== 'function') return;

            try {
                saveLessonProgress(lessonPlayer.getCurrentTime());
            } catch (e) {
                console.error('Before unload save error:', e);
            }
        });
    </script>
@endif

<script>
function replyToComment(commentId, name) {
    const input = document.getElementById('parent_id');
    if (input) input.value = commentId;

    const label = document.getElementById('replyingToLabel');
    if (label) {
        label.textContent = 'Reply thread: ' + name + '...';
        label.classList.remove('hidden');
    }

    const textarea = document.querySelector('textarea[name="body"]');
    if (textarea) textarea.focus();

    document.querySelectorAll('.comment-item').forEach(el => {
        el.classList.remove('ring-2', 'ring-blue-400');
    });

    const target = document.getElementById('comment-' + commentId);
    if (target) {
        target.classList.add('ring-2', 'ring-blue-400');
    }

    const anchor = document.getElementById('lesson-discussion');
    if (anchor) anchor.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>

<style>
details > summary::-webkit-details-marker { display: none; }
</style>

</x-app-layout>
