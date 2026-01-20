<h1>Courses</h1>

<a href="{{ route('courses.create') }}">Create New Course</a>

<ul>
@foreach($courses as $course)
    <li><a href="{{ route('courses.show', $course->id) }}">{{ $course->title }}</a></li>
@endforeach
</ul>
