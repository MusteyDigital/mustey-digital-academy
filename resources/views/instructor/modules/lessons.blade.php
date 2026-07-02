<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Lessons — {{ $course->title }} / {{ $module->title }}
            </h2>

            <a href="{{ route('instructor.modules.index', $course->id) }}"
               class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Back to Modules
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800 text-sm">
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-slate-800 text-lg">Add Lesson to this Module</h3>

                <form method="POST" action="{{ route('instructor.modules.lessons.store', [$course->id, $module->id]) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Lesson Title</label>
                        <input
                            name="title"
                            value="{{ old('title') }}"
                            class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Lesson title"
                            required
                        >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Duration</label>
                            <input
                                name="duration"
                                value="{{ old('duration') }}"
                                class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="e.g. 18 min"
                            >
                            <p class="text-xs text-slate-500 mt-1">
                                Example: 5 min, 21 min, 1 hr
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Order</label>
                            <input
                                name="order"
                                type="number"
                                value="{{ old('order', 0) }}"
                                class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="0"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Lesson Content</label>
                        <textarea
                            name="content"
                            class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            {{ old('enable_drab') ? 'checked' : '' }}
                        >
                        <label for="enable_drab_create" class="text-sm text-slate-700">
                            Enable DRAB Benchmark Lab for this lesson
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Video URL</label>
                        <input
                            name="video_url"
                            value="{{ old('video_url') }}"
                            class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="https://www.youtube.com/embed/VIDEO_ID"
                        >
                        <p class="text-xs text-slate-500 mt-1">
                            Use YouTube embed URL for lesson playback.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Starts At</label>
                        <input
                            name="starts_at"
                            type="datetime-local"
                            value="{{ old('starts_at') }}"
                            class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    <button class="inline-flex items-center rounded-xl bg-blue-600 text-white px-4 py-2 text-sm font-semibold shadow-sm hover:bg-blue-700 transition">
                        Add Lesson
                    </button>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 text-lg">Lessons in this Module</h3>
                    <span class="text-xs text-slate-500">Total: {{ $lessons->count() }}</span>
                </div>

                @if($lessons->isEmpty())
                    <div class="mt-4 rounded-xl border border-dashed border-slate-200 p-8 bg-slate-50 text-slate-500 text-center text-sm">
                        No lessons yet.
                    </div>
                @else
                    <div class="mt-4 space-y-4">
                        @foreach($lessons as $lesson)
                            <details class="rounded-xl border border-slate-200 bg-white hover:border-blue-200 transition">
                                <summary class="list-none cursor-pointer px-4 py-4 flex flex-wrap items-center justify-between gap-3 hover:bg-slate-50 rounded-xl">
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $lesson->title }}</div>

                                        <div class="text-xs text-slate-500 flex flex-wrap gap-3 mt-1">
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
                                        <a class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 transition"
                                           href="{{ route('lessons.show', [$course->id, $lesson->id]) }}">
                                            Preview
                                        </a>

                                        <a class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 transition"
                                           href="{{ route('instructor.modules.lessons.resources.index', [$course->id, $module->id, $lesson->id]) }}">
                                            Resources
                                        </a>

                                        <span class="text-xs text-slate-400">Click to edit</span>
                                    </div>
                                </summary>

                                <div class="border-t border-slate-200 px-4 py-4 space-y-4 bg-slate-50 rounded-b-xl">
                                    <form method="POST" action="{{ route('instructor.modules.lessons.update', [$course->id, $module->id, $lesson->id]) }}" class="space-y-4">
                                        @csrf
                                        @method('PUT')

                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Lesson Title</label>
                                            <input
                                                name="title"
                                                value="{{ old('title', $lesson->title) }}"
                                                class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                required
                                            >
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-1">Duration</label>
                                                <input
                                                    name="duration"
                                                    value="{{ old('duration', $lesson->duration) }}"
                                                    class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                    placeholder="e.g. 18 min"
                                                >
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 mb-1">Order</label>
                                                <input
                                                    name="order"
                                                    type="number"
                                                    value="{{ old('order', $lesson->order ?? 0) }}"
                                                    class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                >
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Lesson Content</label>
                                            <textarea
                                                name="content"
                                                class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                rows="4"
                                            >{{ old('content', $lesson->content) }}</textarea>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <input
                                                type="checkbox"
                                                id="enable_drab_edit_{{ $lesson->id }}"
                                                name="enable_drab"
                                                value="1"
                                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                                {{ old('enable_drab', $lesson->enable_drab) ? 'checked' : '' }}
                                            >
                                            <label for="enable_drab_edit_{{ $lesson->id }}" class="text-sm text-slate-700">
                                                Enable DRAB Benchmark Lab for this lesson
                                            </label>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Video URL</label>
                                            <input
                                                name="video_url"
                                                value="{{ old('video_url', $lesson->video_url) }}"
                                                class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="https://www.youtube.com/embed/VIDEO_ID"
                                            >
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Starts At</label>
                                            <input
                                                name="starts_at"
                                                type="datetime-local"
                                                value="{{ old('starts_at', optional($lesson->starts_at)->format('Y-m-d\TH:i')) }}"
                                                class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            >
                                        </div>

                                        <div class="flex flex-wrap items-center gap-3">
                                            <button class="inline-flex items-center rounded-xl bg-blue-600 text-white px-4 py-2 text-sm font-semibold shadow-sm hover:bg-blue-700 transition">
                                                Save Changes
                                            </button>
                                        </div>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('instructor.modules.lessons.destroy', [$course->id, $module->id, $lesson->id]) }}"
                                          onsubmit="return confirm('Delete this lesson?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 hover:bg-red-100 transition">
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