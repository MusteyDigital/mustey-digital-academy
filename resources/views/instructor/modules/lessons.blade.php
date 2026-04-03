<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📘 Lessons — {{ $course->title }} / {{ $module->title }}
            </h2>

            <a href="{{ route('instructor.modules.index', $course->id) }}" class="underline text-gray-600">
                ← Back to Modules
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white border rounded-lg p-6 space-y-4">
                <h3 class="font-semibold text-gray-800">➕ Add Lesson to this Module</h3>

                <form method="POST" action="{{ route('instructor.modules.lessons.store', [$course->id, $module->id]) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lesson Title</label>
                        <input
                            name="title"
                            value="{{ old('title') }}"
                            class="w-full border rounded p-2"
                            placeholder="Lesson title"
                            required
                        >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                            <input
                                name="duration"
                                value="{{ old('duration') }}"
                                class="w-full border rounded p-2"
                                placeholder="e.g. 18 min"
                            >
                            <p class="text-xs text-gray-500 mt-1">
                                Example: 5 min, 21 min, 1 hr
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                            <input
                                name="order"
                                type="number"
                                value="{{ old('order', 0) }}"
                                class="w-full border rounded p-2"
                                placeholder="0"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lesson Content</label>
                        <textarea
                            name="content"
                            class="w-full border rounded p-2"
                            rows="4"
                            placeholder="Lesson content (optional)"
                        >{{ old('content') }}</textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <input
                            type="checkbox"
                            id="enable_drab_create"
                            name="enable_drab"
                            value="1"
                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                            {{ old('enable_drab') ? 'checked' : '' }}
                        >
                        <label for="enable_drab_create" class="text-sm text-gray-700">
                            Enable DRAB Benchmark Lab for this lesson
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
                        <input
                            name="video_url"
                            value="{{ old('video_url') }}"
                            class="w-full border rounded p-2"
                            placeholder="https://www.youtube.com/embed/VIDEO_ID"
                        >
                        <p class="text-xs text-gray-500 mt-1">
                            Use YouTube embed URL for lesson playback.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Starts At</label>
                        <input
                            name="starts_at"
                            type="datetime-local"
                            value="{{ old('starts_at') }}"
                            class="w-full border rounded p-2"
                        >
                    </div>

                    <button class="rounded bg-gray-900 text-white px-4 py-2 text-sm font-semibold">
                        Add Lesson
                    </button>
                </form>
            </div>

            <div class="bg-white border rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Lessons in this Module</h3>
                    <span class="text-xs text-gray-500">Total: {{ $lessons->count() }}</span>
                </div>

                @if($lessons->isEmpty())
                    <div class="mt-4 rounded border border-dashed p-6 bg-gray-50 text-gray-600 text-center">
                        No lessons yet.
                    </div>
                @else
                    <div class="mt-4 space-y-4">
                        @foreach($lessons as $lesson)
                            <details class="border rounded-xl bg-white">
                                <summary class="list-none cursor-pointer px-4 py-4 flex flex-wrap items-center justify-between gap-3 hover:bg-gray-50 rounded-xl">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $lesson->title }}</div>

                                        <div class="text-xs text-gray-500 flex flex-wrap gap-3 mt-1">
                                            <span>Lesson ID: {{ $lesson->id }}</span>

                                            @if(!empty($lesson->duration))
                                                <span>Duration: {{ $lesson->duration }}</span>
                                            @endif

                                            @if(!is_null($lesson->order))
                                                <span>Order: {{ $lesson->order }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <a class="rounded border px-3 py-2 text-sm hover:bg-gray-50"
                                           href="{{ route('lessons.show', [$course->id, $lesson->id]) }}">
                                            Preview
                                        </a>

                                        <a class="rounded border px-3 py-2 text-sm hover:bg-gray-50"
                                           href="{{ route('instructor.modules.lessons.resources.index', [$course->id, $module->id, $lesson->id]) }}">
                                            Resources
                                        </a>

                                        <span class="text-xs text-gray-400">Click to edit</span>
                                    </div>
                                </summary>

                                <div class="border-t px-4 py-4 space-y-4 bg-gray-50 rounded-b-xl">
                                    <form method="POST" action="{{ route('instructor.modules.lessons.update', [$course->id, $module->id, $lesson->id]) }}" class="space-y-4">
                                        @csrf
                                        @method('PUT')

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Lesson Title</label>
                                            <input
                                                name="title"
                                                value="{{ old('title', $lesson->title) }}"
                                                class="w-full border rounded p-2"
                                                required
                                            >
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                                                <input
                                                    name="duration"
                                                    value="{{ old('duration', $lesson->duration) }}"
                                                    class="w-full border rounded p-2"
                                                    placeholder="e.g. 18 min"
                                                >
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                                                <input
                                                    name="order"
                                                    type="number"
                                                    value="{{ old('order', $lesson->order ?? 0) }}"
                                                    class="w-full border rounded p-2"
                                                >
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Lesson Content</label>
                                            <textarea
                                                name="content"
                                                class="w-full border rounded p-2"
                                                rows="4"
                                            >{{ old('content', $lesson->content) }}</textarea>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <input
                                                type="checkbox"
                                                id="enable_drab_edit_{{ $lesson->id }}"
                                                name="enable_drab"
                                                value="1"
                                                class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                                                {{ old('enable_drab', $lesson->enable_drab) ? 'checked' : '' }}
                                            >
                                            <label for="enable_drab_edit_{{ $lesson->id }}" class="text-sm text-gray-700">
                                                Enable DRAB Benchmark Lab for this lesson
                                            </label>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
                                            <input
                                                name="video_url"
                                                value="{{ old('video_url', $lesson->video_url) }}"
                                                class="w-full border rounded p-2"
                                                placeholder="https://www.youtube.com/embed/VIDEO_ID"
                                            >
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Starts At</label>
                                            <input
                                                name="starts_at"
                                                type="datetime-local"
                                                value="{{ old('starts_at', optional($lesson->starts_at)->format('Y-m-d\TH:i')) }}"
                                                class="w-full border rounded p-2"
                                            >
                                        </div>

                                        <div class="flex flex-wrap items-center gap-3">
                                            <button class="rounded bg-gray-900 text-white px-4 py-2 text-sm font-semibold">
                                                Save Changes
                                            </button>
                                        </div>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('instructor.modules.lessons.destroy', [$course->id, $module->id, $lesson->id]) }}"
                                          onsubmit="return confirm('Delete this lesson?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 hover:bg-red-100">
                                            Delete Lesson
                                        </button>
                                    </form>
                                </div>
                            </details>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
