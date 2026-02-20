@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">

    <div class="bg-white shadow-lg rounded-xl p-8">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">
                Create Lesson for:
                <span class="text-indigo-600">{{ $course->title }}</span>
            </h2>

            <a href="{{ route('courses.show', $course->id) }}"
               class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                ← Back to Course
            </a>
        </div>

        <form method="POST"
              action="{{ route('lessons.store', $course->id) }}"
              class="space-y-6">
            @csrf

            {{-- Title --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Lesson Title
                </label>
                <input
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                >
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Content --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Content (Optional)
                </label>
                <textarea
                    name="content"
                    rows="6"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                >{{ old('content') }}</textarea>
            </div>

            {{-- Video URL --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Video URL (Optional)
                </label>
                <input
                    type="url"
                    name="video_url"
                    value="{{ old('video_url') }}"
                    placeholder="https://youtube.com/..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                >
            </div>

            {{-- Starts At --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Starts At (Optional)
                </label>
                <input
                    type="datetime-local"
                    name="starts_at"
                    value="{{ old('starts_at') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"
                >
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-4 pt-6">

                <a href="{{ route('courses.show', $course->id) }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                    Cancel
                </a>

                <button
                    type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition duration-200 shadow-md">
                    Create Lesson
                </button>

            </div>

        </form>

    </div>

</div>
@endsection
