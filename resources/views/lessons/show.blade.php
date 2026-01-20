<h1>{{ $course->title }}</h1>
<p>{{ $course->description }}</p>
<p>Instructor: {{ $course->instructor->name }}</p>

{{-- Student enroll button --}}
@if(auth()->user()->role === 'student')
    <form method="POST" action="{{ route('courses.enroll', $course->id) }}">
        @csrf
        <button type="submit">Enroll</button>
    </form>
@endif

<hr>

<h2>Lessons</h2>

@if($course->lessons->isEmpty())
    <p>No lessons yet.</p>
@else
    <ul>
        @foreach($course->lessons as $lesson)
            <li>
                <a href="{{ route('lessons.show', [$course->id, $lesson->id]) }}">
                    {{ $lesson->title }}
                </a>
            </li>
        @endforeach
    </ul>
@endif

{{-- Instructor owner can add lesson --}}
@if(auth()->user()->role === 'instructor' && $course->instructor_id === auth()->id())
    <a href="{{ route('lessons.create', $course->id) }}">+ Add Lesson</a>
@endif
