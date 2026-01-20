<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
</head>
<body>
    <h1>Welcome, {{ auth()->user()->name }}!</h1>
<h2>Your Courses</h2>

@if($courses->isEmpty())
    <p>You are not enrolled in any courses yet.</p>
@else
    <ul>
    @foreach($courses as $course)
        <li>
            <a href="{{ route('courses.show', $course->id) }}">{{ $course->title }}</a> 
            (Status: {{ $course->pivot->status }})
        </li>
    @endforeach
    </ul>
@endif

</body>
</html>
