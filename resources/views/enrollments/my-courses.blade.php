<h1>My Courses</h1>

@if($courses->isEmpty())
    <p>You have not enrolled in any course yet.</p>
@else
    <ul>
        @foreach($courses as $course)
            <li>
                <a href="{{ route('courses.show', $course->id) }}">
                    {{ $course->title }}
                </a>
                ({{ $course->pivot->status ?? 'enrolled' }})
            </li>
        @endforeach
    </ul>
@endif
