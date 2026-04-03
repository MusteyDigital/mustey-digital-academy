<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Create Quiz
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $course->title }}</p>
            </div>

            <a href="{{ route('courses.show', $course->id) }}" class="underline text-gray-600">
                ← Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border p-6 space-y-5">

                @if($errors->any())
                    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('quizzes.store', $course->id) }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quiz Title</label>
                        <input type="text"
                               name="title"
                               value="{{ old('title') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Attach to Lesson (Optional)</label>
                        <select name="lesson_id"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Final / Course Quiz</option>
                            @foreach(($lessons ?? collect()) as $lessonOption)
                                <option value="{{ $lessonOption->id }}"
                                    {{ (string) old('lesson_id', $selectedLessonId ?? '') === (string) $lessonOption->id ? 'selected' : '' }}>
                                    {{ $lessonOption->title }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Select a lesson to place this quiz inside the lesson flow.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pass Mark (%)</label>
                            <input type="number"
                                   name="pass_mark"
                                   min="0"
                                   max="100"
                                   value="{{ old('pass_mark', 50) }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Attempt Limit</label>
                            <input type="number"
                                   name="max_attempts"
                                   min="1"
                                   value="{{ old('max_attempts') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Leave empty for unlimited">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Time Limit (minutes)</label>
                            <input type="number"
                                   name="time_limit_minutes"
                                   min="1"
                                   value="{{ old('time_limit_minutes') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Leave empty for no timer">
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" name="is_published" value="1" id="is_published" class="rounded border-gray-300">
                        <label for="is_published" class="text-sm text-gray-700">Publish immediately</label>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Create Quiz
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
