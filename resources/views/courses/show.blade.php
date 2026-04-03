<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $course->title }}
            </h2>

            <a href="{{ route('courses.index') }}" class="underline text-gray-600">
                ← Back to Courses
            </a>
        </div>
    </x-slot>

    @php
        $user = auth()->user();
        $isStudent = auth()->check() && $user->role === 'student';
        $isInstructorOwner = auth()->check() && $user->role === 'instructor' && $course->instructor_id === $user->id;
        $isAdmin = auth()->check() && $user->role === 'admin';

        $enrolled = false;
        if ($isStudent) {
            $enrolled = $user->coursesEnrolled()
                ->where('courses.id', $course->id)
                ->exists();
        }

        $modules = $course->modules()
            ->with(['lessons' => function ($q) {
                $q->withCount('resources')
                    ->orderBy('order')
                    ->orderBy('id');
            }])
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $unassignedLessons = $course->lessons()
            ->withCount('resources')
            ->whereNull('module_id')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $orderedCourseLessons = $course->lessons()
            ->withCount('resources')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $orderedLessonIds = $orderedCourseLessons->pluck('id')->values()->all();

        $completedLessonIds = $completedLessonIds ?? [];
        $allLessonsCount = $course->lessons()->count();
        $completedCount = is_array($completedLessonIds) ? count($completedLessonIds) : 0;
        $percent = $allLessonsCount > 0 ? (int) round(($completedCount / $allLessonsCount) * 100) : 0;

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

        $formatMinutes = function ($minutes) {
            if ($minutes <= 0) {
                return null;
            }

            $hours = intdiv($minutes, 60);
            $mins = $minutes % 60;

            if ($hours > 0 && $mins > 0) {
                return $hours . ' hr ' . $mins . ' min';
            }

            if ($hours > 0) {
                return $hours . ' hr';
            }

            return $mins . ' min';
        };

        $totalCourseMinutes = 0;
        foreach ($orderedCourseLessons as $durationLesson) {
            $totalCourseMinutes += $parseDurationToMinutes($durationLesson->duration ?? null);
        }

        $totalCourseDurationHuman = $formatMinutes($totalCourseMinutes);

        $enrollmentCount = \App\Models\Enrollment::where('course_id', $course->id)->count();
        $certificateCount = \App\Models\Certificate::where('course_id', $course->id)->count();
        $attendanceCount = \App\Models\Attendance::where('course_id', $course->id)->count();

        $resourceDownloadCount = \App\Models\LessonResource::whereHas('lesson', function ($q) use ($course) {
            $q->where('course_id', $course->id);
        })->sum('download_count');

        $assignmentSubmissionCount = \App\Models\AssignmentSubmission::whereHas('assignment.lesson', function ($q) use ($course) {
            $q->where('course_id', $course->id);
        })->count();

        $gradedSubmissionCount = \App\Models\AssignmentSubmission::whereHas('assignment.lesson', function ($q) use ($course) {
            $q->where('course_id', $course->id);
        })->whereNotNull('score')->count();

        $lessonCommentCount = \App\Models\LessonDiscussionMessage::where('course_id', $course->id)->whereNull('parent_id')->count();

        $courseChatMessages = $course->courseChatMessages()->paginate(15);
        $averageStudentProgress = 0;
        if ($allLessonsCount > 0 && $enrollmentCount > 0) {
            $enrolledUserIds = \App\Models\Enrollment::where('course_id', $course->id)->pluck('user_id');

            $totalStudentPercent = 0;

            foreach ($enrolledUserIds as $studentId) {
                $studentCompleted = \App\Models\LessonCompletion::where('user_id', $studentId)
                    ->whereIn('lesson_id', $orderedLessonIds)
                    ->count();

                $totalStudentPercent += ($studentCompleted / $allLessonsCount) * 100;
            }

            $averageStudentProgress = (int) round($totalStudentPercent / $enrollmentCount);
        }

        $nextUnfinishedLesson = null;
        foreach ($orderedCourseLessons as $orderedLesson) {
            if (!in_array($orderedLesson->id, $completedLessonIds, true)) {
                $nextUnfinishedLesson = $orderedLesson;
                break;
            }
        }
    @endphp

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden border">
                @if(!empty($course->thumbnail))
                    <div class="w-full">
                        <img
                            src="{{ asset('storage/' . $course->thumbnail) }}"
                            alt="{{ $course->title }} thumbnail"
                            class="w-full max-h-64 object-cover rounded-t-lg"
                            style="height: 180px;"
                            loading="lazy"
                        >
                    </div>
                @endif

                <div class="p-6 space-y-3">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $course->title }}</h3>
                        <p class="text-gray-700 mt-2">{{ $course->description ?? 'No description yet.' }}</p>
                    </div>

                    <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                        <span><strong>Instructor:</strong> {{ optional($course->instructor)->name ?? '—' }}</span>
                        <span>•</span>
                        <span><strong>Lessons:</strong> {{ $allLessonsCount }}</span>
                        @if($totalCourseDurationHuman)
                            <span>•</span>
                            <span><strong>Total Duration:</strong> {{ $totalCourseDurationHuman }}</span>
                        @endif
                    </div>

                    @if($isInstructorOwner || $isAdmin)
                        <div class="pt-2 flex flex-wrap gap-2">
                            <a class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-gray-50"
                               href="{{ route('instructor.modules.index', $course->id) }}">
                                ⚙ Manage Modules
                            </a>

                            <a class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-gray-50"
                               href="{{ route('lessons.create', $course->id) }}">
                                ➕ Add Lesson
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            @auth
                @if($isInstructorOwner || $isAdmin)
                    <div class="grid grid-cols-1 md:grid-cols-4 xl:grid-cols-8 gap-4">
                        <div class="bg-white shadow-sm sm:rounded-lg border p-5">
                            <div class="text-sm text-gray-500">Enrollments</div>
                            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $enrollmentCount }}</div>
                        </div>

                        <div class="bg-white shadow-sm sm:rounded-lg border p-5">
                            <div class="text-sm text-gray-500">Lessons</div>
                            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $allLessonsCount }}</div>
                        </div>

                        <div class="bg-white shadow-sm sm:rounded-lg border p-5">
                            <div class="text-sm text-gray-500">Certificates</div>
                            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $certificateCount }}</div>
                        </div>

                        <div class="bg-white shadow-sm sm:rounded-lg border p-5">
                            <div class="text-sm text-gray-500">Attendance</div>
                            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $attendanceCount }}</div>
                        </div>

                        <div class="bg-white shadow-sm sm:rounded-lg border p-5">
                            <div class="text-sm text-gray-500">Downloads</div>
                            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $resourceDownloadCount }}</div>
                        </div>

                        <div class="bg-white shadow-sm sm:rounded-lg border p-5">
                            <div class="text-sm text-gray-500">Submissions</div>
                            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $assignmentSubmissionCount }}</div>
                        </div>

                        <div class="bg-white shadow-sm sm:rounded-lg border p-5">
                            <div class="text-sm text-gray-500">Graded</div>
                            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $gradedSubmissionCount }}</div>
                        </div>

                        <div class="bg-white shadow-sm sm:rounded-lg border p-5">
                            <div class="text-sm text-gray-500">Avg Progress</div>
                            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $averageStudentProgress }}%</div>
                        </div>
                    </div>

                    <div class="bg-white shadow-sm sm:rounded-lg border p-6">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div>
                                <h3 class="font-semibold text-gray-800 text-lg">Instructor Analytics</h3>
                                <p class="text-sm text-gray-500">
                                    Course activity overview including assignments, downloads, and discussions.
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="rounded-xl border p-4 bg-gray-50">
                                <div class="text-sm text-gray-500">Lesson Discussions</div>
                                <div class="text-2xl font-bold text-gray-900 mt-1">{{ $lessonCommentCount }}</div>
                            </div>

                            <div class="rounded-xl border p-4 bg-gray-50">
                                <div class="text-sm text-gray-500">Submission Grading Rate</div>
                                <div class="text-2xl font-bold text-gray-900 mt-1">
                                    {{ $assignmentSubmissionCount > 0 ? (int) round(($gradedSubmissionCount / $assignmentSubmissionCount) * 100) : 0 }}%
                                </div>
                            </div>

                            <div class="rounded-xl border p-4 bg-gray-50">
                                <div class="text-sm text-gray-500">Downloads per Enrollment</div>
                                <div class="text-2xl font-bold text-gray-900 mt-1">
                                    {{ $enrollmentCount > 0 ? number_format($resourceDownloadCount / $enrollmentCount, 1) : '0.0' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth

            @auth
                @if($isStudent)
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
                        <h3 class="font-semibold text-gray-800 mb-3">Enrollment</h3>

                        <div class="mb-4">
                            <span class="text-sm text-gray-500">Course Price:</span>
                            <div class="mt-1 text-2xl font-bold text-gray-900">
                                @if((int) ($course->price ?? 0) > 0)
                                    ₦{{ number_format((int) $course->price) }}
                                @else
                                    Free
                                @endif
                            </div>
                        </div>

                        @if(!$enrolled)
                            @if((int) ($course->price ?? 0) > 0)
                                <form method="POST" action="{{ route('payments.initialize', $course->id) }}" class="space-y-3">
                                    @csrf

                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Coupon Code (Optional)</label>
                                        <input type="text"
                                               name="coupon_code"
                                               value="{{ old('coupon_code') }}"
                                               placeholder="Enter coupon code"
                                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <x-primary-button>Pay & Enroll</x-primary-button>
                                </form>

                                <p class="text-xs text-gray-500 mt-2">
                                    Secure payment will be processed before enrollment is activated.
                                </p>
                            @else
                                <form method="POST" action="{{ route('payments.initialize', $course->id) }}">
                                    @csrf
                                    <x-primary-button>Enroll Free</x-primary-button>
                                </form>

                                <p class="text-xs text-gray-500 mt-2">
                                    After enrolling, lessons will be accessible and progress tracking will start.
                                </p>
                            @endif
                        @else
                            <div class="flex flex-wrap gap-3 items-center">
                                <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 px-3 py-1">
                                    ✅ You are enrolled
                                </span>

                                <form method="POST" action="{{ route('courses.unenroll', $course->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button>Unenroll</x-danger-button>
                                </form>
                            </div>

                            <p class="text-xs text-gray-500 mt-2">
                                You can unenroll if you no longer want to continue this course.
                            </p>
                        @endif
                    </div>
                @endif
            @endauth
            @auth
                @if(($isStudent && $enrolled) || in_array(auth()->user()->role, ['instructor', 'admin']))
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border" id="course-chat">
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <div>
                                <h3 class="font-semibold text-gray-800 text-lg">Course Community Chat</h3>
                                <p class="text-sm text-gray-500">
                                    Chat with other learners and the instructor in this course.
                                </p>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ method_exists($courseChatMessages, 'total') ? $courseChatMessages->total() : $courseChatMessages->count() }} messages
                            </div>
                        </div>

                        <div class="mt-4">
                            @if (session('success'))
                                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('courses.chat.store', $course->id) }}" class="space-y-4 rounded-xl border bg-gray-50 p-4">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ request('reply_to') }}">

                                @if(request('reply_to'))
                                    @php
                                        $replyTarget = $courseChatMessages->firstWhere('id', (int) request('reply_to'));
                                    @endphp
                                    <div class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-700">
                                        Replying to {{ optional(optional($replyTarget)->user)->name ?? 'this message' }}
                                        <a href="{{ route('courses.show', $course->id) }}#course-chat" class="ml-2 underline">Cancel</a>
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Say something to the course community</label>
                                    <textarea
                                        name="body"
                                        rows="4"
                                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Write a message..."
                                        required>{{ old('body') }}</textarea>
                                </div>

                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Post Message
                                </button>
                            </form>
                        </div>

                        <div class="mt-6 space-y-4">
                            @if($courseChatMessages->isEmpty())
                                <div class="rounded-xl border border-dashed p-6 bg-gray-50 text-gray-600">
                                    No course chat yet. Start the conversation.
                                </div>
                            @else
                                @foreach($courseChatMessages as $message)
                                    @php
                                        $messageUser = optional($message->user);
                                        $messageName = $messageUser->name ?? 'User';
                                        $messageRole = strtolower($messageUser->role ?? 'member');
                                        $initials = collect(explode(' ', trim($messageName)))
                                            ->filter()
                                            ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                                            ->take(2)
                                            ->implode('');
                                    @endphp

                                    <div class="rounded-xl p-4 {{ $messageRole === 'instructor' ? 'border-l-4 border-blue-500 bg-blue-50' : 'border bg-white' }}">
                                        <div class="flex items-start justify-between gap-3 flex-wrap">
                                            <div class="flex items-start gap-3">
                                                <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center font-semibold text-sm">
                                                    {{ $initials ?: 'U' }}
                                                </div>

                                                <div>
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <div class="font-semibold text-gray-900">
                                                            {{ $messageName }}
                                                        </div>

                                                        @if($messageRole === 'instructor')
                                                            <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-700 border border-blue-200 px-2.5 py-0.5 text-xs font-semibold">
                                                                Instructor
                                                            </span>
                                                        @endif

                                                        @if($message->is_pinned)
                                                            <span class="inline-flex items-center rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200 px-2.5 py-0.5 text-xs font-semibold">
                                                                📌 Pinned
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ ucfirst($messageRole) }} • {{ $message->created_at?->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3 text-sm text-gray-700 whitespace-pre-line leading-6">
                                            {{ $message->body }}
                                        </div>

                                        @if($message->replies->count())
                                            <div class="mt-4 ml-10 space-y-3">
                                                @foreach($message->replies as $reply)
                                                    @php
                                                        $replyUser = optional($reply->user);
                                                        $replyName = $replyUser->name ?? 'User';
                                                    @endphp

                                                    <div class="rounded-lg border bg-gray-50 p-3">
                                                        <div class="text-sm font-semibold text-gray-800">
                                                            {{ $replyName }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 mb-1">
                                                            {{ $reply->created_at?->diffForHumans() }}
                                                        </div>
                                                        <div class="text-sm text-gray-700 whitespace-pre-line">
                                                            {{ $reply->body }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="mt-4 flex items-center gap-4 flex-wrap text-sm">
                                            @if(auth()->check() && in_array(auth()->user()->role, ['instructor', 'admin']))
                                                <form method="POST" action="{{ route('courses.chat.pin', [$course->id, $message->id]) }}">
                                                    @csrf
                                                    <button class="text-yellow-700 hover:underline">
                                                        {{ $message->is_pinned ? 'Unpin' : 'Pin' }}
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('courses.show', $course->id) }}?reply_to={{ $message->id }}#course-chat" class="text-blue-600 hover:underline">Reply</a>

                                            @if(auth()->check() && (auth()->id() === $message->user_id || in_array(auth()->user()->role, ['instructor', 'admin'])))
                                                <form method="POST"
                                                      action="{{ route('courses.chat.destroy', [$course->id, $message->id]) }}"
                                                      onsubmit="return confirm('Delete this message?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-600 hover:underline">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                <div class="pt-2">
                                    {{ $courseChatMessages->withQueryString()->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endauth

            @auth
                @if($isStudent && $enrolled)
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border space-y-4">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div>
                                <h3 class="font-semibold text-gray-800 text-lg">Progress</h3>
                                <span class="text-sm text-gray-600">
                                    {{ $completedCount }}/{{ $allLessonsCount }} lessons completed
                                </span>
                            </div>

                            <div>
                                @if($nextUnfinishedLesson)
                                    <a href="{{ route('lessons.show', [$course->id, $nextUnfinishedLesson->id]) }}"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        Continue Learning →
                                    </a>
                                @elseif($allLessonsCount > 0 && $percent === 100)
                                    <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 px-3 py-2 text-sm font-semibold">
                                        ✅ Course Completed
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-green-600 h-3 rounded-full" style="width: {{ $percent }}%;"></div>
                        </div>

                        <p class="text-sm text-gray-700">
                            <strong>{{ $percent }}%</strong> complete
                        </p>

                        @if($allLessonsCount > 0 && $percent === 100)
                            <div class="rounded-xl border border-green-200 bg-green-50 p-4">
                                <h3 class="font-semibold text-gray-800 mb-2">Certificate</h3>
                                <a href="{{ route('certificates.download', $course->id) }}">
                                    <x-primary-button>Download Certificate (PDF)</x-primary-button>
                                </a>
                                <p class="text-xs text-gray-600 mt-2">
                                    Congratulations! You completed all lessons.
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            @endauth

            <div class="bg-white shadow-sm sm:rounded-lg p-6 border space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">Built-in Live Classroom</h3>
                        <p class="text-sm text-gray-500">
                            Start and join live classes directly inside the platform.
                        </p>
                    </div>

                    @auth
                        @if($isInstructorOwner || $isAdmin)
                            @if($course->activeLiveSession)
                                <a href="{{ route('live-sessions.show', $course->activeLiveSession->id) }}"
                                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    Rejoin Live Session
                                </a>
                            @else
                                <form method="POST" action="{{ route('live-sessions.start', $course->id) }}">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                        Start Live Session
                                    </button>
                                </form>
                            @endif
                        @endif
                    @endauth
                </div>

                @auth
                    @if($isStudent)
                        @if($course->activeLiveSession)
                            <div class="rounded-xl border border-green-200 bg-green-50 p-4 space-y-3">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <div class="text-sm text-green-800 font-semibold">
                                        A live class is currently active.
                                    </div>

                                    <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 px-3 py-1">
                                        ● Live Now
                                    </span>
                                </div>

                                <div class="pt-2">
                                    <a href="{{ route('live-sessions.show', $course->activeLiveSession->id) }}"
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        Join Live Session
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="rounded-lg border border-dashed p-5 text-gray-600 bg-gray-50">
                                <p class="font-semibold">No built-in live session is active.</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    When the instructor starts one, the join button will appear here.
                                </p>
                            </div>
                        @endif
                    @endif
                @endauth
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 border space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">Live Session</h3>
                        <p class="text-sm text-gray-500">
                            Join the class when scheduled and mark your live attendance.
                        </p>
                    </div>

                    @auth
                        @if($isInstructorOwner)
                            <a class="inline-flex items-center gap-2 rounded-md border px-3 py-2 text-sm hover:bg-gray-50"
                               href="{{ route('courses.session.edit', $course->id) }}">
                                ⚙ <span>Set/Update Live Session</span>
                            </a>
                        @endif
                    @endauth
                </div>

                @if($course->meeting_url)
                    <div class="rounded-xl border bg-gray-50 p-4 space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="text-sm text-gray-700">
                                <span class="font-semibold">Starts:</span>
                                <span class="ml-1 inline-flex items-center rounded-full bg-white border px-3 py-1">
                                    {{ $course->starts_at ? $course->starts_at->format('D, M j, Y g:i A') : 'Not set' }}
                                </span>
                            </div>

                            <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 px-3 py-1">
                                ✅ Scheduled
                            </span>
                        </div>

                        <div class="text-sm text-gray-700 break-all">
                            <span class="font-semibold">Meeting URL:</span>
                            <a class="underline text-blue-600 ml-1" href="{{ $course->meeting_url }}" target="_blank">
                                {{ $course->meeting_url }}
                            </a>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            @auth
                                @if($isStudent)
                                    <a href="{{ $course->meeting_url }}" target="_blank">
                                        <x-primary-button>✅ Join Class</x-primary-button>
                                    </a>
                                @else
                                    <a class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-white"
                                       href="{{ $course->meeting_url }}" target="_blank">
                                        Open Link
                                    </a>
                                @endif
                            @endauth

                            @auth
                                @if($isStudent)
                                    <form method="POST" action="{{ route('attendance.live.store', $course->id) }}">
                                        @csrf
                                        <x-primary-button>Mark Live Attendance</x-primary-button>
                                    </form>
                                @endif
                            @endauth

                            @auth
                                @if($isInstructorOwner || $isAdmin)
                                    <a class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-white"
                                       href="{{ route('attendance.live.index', $course->id) }}">
                                        📌 View Live Attendance List
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                @else
                    <div class="rounded-lg border border-dashed p-5 text-gray-600 bg-gray-50">
                        <p class="font-semibold">No live session scheduled yet.</p>
                        <p class="text-sm text-gray-500 mt-1">
                            The instructor will add a meeting link and start time here.
                        </p>
                    </div>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 border space-y-5">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-xl">Final Course Quizzes</h3>
                        <p class="text-sm text-gray-500">
                            General or end-of-course assessments not attached to a specific lesson.
                        </p>
                    </div>

                    @auth
                        @if($isInstructorOwner || $isAdmin)
                            <a href="{{ route('quizzes.create', $course->id) }}">
                                <x-primary-button>+ Create Quiz</x-primary-button>
                            </a>
                        @endif
                    @endauth
                </div>

                @php
                    $courseQuizzesQuery = \App\Models\Quiz::withCount('questions')
                        ->where('course_id', $course->id)
                        ->whereNull('lesson_id');

                    if (!$isInstructorOwner && !$isAdmin) {
                        $courseQuizzesQuery->where('is_published', true);
                    }

                    $courseQuizzes = $courseQuizzesQuery->latest()->get();
                @endphp

                @if($courseQuizzes->isEmpty())
                    <div class="rounded-lg border border-dashed p-5 text-gray-600 bg-gray-50">
                        No published quizzes available yet.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($courseQuizzes as $quizItem)
                            <div class="rounded-xl border p-5 bg-white">
                                <div class="flex items-start justify-between flex-wrap gap-4">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="font-semibold text-lg text-gray-900">{{ $quizItem->title }}</h4>

                                            @if($quizItem->is_published)
                                                <span class="inline-flex items-center rounded-full bg-green-50 text-green-700 border border-green-200 px-3 py-1 text-xs font-semibold">
                                                    Published
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200 px-3 py-1 text-xs font-semibold">
                                                    Draft
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                                            <span><strong>Questions:</strong> {{ $quizItem->questions_count }}</span>
                                            <span><strong>Pass Mark:</strong> {{ $quizItem->pass_mark ?? 0 }}%</span>
                                            <span><strong>Attempts:</strong> {{ is_null($quizItem->max_attempts) ? 'Unlimited' : $quizItem->max_attempts }}</span>
                                            <span><strong>Time:</strong> {{ is_null($quizItem->time_limit_minutes) ? 'No limit' : $quizItem->time_limit_minutes . ' min' }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('quizzes.show', [$course->id, $quizItem->id]) }}"
                                           class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                                            Open Quiz
                                        </a>

                                        @auth
                                            @if($isInstructorOwner || $isAdmin)
                                                <a href="{{ route('quizzes.edit', [$course->id, $quizItem->id]) }}"
                                                   class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                                                    Edit
                                                </a>

                                                <form method="POST"
                                                      action="{{ route('quizzes.destroy', [$course->id, $quizItem->id]) }}"
                                                      onsubmit="return confirm('Delete this quiz? This cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                        Delete
                                                    </button>
                                                </form>

                                                <a href="{{ route('instructor.quizzes.analytics', [$course->id, $quizItem->id]) }}"
                                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                                    Analytics
                                                </a>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 border space-y-5">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-xl">Course Lessons</h3>
                        <p class="text-sm text-gray-500">
                            Follow the lessons in order and track your progress.
                        </p>
                    </div>

                    @auth
                        @if($isInstructorOwner)
                            <a href="{{ route('lessons.create', $course->id) }}">
                                <x-primary-button>+ Add Lesson</x-primary-button>
                            </a>
                        @endif
                    @endauth
                </div>

                @if($isStudent && !$enrolled)
                    <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-yellow-900">
                        Enroll to access lessons.
                    </div>
                @endif

                @php
                    $canViewLessons = (!$isStudent) || ($isStudent && $enrolled);
                @endphp

                @if($canViewLessons)

                    @if($unassignedLessons->count() > 0)
                        @php
                            $unassignedCompleted = 0;
                            $unassignedMinutes = 0;
                            $unassignedNextLesson = null;

                            foreach ($unassignedLessons as $lesson) {
                                if (in_array($lesson->id, $completedLessonIds, true)) {
                                    $unassignedCompleted++;
                                } elseif (!$unassignedNextLesson) {
                                    $unassignedNextLesson = $lesson;
                                }

                                $unassignedMinutes += $parseDurationToMinutes($lesson->duration ?? null);
                            }

                            $unassignedPercent = $unassignedLessons->count() > 0
                                ? (int) round(($unassignedCompleted / $unassignedLessons->count()) * 100)
                                : 0;

                            $unassignedDurationHuman = $formatMinutes($unassignedMinutes);
                        @endphp

                        <details class="border border-gray-200 rounded-2xl overflow-hidden bg-white" open>
                            <summary class="cursor-pointer list-none bg-gray-50 px-5 py-4 border-b border-gray-200">
                                <div class="flex items-center justify-between gap-3 flex-wrap">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 text-lg">Unassigned Lessons</h4>
                                        <p class="text-sm text-gray-500">
                                            {{ $unassignedLessons->count() }} lesson{{ $unassignedLessons->count() !== 1 ? 's' : '' }}
                                            @if($unassignedDurationHuman)
                                                • {{ $unassignedDurationHuman }}
                                            @endif
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-3 flex-wrap">
                                        @if($isStudent && $enrolled)
                                            <div class="text-sm text-gray-600">
                                                {{ $unassignedCompleted }}/{{ $unassignedLessons->count() }} completed
                                                <span class="ml-2 font-semibold">{{ $unassignedPercent }}%</span>
                                            </div>

                                            @if($unassignedNextLesson)
                                                <a href="{{ route('lessons.show', [$course->id, $unassignedNextLesson->id]) }}"
                                                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                                                    Continue Module →
                                                </a>
                                            @elseif($unassignedLessons->count() > 0)
                                                <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 px-3 py-2 text-sm font-semibold">
                                                    ✅ Completed
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </summary>

                            @if($isStudent && $enrolled)
                                <div class="px-5 py-3 bg-white border-b border-gray-100">
                                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $unassignedPercent }}%;"></div>
                                    </div>
                                </div>
                            @endif

                            <div class="divide-y divide-gray-100">
                                @foreach($unassignedLessons as $index => $lesson)
                                    @php
                                        $done = is_array($completedLessonIds) && in_array($lesson->id, $completedLessonIds, true);
                                        $currentIndexForLock = array_search($lesson->id, $orderedLessonIds, true);
                                        $isLocked = false;

                                        if ($isStudent && $enrolled && $currentIndexForLock !== false && $currentIndexForLock > 0) {
                                            $prevId = $orderedLessonIds[$currentIndexForLock - 1];
                                            $prevDone = in_array($prevId, $completedLessonIds, true);
                                            $isLocked = !$prevDone && !$done;
                                        }
                                    @endphp

                                    @if($isStudent && $enrolled && $isLocked)
                                        <div class="flex items-center justify-between px-5 py-4 bg-gray-50">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-semibold text-sm">
                                                    {{ $index + 1 }}
                                                </div>

                                                <div>
                                                    <div class="font-medium text-gray-700">
                                                        {{ $lesson->title }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 flex flex-wrap gap-3">
                                                        <span>Lesson ID: {{ $lesson->id }}</span>
                                                        @if(!empty($lesson->duration))
                                                            <span>Duration: {{ $lesson->duration }}</span>
                                                        @endif
                                                        <span>Resources: {{ $lesson->resources_count }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <span class="inline-flex items-center gap-1 text-gray-500 bg-gray-100 border border-gray-200 px-3 py-1 rounded-full text-sm font-medium">
                                                🔒 Locked
                                            </span>
                                        </div>
                                    @else
                                        <a href="{{ route('lessons.show', [$course->id, $lesson->id]) }}"
                                           class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-semibold text-sm">
                                                    {{ $index + 1 }}
                                                </div>

                                                <div>
                                                    <div class="font-medium text-gray-900">
                                                        {{ $lesson->title }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 flex flex-wrap gap-3">
                                                        <span>Lesson ID: {{ $lesson->id }}</span>
                                                        @if(!empty($lesson->duration))
                                                            <span>Duration: {{ $lesson->duration }}</span>
                                                        @endif
                                                        <span>Resources: {{ $lesson->resources_count }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                @if($isStudent && $done)
                                                    <span class="inline-flex items-center gap-1 text-green-700 bg-green-50 border border-green-200 px-3 py-1 rounded-full text-sm font-medium">
                                                        ✓ Completed
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1 rounded-full text-sm font-medium">
                                                        ▶ Open Lesson
                                                    </span>
                                                @endif
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </details>
                    @endif

                    @if($modules->count() === 0 && $unassignedLessons->count() === 0)
                        <p class="text-gray-600">No lessons yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($modules as $module)
                                @php
                                    $moduleLessonCount = $module->lessons->count();
                                    $moduleCompleted = 0;
                                    $moduleMinutes = 0;
                                    $moduleNextLesson = null;

                                    foreach ($module->lessons as $moduleLesson) {
                                        if (in_array($moduleLesson->id, $completedLessonIds, true)) {
                                            $moduleCompleted++;
                                        } elseif (!$moduleNextLesson) {
                                            $moduleNextLesson = $moduleLesson;
                                        }

                                        $moduleMinutes += $parseDurationToMinutes($moduleLesson->duration ?? null);
                                    }

                                    $modulePercent = $moduleLessonCount > 0
                                        ? (int) round(($moduleCompleted / $moduleLessonCount) * 100)
                                        : 0;

                                    $moduleDurationHuman = $formatMinutes($moduleMinutes);
                                @endphp

                                <details class="border border-gray-200 rounded-2xl overflow-hidden bg-white" {{ $loop->first ? 'open' : '' }}>
                                    <summary class="cursor-pointer list-none bg-gray-50 px-5 py-4 border-b border-gray-200">
                                        <div class="flex items-center justify-between gap-3 flex-wrap">
                                            <div>
                                                <h4 class="font-semibold text-gray-900 text-lg">
                                                    {{ $module->title }}
                                                </h4>
                                                <p class="text-sm text-gray-500">
                                                    {{ $moduleLessonCount }} lesson{{ $moduleLessonCount !== 1 ? 's' : '' }}
                                                    @if($moduleDurationHuman)
                                                        • {{ $moduleDurationHuman }}
                                                    @endif
                                                </p>
                                            </div>

                                            <div class="flex items-center gap-3 flex-wrap">
                                                @if($isStudent && $enrolled)
                                                    <div class="text-sm text-gray-600">
                                                        {{ $moduleCompleted }}/{{ $moduleLessonCount }} completed
                                                        <span class="ml-2 font-semibold">{{ $modulePercent }}%</span>
                                                    </div>

                                                    @if($moduleNextLesson)
                                                        <a href="{{ route('lessons.show', [$course->id, $moduleNextLesson->id]) }}"
                                                           class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                                                            Continue Module →
                                                        </a>
                                                    @elseif($moduleLessonCount > 0)
                                                        <span class="inline-flex items-center rounded-full bg-green-100 text-green-800 px-3 py-2 text-sm font-semibold">
                                                            ✅ Completed
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </summary>

                                    @if($isStudent && $enrolled && $moduleLessonCount > 0)
                                        <div class="px-5 py-3 bg-white border-b border-gray-100">
                                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $modulePercent }}%;"></div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="divide-y divide-gray-100">
                                        @if($module->lessons->isEmpty())
                                            <div class="px-5 py-4 text-sm text-gray-500">
                                                No lessons in this module yet.
                                            </div>
                                        @else
                                            @foreach($module->lessons as $index => $lesson)
                                                @php
                                                    $done = is_array($completedLessonIds) && in_array($lesson->id, $completedLessonIds, true);
                                                    $currentIndexForLock = array_search($lesson->id, $orderedLessonIds, true);
                                                    $isLocked = false;

                                                    if ($isStudent && $enrolled && $currentIndexForLock !== false && $currentIndexForLock > 0) {
                                                        $prevId = $orderedLessonIds[$currentIndexForLock - 1];
                                                        $prevDone = in_array($prevId, $completedLessonIds, true);
                                                        $isLocked = !$prevDone && !$done;
                                                    }
                                                @endphp

                                                @if($isStudent && $enrolled && $isLocked)
                                                    <div class="flex items-center justify-between px-5 py-4 bg-gray-50">
                                                        <div class="flex items-center gap-4">
                                                            <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-semibold text-sm">
                                                                {{ $index + 1 }}
                                                            </div>

                                                            <div>
                                                                <div class="font-medium text-gray-700">
                                                                    {{ $lesson->title }}
                                                                </div>
                                                                <div class="text-sm text-gray-500 flex flex-wrap gap-3">
                                                                    <span>Lesson ID: {{ $lesson->id }}</span>
                                                                    @if(!empty($lesson->duration))
                                                                        <span>Duration: {{ $lesson->duration }}</span>
                                                                    @endif
                                                                    <span>Resources: {{ $lesson->resources_count }}</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <span class="inline-flex items-center gap-1 text-gray-500 bg-gray-100 border border-gray-200 px-3 py-1 rounded-full text-sm font-medium">
                                                            🔒 Locked
                                                        </span>
                                                    </div>
                                                @else
                                                    <a href="{{ route('lessons.show', [$course->id, $lesson->id]) }}"
                                                       class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition">
                                                        <div class="flex items-center gap-4">
                                                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-semibold text-sm">
                                                                {{ $index + 1 }}
                                                            </div>

                                                            <div>
                                                                <div class="font-medium text-gray-900">
                                                                    {{ $lesson->title }}
                                                                </div>
                                                                <div class="text-sm text-gray-500 flex flex-wrap gap-3">
                                                                    <span>Lesson ID: {{ $lesson->id }}</span>
                                                                    @if(!empty($lesson->duration))
                                                                        <span>Duration: {{ $lesson->duration }}</span>
                                                                    @endif
                                                                    <span>Resources: {{ $lesson->resources_count }}</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            @if($isStudent && $done)
                                                                <span class="inline-flex items-center gap-1 text-green-700 bg-green-50 border border-green-200 px-3 py-1 rounded-full text-sm font-medium">
                                                                    ✓ Completed
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1 rounded-full text-sm font-medium">
                                                                    ▶ Open Lesson
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </a>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
