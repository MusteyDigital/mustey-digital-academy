<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                ✏️ Edit Course
            </h2>

            <a href="{{ route('courses.show', $course->id) }}" class="underline text-gray-600">
                ← Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash --}}
            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Errors --}}
            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 border space-y-5">
                {{-- Current thumbnail preview --}}
                @if(!empty($course->thumbnail))
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Current Thumbnail</p>
                        <img
                            src="{{ asset('storage/' . $course->thumbnail) }}"
                            alt="Course thumbnail"
                            class="w-full max-w-md rounded-lg border"
                        >
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('courses.update', $course->id) }}"
                      enctype="multipart/form-data"
                      class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-sm text-gray-600">Course Title</label>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title', $course->title) }}"
                            class="w-full border rounded p-2"
                            required
                        >
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Description</label>
                        <textarea
                            name="description"
                            rows="5"
                            class="w-full border rounded p-2"
                            placeholder="Course description..."
                        >{{ old('description', $course->description) }}</textarea>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Change Thumbnail (optional)</label>
                        <input
                            type="file"
                            name="thumbnail"
                            accept="image/*"
                            class="w-full border rounded p-2 bg-white"
                        >
                        <p class="text-xs text-gray-500 mt-1">Max 2MB • JPG/PNG/WebP</p>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <x-primary-button>Save Changes</x-primary-button>

                        <a href="{{ route('courses.show', $course->id) }}"
                           class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
                <p class="text-sm text-gray-700">
                    <span class="font-semibold">Note:</span>
                    Live session settings (meeting URL + start time) are edited on the session page.
                </p>

                <div class="mt-3">
                    <a class="underline text-blue-600 text-sm"
                       href="{{ route('courses.session.edit', $course->id) }}">
                        ⚙ Edit Live Session Settings →
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
