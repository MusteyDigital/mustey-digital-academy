<h1>Create Course</h1>

<form method="POST" action="{{ route('courses.store') }}">
    @csrf
    <label>Title:</label>
    <input type="text" name="title" required><br><br>

    <label>Description:</label>
    <textarea name="description"></textarea><br><br>

    <button type="submit">Create</button>
</form>
