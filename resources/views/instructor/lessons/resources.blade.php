<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    📎 Lesson Resources — {{ $lesson->title }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $course->title }} / {{ $module->title }}
                </p>
            </div>

            <a href="{{ route('instructor.modules.lessons.index', [$course->id, $module->id]) }}" class="underline text-gray-600">
                ← Back to Module Lessons
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

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
                <h3 class="font-semibold text-gray-800">➕ Upload Resource</h3>

                <form method="POST"
                      action="{{ route('instructor.modules.lessons.resources.store', [$course->id, $module->id, $lesson->id]) }}"
                      enctype="multipart/form-data"
                      class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Resource Title</label>
                        <input
                            name="title"
                            value="{{ old('title') }}"
                            class="w-full border rounded p-2"
                            placeholder="e.g. Excel Practice File"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Choose File</label>
                        <input
                            type="file"
                            name="file"
                            class="w-full border rounded p-2"
                            required
                        >
                        <p class="text-xs text-gray-500 mt-1">
                            Max file size: 20MB
                        </p>
                    </div>

                    <button class="rounded bg-gray-900 text-white px-4 py-2 text-sm font-semibold">
                        Upload Resource
                    </button>
                </form>
            </div>

            <div class="bg-white border rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Resources</h3>
                    <span class="text-xs text-gray-500">Total: {{ $resources->count() }}</span>
                </div>

                @if($resources->isEmpty())
                    <div class="mt-4 rounded border border-dashed p-6 bg-gray-50 text-gray-600 text-center">
                        No resources uploaded yet.
                    </div>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach($resources as $resource)
                            <div class="border rounded-lg p-4 flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <div class="font-semibold text-gray-900">{{ $resource->title }}</div>
                                        <span class="inline-flex items-center rounded-full bg-blue-50 border border-blue-200 px-2 py-1 text-xs text-blue-700">
                                            {{ $resource->simple_type }}
                                        </span>
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1 flex flex-wrap gap-3">
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
                                       class="rounded border px-3 py-2 text-sm hover:bg-gray-50">
                                        Download
                                    </a>

                                    <form method="POST"
                                          action="{{ route('instructor.modules.lessons.resources.destroy', [$course->id, $module->id, $lesson->id, $resource->id]) }}"
                                          onsubmit="return confirm('Delete this resource?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 hover:bg-red-100">
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
