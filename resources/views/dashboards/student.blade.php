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
                    ->whereIn('lesson_id', $courseLessonIds)
                    ->count();
            }

            $overallCompletedLessons += $courseCompletedCount;
            $overallLessons += $courseLessonCount;

            if ($courseLessonCount > 0 && $courseCompletedCount >= $courseLessonCount) {
                $completedCourses++;
            } else {
                $activeCourses++;
            }
        }

        $overallPercent = $overallLessons > 0
            ? (int) round(($overallCompletedLessons / $overallLessons) * 100)
            : 0;

        $recentNotesCount = \App\Models\LessonNote::where('user_id', $student->id)->count();

        $recentVideoProgress = \App\Models\LessonVideoProgress::with(['lesson.course'])
            ->where('user_id', $student->id)
            ->where('watched_seconds', '>', 0)
            ->latest('updated_at')
            ->take(5)
            ->get();

        $recentCompletions = \App\Models\LessonCompletion::with(['lesson.course'])
            ->where('user_id', $student->id)
            ->latest('completed_at')
            ->take(5)
            ->get();

        $recentNotes = \App\Models\LessonNote::with(['lesson.course'])
            ->where('user_id', $student->id)
            ->whereNotNull('note')
            ->where('note', '!=', '')
            ->latest()
            ->take(5)
            ->get();

        $recentAssignmentSubmissions = \App\Models\AssignmentSubmission::with(['assignment.lesson.course'])
            ->where('user_id', $student->id)
            ->latest('submitted_at')
            ->take(5)
            ->get();

        $upcomingLiveSessions = $courses->filter(function ($course) {
                return !empty($course->meeting_url) && !empty($course->starts_at);
            })
            ->sortBy('starts_at')
            ->take(5);

        $recentPayments = \App\Models\Payment::with('course')
            ->where('user_id', $student->id)
            ->where('status', 'success')
            ->latest()
            ->take(5)
            ->get();

        $totalSpent = \App\Models\Payment::where('user_id', $student->id)
            ->where('status', 'success')
            ->sum('amount');

        $formatSeconds = function ($seconds) {
            $seconds = (int) $seconds;
            $minutes = floor($seconds / 60);
            $remaining = $seconds % 60;
            return str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($remaining, 2, '0', STR_PAD_LEFT);
        };
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            Student Dashboard
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Welcome back, {{ auth()->user()->name }}.
                        </p>
                    </div>
                </div>
            </div>
            </div>


            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6 space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">Learning Summary</h3>
                        <p class="text-sm text-gray-500">Your quick overview at a glance.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="rounded-xl border bg-gray-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-gray-500">My Courses</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">{{ $totalCourses }}</div>
                    </div>

                    <div class="rounded-xl border bg-gray-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-gray-500">Active Courses</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">{{ $activeCourses }}</div>
                    </div>

                    <div class="rounded-xl border bg-gray-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-gray-500">Completed</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">{{ $completedCourses }}</div>
                    </div>

                    <div class="rounded-xl border bg-gray-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-gray-500">My Notes</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">{{ $recentNotesCount }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6 space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">Overall Learning Progress</h3>
                        <p class="text-sm text-gray-500">
                            {{ $overallCompletedLessons }} of {{ $overallLessons }} lessons completed
                        </p>
                    </div>

                    <span class="inline-flex items-center rounded-full bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 text-sm font-semibold">
                        {{ $overallPercent }}%
                    </span>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="bg-green-600 h-3 rounded-full" style="width: {{ $overallPercent }}%;"></div>
                </div>
            </div>

                        @if(!($isDataAnalysisStudent ?? false))
                <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-800 text-lg">🧠 Logic Benchmark</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Benchmark stats and leaderboard are shown only for students enrolled in Data Analysis.
                    </p>
                </div>
            @endif

@if($isDataAnalysisStudent ?? false)
<div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6 space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">🧠 DRAB Performance</h3>
                        <p class="text-sm text-gray-500">
                            Your adaptive reasoning benchmark summary.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
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

                    <div class="rounded-xl border bg-orange-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-orange-600">Current Streak</div>
                        <div class="text-2xl font-bold text-orange-700 mt-2">🔥 {{ $drabCurrentStreak ?? 0 }}</div>
                    </div>

                    <div class="rounded-xl border bg-amber-50 p-4">
                        <div class="text-xs uppercase tracking-wide text-amber-700">Best Streak</div>
                        <div class="text-2xl font-bold text-amber-800 mt-2">🏆 {{ $drabBestStreak ?? 0 }}</div>
                    </div>
                </div>

                <div class="rounded-xl border bg-indigo-50 p-4 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h4 class="font-semibold text-indigo-900 text-lg">⭐ DRAB XP & Level</h4>
                            <p class="text-sm text-indigo-700">
                                Your reasoning progression across all DRAB attempts.
                            </p>
                        </div>

                        <div class="text-right">
                            <div class="text-xs uppercase tracking-wide text-indigo-700">Level</div>
                            <div class="text-3xl font-bold text-indigo-900">Lv {{ $drabLevel ?? 1 }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-xl border bg-white p-4">
                            <div class="text-xs uppercase tracking-wide text-gray-500">Total XP</div>
                            <div class="text-2xl font-bold text-gray-900 mt-2">{{ $drabTotalXp ?? 0 }}</div>
                        </div>

                        <div class="rounded-xl border bg-white p-4">
                            <div class="text-xs uppercase tracking-wide text-gray-500">Next Level</div>
                            <div class="text-2xl font-bold text-gray-900 mt-2">{{ $drabCurrentLevelXp ?? 0 }}/{{ $drabNextLevelXp ?? 100 }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-sm text-indigo-800 mb-2">
                            <span>Progress to Level {{ ($drabLevel ?? 1) + 1 }}</span>
                            <span>{{ $drabLevelProgressPercent ?? 0 }}%</span>
                        </div>

                        <div class="w-full bg-indigo-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-indigo-600 h-3 rounded-full" style="width: {{ $drabLevelProgressPercent ?? 0 }}%;"></div>
                        </div>
                    </div>
                </div>

            </div>

            @if(isset($drabByDifficulty))
                
<div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                    <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-lg">🧠 DRAB by Difficulty</h3>
                            <p class="text-sm text-gray-500">
                                Quick summary of your reasoning performance across levels.
                            </p>
                        </div>

                        <a href="{{ route('practice-lab.index') }}"
                           class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                            Open Logic Benchmark
                        </a>
                    </div>

                    <div class="w-full overflow-x-auto rounded-lg">
                        <table class="w-full text-[11px] sm:text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Level</th>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Try</th>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Avg</th>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Best</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard'] as $key => $label)
                                    <tr class="border-b">
                                        <td class="p-2 sm:p-3 align-top break-words font-semibold">{{ $label }}</td>
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

            @if(isset($leaderboardUsers) && $leaderboardUsers->count())
                <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                    <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-lg">🏆 DRAB Leaderboard</h3>
                            <p class="text-sm text-gray-500">
                                Top learners ranked by XP, streak, and total attempts.
                            </p>
                        </div>
                    </div>

                    <div class="hidden sm:block w-full overflow-x-auto rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Rank</th>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Student</th>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Level</th>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">XP</th>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Best Streak</th>
                                    <th class="text-left p-2 sm:p-3 align-top break-words">Attempts</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaderboardUsers as $index => $leader)
                                    <tr class="border-b">
                                        <td class="p-2 sm:p-3 align-top break-words font-semibold">#{{ $index + 1 }}</td>
                                        <td class="p-2 sm:p-3 align-top break-words">{{ $leader->name }}</td>
                                        <td class="p-2 sm:p-3 align-top break-words">Lv {{ $leader->level }}</td>
                                        <td class="p-2 sm:p-3 align-top break-words">{{ $leader->total_xp }}</td>
                                        <td class="p-2 sm:p-3 align-top break-words">🔥 {{ $leader->best_streak }}</td>
                                        <td class="p-2 sm:p-3 align-top break-words">{{ $leader->total_attempts }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="sm:hidden space-y-3">
                        @foreach($leaderboardUsers as $index => $leader)
                            <div class="rounded-lg border bg-gray-50 p-3 space-y-1">
                                <div class="text-sm"><strong>Rank:</strong> #{{ $index + 1 }}</div>
                                <div class="text-sm"><strong>Student:</strong> {{ $leader->name }}</div>
                                <div class="text-sm"><strong>Level:</strong> Lv {{ $leader->level }}</div>
                                <div class="text-sm"><strong>XP:</strong> {{ $leader->total_xp }}</div>
                                <div class="text-sm"><strong>Best Streak:</strong> 🔥 {{ $leader->best_streak }}</div>
                                <div class="text-sm"><strong>Attempts:</strong> {{ $leader->total_attempts }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            
@endif
<div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">Upcoming Live Sessions</h3>
                        <p class="text-sm text-gray-500">
                            Your nearest scheduled classes with live meeting links.
                        </p>
                    </div>
                </div>

                @if($upcomingLiveSessions->isEmpty())
                    <div class="rounded-lg border border-dashed p-4 sm:p-6 bg-gray-50 text-gray-600">
                        No upcoming live sessions scheduled yet.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($upcomingLiveSessions as $liveCourse)
                            <div class="border rounded-xl p-4">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    <div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $liveCourse->title }}
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            {{ $liveCourse->starts_at ? $liveCourse->starts_at->format('D, M j, Y g:i A') : 'Time not set' }}
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('courses.show', $liveCourse->id) }}"
                                           class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                                            Open Course
                                        </a>

                                        <a href="{{ $liveCourse->meeting_url }}"
                                           target="_blank"
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                            Join Live Class
                                        </a>
                                    </div>
                                </div>

                                <div class="mt-3 text-sm text-gray-600 break-all">
                                    {{ $liveCourse->meeting_url }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div id="my-payments" class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">My Payments</h3>
                        <p class="text-sm text-gray-500">
                            Total spent: ₦{{ number_format($totalSpent) }}
                        </p>
                    </div>
                </div>

                @if($recentPayments->isEmpty())
                    <div class="rounded-lg border border-dashed p-4 sm:p-6 bg-gray-50 text-gray-600">
                        No payment history yet.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($recentPayments as $payment)
                            <div class="border rounded-xl p-4">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    <div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $payment->course->title ?? 'Course' }}
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            Ref: {{ $payment->reference }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $payment->paid_at ? $payment->paid_at->format('M j, Y g:i A') : $payment->created_at->format('M j, Y g:i A') }}
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2 items-center">
                                        <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 px-3 py-1 text-sm font-semibold">
                                            ₦{{ number_format($payment->amount) }}
                                        </span>

                                        <a href="{{ route('payments.receipt', $payment->id) }}"
                                           class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                                            View Receipt
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">Continue Learning</h3>
                        <p class="text-sm text-gray-500">
                            Jump straight to your next unfinished lesson.
                        </p>
                    </div>

                    <a href="{{ route('enrollments.my-courses') }}" class="underline text-gray-600 text-sm">
                        View all my courses
                    </a>
                </div>

                @if($courses->isEmpty())
                    <div class="rounded-lg border border-dashed p-8 bg-gray-50 text-center text-gray-600">
                        You are not enrolled in any course yet.
                    </div>
                @else
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        @foreach($courses as $course)
                            @php
                                $orderedLessons = $course->lessons()
                                    ->orderBy('order')
                                    ->orderBy('id')
                                    ->get();

                                $lessonIds = $orderedLessons->pluck('id')->toArray();
                                $lessonCount = $orderedLessons->count();

                                $completedLessonIds = [];
                                if ($lessonCount > 0) {
                                    $completedLessonIds = \App\Models\LessonCompletion::where('user_id', $student->id)
                                        ->whereIn('lesson_id', $lessonIds)
                                        ->pluck('lesson_id')
                                        ->toArray();
                                }

                                $completedCount = count($completedLessonIds);
                                $coursePercent = $lessonCount > 0
                                    ? (int) round(($completedCount / $lessonCount) * 100)
                                    : 0;

                                $nextLesson = null;
                                foreach ($orderedLessons as $orderedLesson) {
                                    if (!in_array($orderedLesson->id, $completedLessonIds, true)) {
                                        $nextLesson = $orderedLesson;
                                        break;
                                    }
                                }

                                $latestCertificate = \App\Models\Certificate::where('user_id', $student->id)
                                    ->where('course_id', $course->id)
                                    ->latest()
                                    ->first();
                            @endphp

                            <div class="border rounded-2xl overflow-hidden bg-white shadow-sm">
                                @if(!empty($course->thumbnail))
                                    <img
                                        src="{{ asset('storage/' . $course->thumbnail) }}"
                                        alt="{{ $course->title }}"
                                        class="w-full h-40 object-cover"
                                    >
                                @endif

                                <div class="p-5 space-y-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">
                                            {{ $course->title }}
                                        </h4>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ $completedCount }}/{{ $lessonCount }} lessons completed
                                        </p>
                                    </div>

                                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                        <div class="bg-green-600 h-3 rounded-full" style="width: {{ $coursePercent }}%;"></div>
                                    </div>

                                    <div class="flex items-center justify-between flex-wrap gap-2 text-sm">
                                        <span class="text-gray-600">{{ $coursePercent }}% complete</span>

                                        @if($nextLesson)
                                            <span class="inline-flex items-center rounded-full bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 text-xs font-semibold">
                                                Next: {{ $nextLesson->title }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 border border-green-200 px-3 py-1 text-xs font-semibold">
                                                ✅ Completed
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap gap-3">
                                        @if($nextLesson)
                                            <a href="{{ route('lessons.show', [$course->id, $nextLesson->id]) }}"
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                                Continue Learning →
                                            </a>
                                        @else
                                            <a href="{{ route('courses.show', $course->id) }}"
                                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                                Review Course
                                            </a>
                                        @endif

                                        <a href="{{ route('courses.show', $course->id) }}"
                                           class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                                            Open Course
                                        </a>

                                        @if($latestCertificate)
                                            <a href="{{ route('certificates.download', $course->id) }}"
                                               class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                                                Certificate
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:p-6">
                <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                    <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-lg">Recent Watching Activity</h3>
                            <p class="text-sm text-gray-500">
                                Lessons you recently watched and can resume.
                            </p>
                        </div>
                    </div>

                    @if($recentVideoProgress->isEmpty())
                        <div class="rounded-lg border border-dashed p-4 sm:p-6 bg-gray-50 text-gray-600">
                            No recent watching activity yet.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($recentVideoProgress as $progress)
                                @php
                                    $activityLesson = $progress->lesson;
                                    $activityCourse = optional($activityLesson)->course;
                                @endphp

                                <div class="border rounded-xl p-4">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <div>
                                            <div class="font-semibold text-gray-900">
                                                {{ optional($activityLesson)->title ?? 'Lesson' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ optional($activityCourse)->title ?? 'Course' }}
                                            </div>
                                        </div>

                                        @if($activityLesson && $activityCourse)
                                            <a href="{{ route('lessons.show', [$activityCourse->id, $activityLesson->id]) }}"
                                               class="text-sm underline text-blue-600">
                                                Resume
                                            </a>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex items-center justify-between flex-wrap gap-2 text-sm text-gray-600">
                                        <span>Last saved position: {{ $formatSeconds($progress->watched_seconds) }}</span>
                                        <span>{{ $progress->updated_at?->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                    <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-lg">Recently Completed Lessons</h3>
                            <p class="text-sm text-gray-500">
                                Your latest finished lessons.
                            </p>
                        </div>
                    </div>

                    @if($recentCompletions->isEmpty())
                        <div class="rounded-lg border border-dashed p-4 sm:p-6 bg-gray-50 text-gray-600">
                            No completed lessons yet.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($recentCompletions as $completion)
                                @php
                                    $completionLesson = $completion->lesson;
                                    $completionCourse = optional($completionLesson)->course;
                                @endphp

                                <div class="border rounded-xl p-4">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <div>
                                            <div class="font-semibold text-gray-900">
                                                {{ optional($completionLesson)->title ?? 'Lesson' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ optional($completionCourse)->title ?? 'Course' }}
                                            </div>
                                        </div>

                                        <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 border border-green-200 px-3 py-1 text-xs font-semibold">
                                            ✅ Completed
                                        </span>
                                    </div>

                                    <div class="mt-3 flex items-center justify-between flex-wrap gap-2 text-sm text-gray-600">
                                        <span>{{ $completion->completed_at ? \Carbon\Carbon::parse($completion->completed_at)->format('M j, Y g:i A') : $completion->updated_at?->format('M j, Y g:i A') }}</span>

                                        @if($completionLesson && $completionCourse)
                                            <a href="{{ route('lessons.show', [$completionCourse->id, $completionLesson->id]) }}"
                                               class="underline text-blue-600">
                                                Open Lesson
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:p-6">
                <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                    <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-lg">Assignment Status</h3>
                            <p class="text-sm text-gray-500">
                                Your latest assignment submissions and grading status.
                            </p>
                        </div>
                    </div>

                    @if($recentAssignmentSubmissions->isEmpty())
                        <div class="rounded-lg border border-dashed p-4 sm:p-6 bg-gray-50 text-gray-600">
                            No assignment submissions yet.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($recentAssignmentSubmissions as $submission)
                                @php
                                    $assignment = $submission->assignment;
                                    $lesson = optional($assignment)->lesson;
                                    $course = optional($lesson)->course;
                                @endphp

                                <div class="border rounded-xl p-4">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <div>
                                            <div class="font-semibold text-gray-900">
                                                {{ optional($assignment)->title ?? 'Assignment' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ optional($course)->title ?? 'Course' }}
                                                @if($lesson)
                                                    • {{ $lesson->title }}
                                                @endif
                                            </div>
                                        </div>

                                        @if(!is_null($submission->score))
                                            <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 border border-green-200 px-3 py-1 text-xs font-semibold">
                                                Graded: {{ $submission->score }}/{{ optional($assignment)->max_score ?? 100 }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200 px-3 py-1 text-xs font-semibold">
                                                Awaiting Review
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex items-center justify-between flex-wrap gap-2 text-sm text-gray-600">
                                        <span>
                                            Submitted:
                                            {{ $submission->submitted_at ? $submission->submitted_at->format('M j, Y g:i A') : '—' }}
                                        </span>

                                        @if($lesson && $course)
                                            <a href="{{ route('lessons.show', [$course->id, $lesson->id]) }}"
                                               class="underline text-blue-600">
                                                Open Lesson
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg border p-4 sm:p-6">
                    <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-lg">Recent Notes</h3>
                            <p class="text-sm text-gray-500">
                                Your latest saved lesson notes.
                            </p>
                        </div>

                        <a href="{{ route('progress.index') }}" class="underline text-gray-600 text-sm">
                            View my progress
                        </a>
                    </div>

                    @if($recentNotes->isEmpty())
                        <div class="rounded-lg border border-dashed p-4 sm:p-6 bg-gray-50 text-gray-600">
                            No saved notes yet.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($recentNotes as $note)
                                <div class="border rounded-xl p-4">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <div>
                                            <div class="font-semibold text-gray-900">
                                                {{ optional($note->lesson)->title ?? 'Lesson' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ optional(optional($note->lesson)->course)->title ?? 'Course' }}
                                            </div>
                                        </div>

                                        @if($note->lesson && $note->lesson->course)
                                            <a href="{{ route('lessons.show', [$note->lesson->course->id, $note->lesson->id]) }}"
                                               class="text-sm underline text-blue-600">
                                                Open Lesson
                                            </a>
                                        @endif
                                    </div>

                                    <div class="mt-3 text-sm text-gray-700 whitespace-pre-line">
                                        {{ \Illuminate\Support\Str::limit($note->note, 220) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
