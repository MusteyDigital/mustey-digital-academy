<h1 style="color:red;">SHOW PAGE UPDATED ✅</h1>


<h1>Create Lesson for: {{ $course->title }}</h1>

<form method="POST" action="{{ route('lessons.store', $course->id) }}">
    @csrf

    <label>Title</label><br>
    <input type="text" name="title" required><br><br>

    <label>Content (optional)</label><br>
    <textarea name="content"></textarea><br><br>

    <label>Video URL (optional)</label><br>
    <input type="text" name="video_url"><br><br>

    <label>Starts At (optional)</label><br>
    <input type="datetime-local" name="starts_at"><br><br>

    <button type="submit">Create Lesson</button>
</form>
