<h2>Enrollment Confirmed ✅</h2>

<p>Hello {{ $user->name }},</p>

<p>
    You have successfully enrolled in:
    <strong>{{ $course->title }}</strong>
</p>

@if(!empty($course->description))
    <p>{{ $course->description }}</p>
@endif

<p>— {{ config('app.name') }}</p>
