<!DOCTYPE html>
<html>
<head>
    <title>Instructor Dashboard</title>
</head>
<body>
    <h1>Welcome, Instructor {{ auth()->user()->name }}!</h1>
<h2>Courses You Teach</h2>

@if($courses->isEmpty())
    <p>You have not created any courses yet.</p>
@else
    <ul>
    @foreach($courses as $course)
        <li>
            <a href="{{ route('courses.show', $course->id) }}">{{ $course->title }}</a>
        </li>
    @endforeach
    </ul>
@endif

</body>
</html>
