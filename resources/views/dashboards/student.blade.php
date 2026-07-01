<x-app-layout>
@php
    $student = auth()->user();
    $courses = $courses ?? collect();
    $totalCourses = $courses->count();
    $activeCourses = 0;
    $completedCourses = 0;
    $overallCompletedLessons = 0;
    $overallLessons = 0;

    foreach ($courses as $dashboardCourse) {
        $courseLessonIds = $dashboardCourse->lessons()->pluck('id')->toArray();
        $courseLessonCount = count($courseLessonIds);
        $courseCompletedCount = 0;
        if ($courseLessonCount > 0) {
            $courseCompletedCount = \App\Models\LessonCompletion::where('user_id', $student->id)
                ->whereIn('lesson_id', $courseLessonIds)->count();
        }
        $overallCompletedLessons += $courseCompletedCount;
        $overallLessons += $courseLessonCount;
        if ($courseLessonCount > 0 && $courseCompletedCount >= $courseLessonCount) {
            $completedCourses++;
        } else {
            $activeCourses++;
        }
    }

    $overallPercent = $overallLessons > 0 ? (int) round(($overallCompletedLessons / $overallLessons) * 100) : 0;
    $recentNotesCount = \App\Models\LessonNote::where('user_id', $student->id)->count();
    $recentVideoProgress = \App\Models\LessonVideoProgress::with(['lesson.course'])->where('user_id', $student->id)->where('watched_seconds', '>', 0)->latest('updated_at')->take(5)->get();
    $recentCompletions = \App\Models\LessonCompletion::with(['lesson.course'])->where('user_id', $student->id)->latest('completed_at')->take(5)->get();
    $recentNotes = \App\Models\LessonNote::with(['lesson.course'])->where('user_id', $student->id)->whereNotNull('note')->where('note', '!=', '')->latest()->take(5)->get();
    $recentAssignmentSubmissions = \App\Models\AssignmentSubmission::with(['assignment.lesson.course'])->where('user_id', $student->id)->latest('submitted_at')->take(5)->get();
    $upcomingLiveSessions = $courses->filter(fn($c) => !empty($c->meeting_url) && !empty($c->starts_at))->sortBy('starts_at')->take(5);
    $recentPayments = \App\Models\Payment::with('course')->where('user_id', $student->id)->where('status', 'success')->latest()->take(5)->get();
    $totalSpent = \App\Models\Payment::where('user_id', $student->id)->where('status', 'success')->sum('amount');
    $formatSeconds = function ($seconds) {
        $seconds = (int) $seconds;
        $minutes = floor($seconds / 60);
        $remaining = $seconds % 60;
        return str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($remaining, 2, '0', STR_PAD_LEFT);
    };
@endphp

<div class="space-y-6">

    {{-- Welcome Banner --}}
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold">Welcome back, {{ auth()->user()->name }} 👋</h1>
                <p class="text-blue-100 mt-1 text-sm">Keep pushing — your skills are growing every day.</p>
            </div>
            <div class="text-right">
                <div class="text-blue-100 text-xs uppercase tracking-wide">Overall Progress</div>
                <div class="text-3xl font-bold">{{ $overallPercent }}%</div>
                <div class="text-blue-100 text-xs">{{ $overallCompletedLessons }}/{{ $overallLessons }} lessons</div>
            </div>
        </div>
        <div class="mt-4 w-full bg-blue-700/50 rounded-full h-2">
            <div class="bg-white h-2 rounded-full transition-all" style="width: {{ $overallPercent }}%;"></div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="text-xs text-slate-500 font-medium uppercase tracking-wide">Enrolled</span>
            </div>
            <div class="text-3xl font-bold text-slate-800">{{ $totalCourses }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="text-xs text-slate-500 font-medium uppercase tracking-wide">Active</span>
            </div>
            <div class="text-3xl font-bold text-slate-800">{{ $activeCourses }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs text-slate-500 font-medium uppercase tracking-wide">Completed</span>
            </div>
            <div class="text-3xl font-bold text-slate-800">{{ $completedCourses }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <span class="text-xs text-slate-500 font-medium uppercase tracking-wide">My Notes</span>
            </div>
            <div class="text-3xl font-bold text-slate-800">{{ $recentNotesCount }}</div>
        </div>
    </div>

    {{-- Continue Learning --}}
    @if($courses->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Continue Learning</h2>
                <p class="text-sm text-slate-500">Pick up where you left off.</p>
            </div>
            <a href="{{ route('enrollments.my-courses') }}" class="text-sm text-blue-600 hover:underline font-medium">View all →</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            @foreach($courses as $course)
                @php
                    $orderedLessons = $course->lessons()->orderBy('order')->orderBy('id')->get();
                    $lessonIds = $orderedLessons->pluck('id')->toArray();
                    $lessonCount = $orderedLessons->count();
                    $completedLessonIds = $lessonCount > 0 ? \App\Models\LessonCompletion::where('user_id', $student->id)->whereIn('lesson_id', $lessonIds)->pluck('lesson_id')->toArray() : [];
                    $completedCount = count($completedLessonIds);
                    $coursePercent = $lessonCount > 0 ? (int) round(($completedCount / $lessonCount) * 100) : 0;
                    $nextLesson = null;
                    foreach ($orderedLessons as $ol) { if (!in_array($ol->id, $completedLessonIds, true)) { $nextLesson = $ol; break; } }
                    $latestCertificate = \App\Models\Certificate::where('user_id', $student->id)->where('course_id', $course->id)->latest()->first();
                @endphp

                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    @if(!empty($course->thumbnail))
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-36 object-cover">
                    @else
                        <div class="w-full h-36 bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                            <svg class="w-10 h-10 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                    @endif

                    <div class="p-4 space-y-3">
                        <h3 class="font-semibold text-slate-800">{{ $course->title }}</h3>
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>{{ $completedCount }}/{{ $lessonCount }} lessons</span>
                            <span class="font-semibold text-slate-700">{{ $coursePercent }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5">
                            <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $coursePercent }}%;"></div>
                        </div>
                        <div class="flex flex-wrap gap-2 pt-1">
                            @if($nextLesson)
                                <a href="{{ route('lessons.show', [$course->id, $nextLesson->id]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium">
                                    Continue →
                                </a>
                            @else
                                <a href="{{ route('courses.show', $course->id) }}"
                                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 font-medium">
                                    ✅ Review
                                </a>
                            @endif
                            @if($latestCertificate)
                                <a href="{{ route('certificates.download', $course->id) }}"
                                   class="inline-flex items-center px-4 py-2 border border-slate-200 text-slate-700 text-sm rounded-lg hover:bg-slate-50">
                                    Certificate
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- DRAB Section --}}
    @if($isDataAnalysisStudent ?? false)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-800">🧠 DRAB Performance</h2>
                <p class="text-sm text-slate-500">Your adaptive reasoning benchmark summary.</p>
            </div>
            <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full">Level {{ $drabLevel ?? 1 }}</span>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
            @foreach([['Attempts', $drabTotalAttempts ?? 0, 'slate'], ['Avg Accuracy', number_format((float)($drabAverageAccuracy ?? 0), 1).'%', 'blue'], ['Best Accuracy', number_format((float)($drabBestAccuracy ?? 0), 1).'%', 'green'], ['Streak 🔥', $drabCurrentStreak ?? 0, 'orange'], ['Best 🏆', $drabBestStreak ?? 0, 'amber']] as [$label, $value, $color])
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-center">
                <div class="text-xs text-slate-500 mb-1">{{ $label }}</div>
                <div class="text-xl font-bold text-slate-800">{{ $value }}</div>
            </div>
            @endforeach
        </div>

        <div>
            <div class="flex justify-between text-sm text-slate-600 mb-1">
                <span>XP Progress to Level {{ ($drabLevel ?? 1) + 1 }}</span>
                <span>{{ $drabCurrentLevelXp ?? 0 }}/{{ $drabNextLevelXp ?? 100 }} XP</span>
            </div>
            <div class="w-full bg-indigo-100 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $drabLevelProgressPercent ?? 0 }}%;"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Recent Activity Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Recent Watching --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-slate-800 mb-4">Recent Watching</h2>
            @if($recentVideoProgress->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-slate-400 text-sm">No recent activity yet.</div>
            @else
                <div class="space-y-3">
                    @foreach($recentVideoProgress as $progress)
                        @php $activityLesson = $progress->lesson; $activityCourse = optional($activityLesson)->course; @endphp
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-slate-800 truncate">{{ optional($activityLesson)->title ?? 'Lesson' }}</div>
                                <div class="text-xs text-slate-500">{{ $formatSeconds($progress->watched_seconds) }} watched • {{ $progress->updated_at?->diffForHumans() }}</div>
                            </div>
                            @if($activityLesson && $activityCourse)
                                <a href="{{ route('lessons.show', [$activityCourse->id, $activityLesson->id]) }}" class="ml-3 text-xs text-blue-600 hover:underline shrink-0">Resume</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Completions --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-slate-800 mb-4">Recently Completed</h2>
            @if($recentCompletions->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-slate-400 text-sm">No completed lessons yet.</div>
            @else
                <div class="space-y-3">
                    @foreach($recentCompletions as $completion)
                        @php $completionLesson = $completion->lesson; $completionCourse = optional($completionLesson)->course; @endphp
                        <div class="flex items-center justify-between p-3 rounded-xl bg-green-50 border border-green-100">
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-slate-800 truncate">{{ optional($completionLesson)->title ?? 'Lesson' }}</div>
                                <div class="text-xs text-slate-500">{{ $completion->completed_at ? \Carbon\Carbon::parse($completion->completed_at)->diffForHumans() : '' }}</div>
                            </div>
                            <span class="ml-3 text-xs bg-green-100 text-green-700 font-semibold px-2 py-1 rounded-full shrink-0">✅ Done</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Assignments --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-slate-800 mb-4">Assignment Status</h2>
            @if($recentAssignmentSubmissions->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-slate-400 text-sm">No submissions yet.</div>
            @else
                <div class="space-y-3">
                    @foreach($recentAssignmentSubmissions as $submission)
                        @php $assignment = $submission->assignment; $lesson = optional($assignment)->lesson; $course = optional($lesson)->course; @endphp
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-slate-800 truncate">{{ optional($assignment)->title ?? 'Assignment' }}</div>
                                <div class="text-xs text-slate-500">{{ optional($course)->title ?? '' }}</div>
                            </div>
                            @if(!is_null($submission->score))
                                <span class="ml-3 text-xs bg-green-100 text-green-700 font-semibold px-2 py-1 rounded-full shrink-0">{{ $submission->score }}/{{ optional($assignment)->max_score ?? 100 }}</span>
                            @else
                                <span class="ml-3 text-xs bg-amber-100 text-amber-700 font-semibold px-2 py-1 rounded-full shrink-0">Pending</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Payments --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-slate-800">Payments</h2>
                <span class="text-sm font-semibold text-green-600">₦{{ number_format($totalSpent) }} total</span>
            </div>
            @if($recentPayments->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-slate-400 text-sm">No payments yet.</div>
            @else
                <div class="space-y-3">
                    @foreach($recentPayments as $payment)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-slate-800 truncate">{{ optional($payment->course)->title ?? 'Course' }}</div>
                                <div class="text-xs text-slate-500">{{ $payment->paid_at ? $payment->paid_at->format('M j, Y') : $payment->created_at->format('M j, Y') }}</div>
                            </div>
                            <div class="flex items-center gap-2 ml-3 shrink-0">
                                <span class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-1 rounded-full">₦{{ number_format($payment->amount) }}</span>
                                <a href="{{ route('payments.receipt', $payment->id) }}" class="text-xs text-blue-600 hover:underline">Receipt</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Upcoming Live Sessions --}}
    @if($upcomingLiveSessions->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-base font-bold text-slate-800 mb-4">📅 Upcoming Live Sessions</h2>
        <div class="space-y-3">
            @foreach($upcomingLiveSessions as $liveCourse)
            <div class="flex items-center justify-between p-4 rounded-xl border border-blue-100 bg-blue-50">
                <div>
                    <div class="font-semibold text-slate-800">{{ $liveCourse->title }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ $liveCourse->starts_at ? $liveCourse->starts_at->format('D, M j, Y g:i A') : 'Time TBD' }}</div>
                </div>
                <a href="{{ $liveCourse->meeting_url }}" target="_blank"
                   class="ml-4 shrink-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium">
                    Join Live
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
</x-app-layout>
