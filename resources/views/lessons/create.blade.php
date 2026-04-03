<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Add Lesson — {{ $course->title }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Create a new lesson and optionally assign it to a module.
                </p>
            </div>

            <a href="{{ route('courses.show', $course->id) }}" class="underline text-gray-600">
                ← Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 border space-y-6">

                @if($errors->any())
                    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('lessons.store', $course->id) }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                            Lesson Title
                        </label>
                        <input
                            id="title"
                            name="title"
                            type="text"
                            value="{{ old('title') }}"
                            required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="e.g. Introduction to Data Analysis"
                        >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">
                                Duration
                            </label>
                            <input
                                id="duration"
                                name="duration"
                                type="text"
                                value="{{ old('duration') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="e.g. 21 min"
                            >
                            <p class="text-xs text-gray-500 mt-1">
                                Example: 5 min, 21 min, 1 hr 10 min
                            </p>
                        </div>

                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 mb-1">
                                Lesson Order
                            </label>
                            <input
                                id="order"
                                name="order"
                                type="number"
                                min="0"
                                value="{{ old('order', 0) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="module_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Module
                        </label>
                        <select
                            id="module_id"
                            name="module_id"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">-- No Module / Unassigned --</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->id }}" {{ old('module_id') == $module->id ? 'selected' : '' }}>
                                    {{ $module->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="video_url" class="block text-sm font-medium text-gray-700 mb-1">
                            Video URL
                        </label>
                        <input
                            id="video_url"
                            name="video_url"
                            type="url"
                            value="{{ old('video_url') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="https://www.youtube.com/embed/VIDEO_ID"
                        >
                        <p class="text-xs text-gray-500 mt-1">
                            Use YouTube embed format for best playback on the lesson page.
                        </p>
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                            Lesson Content
                        </label>
                        <textarea
                            id="content"
                            name="content"
                            rows="6"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Write the lesson overview or notes here..."
                        >{{ old('content') }}</textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Create Lesson</x-primary-button>

                        <a href="{{ route('courses.show', $course->id) }}"
                           class="inline-flex items-center px-4 py-2 rounded-md border text-sm text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
