<h1>My Courses</h1>

<ul>
@foreach($courses as $course)
    <li>
        <a href="{{ route('courses.show', $course->id) }}">{{ $course->title }}</a> 
        (Status: {{ $course->pivot->status }})
    </li>
@endforeach
</ul>
