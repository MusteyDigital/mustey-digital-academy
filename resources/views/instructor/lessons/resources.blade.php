<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Lesson Resources — {{ $lesson->title }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $course->title }} / {{ $module->title }}
                </p>
            </div>

            <a href="{{ route('instructor.modules.lessons.index', [$course->id, $module->id]) }}"
               class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Back to Module Lessons
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

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
                <h3 class="font-semibold text-slate-800 text-lg">Upload Resource</h3>

                <form method="POST"
                      action="{{ route('instructor.modules.lessons.resources.store', [$course->id, $module->id, $lesson->id]) }}"
                      enctype="multipart/form-data"
                      class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Resource Title</label>
                        <input
                            name="title"
                            value="{{ old('title') }}"
                            class="w-full rounded-xl border border-slate-200 p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="e.g. Excel Practice File"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Choose File</label>
                        <input
                            type="file"
                            name="file"
                            class="w-full rounded-xl border border-slate-200 p-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:px-3 file:py-1.5 file:text-sm file:font-medium hover:file:bg-blue-100"
                            required
                        >
                        <p class="text-xs text-slate-500 mt-1">
                            Max file size: 20MB
                        </p>
                    </div>

                    <button class="inline-flex items-center rounded-xl bg-blue-600 text-white px-4 py-2 text-sm font-semibold shadow-sm hover:bg-blue-700 transition">
                        Upload Resource
                    </button>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 text-lg">Resources</h3>
                    <span class="text-xs text-slate-500">Total: {{ $resources->count() }}</span>
                </div>

                @if($resources->isEmpty())
                    <div class="mt-4 rounded-xl border border-dashed border-slate-200 p-8 bg-slate-50 text-slate-500 text-center text-sm">
                        No resources uploaded yet.
                    </div>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach($resources as $resource)
                            <div class="rounded-xl border border-slate-200 p-4 flex flex-wrap items-center justify-between gap-3 hover:border-blue-200 transition">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <div class="font-semibold text-slate-900">{{ $resource->title }}</div>
                                        <span class="inline-flex items-center rounded-full bg-blue-50 border border-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">
                                            {{ $resource->simple_type }}
                                        </span>
                                    </div>

                                    <div class="text-xs text-slate-500 mt-1 flex flex-wrap gap-3">
                                        @if($resource->file_name)
                                            <span>{{ $resource->file_name }}</span>
                                        @endif

                                        <span>{{ $resource->human_file_size }}</span>
                                        <span>Downloads: {{ $resource->download_count }}</span>
                                        <span>Updated: {{ $resource->updated_at?->format('M j, Y g:i A') }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 flex-wrap">
                                    <a href="{{ route('lesson-resources.download', [$course->id, $lesson->id, $resource->id]) }}"
                                       class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 transition">
                                        Download
                                    </a>

                                    <form method="POST"
                                          action="{{ route('instructor.modules.lessons.resources.destroy', [$course->id, $module->id, $lesson->id, $resource->id]) }}"
                                          onsubmit="return confirm('Delete this resource?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 hover:bg-red-100 transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>