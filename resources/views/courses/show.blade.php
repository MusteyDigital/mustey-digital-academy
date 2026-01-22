<h1>{{ $course->title }}</h1>
<p>{{ $course->description }}</p>
<p>Instructor: {{ $course->instructor->name }}</p>

<hr>

{{-- STUDENT: Enroll button --}}
@if(auth()->user()->role === 'student')
    <form method="POST" action="{{ route('courses.enroll', $course->id) }}">
        @csrf
        <button type="submit">Enroll</button>
    </form>
@endif

<hr>
<h2>Live Session</h2>

@if($course->meeting_url)
    <p><strong>Starts:</strong> {{ $course->starts_at ? $course->starts_at : 'Not set' }}</p>

    @if(auth()->user()->role === 'student')
        <a href="{{ $course->meeting_url }}" target="_blank">✅ Join Class</a>
    @else
        <a href="{{ $course->meeting_url }}" target="_blank">Open Link</a>
    @endif
@else
    <p>No live session scheduled yet.</p>
@endif

@if(auth()->user()->role === 'instructor' && $course->instructor_id === auth()->id())
    <p><a href="{{ route('courses.session.edit', $course->id) }}">⚙ Set/Update Live Session</a></p>
@endif

<hr>
<h2>Quizzes</h2>

@if(auth()->user()->role === 'instructor' && $course->instructor_id === auth()->id())
    <p>
        <a href="{{ route('quizzes.create', $course->id) }}">+ Create Quiz</a>
    </p>
@endif

{{-- If at least one quiz exists show link --}}
@php
    $firstQuiz = \App\Models\Quiz::where('course_id', $course->id)->latest()->first();
@endphp

@if($firstQuiz)
    <p>
        <a href="{{ route('quizzes.show', [$course->id, $firstQuiz->id]) }}">Open Latest Quiz: {{ $firstQuiz->title }}</a>
    </p>
@else
    <p>No quizzes yet.</p>
@endif

<hr>

<h2>Lessons</h2>

@php
    $totalLessons = $course->lessons->count();
    $completedCount = isset($completedLessonIds) ? count($completedLessonIds) : 0;
    $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
@endphp

{{-- STUDENT: progress --}}
@if(auth()->user()->role === 'student')
    <p><strong>Progress:</strong> {{ $completedCount }}/{{ $totalLessons }} ({{ $percent }}%)</p>

    <div style="width: 300px; background: #eee; border-radius: 6px; overflow: hidden; margin-bottom: 10px;">
        <div style="width: {{ $percent }}%; background: #22c55e; padding: 6px 0;"></div>
    </div>
@endif

@if($course->lessons->isEmpty())
    <p>No lessons yet.</p>
@else
    <ul>
        @foreach($course->lessons as $lesson)
            @php
                $done = isset($completedLessonIds) && in_array($lesson->id, $completedLessonIds);
            @endphp
            <li>
                <a href="{{ route('lessons.show', [$course->id, $lesson->id]) }}">
                    {{ $lesson->title }}
                </a>

                @if(auth()->user()->role === 'student')
                    @if($done)
                        <span style="color: green; font-weight: bold;">✅ Completed</span>
                    @else
                        <span style="color: #999;">(not completed)</span>
                    @endif
                @endif
            </li>
        @endforeach
    </ul>
@endif

@if(auth()->user()->role === 'instructor' && $course->instructor_id === auth()->id())
    <a href="{{ route('lessons.create', $course->id) }}">+ Add Lesson</a>
@endif
