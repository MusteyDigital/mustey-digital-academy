<h1>{{ $course->title }}</h1>
<p>{{ $course->description }}</p>
<p>Instructor: {{ $course->instructor->name }}</p>

@if(auth()->user()->role === 'student')
    <form method="POST" action="{{ route('courses.enroll', $course->id) }}">
        @csrf
        <button type="submit">Enroll</button>
    </form>
@endif

