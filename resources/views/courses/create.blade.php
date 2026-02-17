<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ➕ Create Course
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Errors --}}
            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">
                    <p class="font-semibold mb-2">Please fix the errors below:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
                <form method="POST"
                      action="{{ route('courses.store') }}"
                      enctype="multipart/form-data"
                      class="space-y-4"
                      x-data="{ preview: null }">
                    @csrf

                    {{-- Title --}}
                    <div>
                        <label class="text-sm text-gray-600">Course Title</label>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            class="w-full border rounded p-2"
                            placeholder="e.g. Data Analysis"
                            required
                        >
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="text-sm text-gray-600">Description</label>
                        <textarea
                            name="description"
                            rows="5"
                            class="w-full border rounded p-2"
                            placeholder="Course description..."
                        >{{ old('description') }}</textarea>
                    </div>

                    {{-- Thumbnail --}}
                    <div class="space-y-2">
                        <label class="text-sm text-gray-600">Thumbnail (optional)</label>

                        <input
                            type="file"
                            name="thumbnail"
                            accept="image/*"
                            class="w-full border rounded p-2 bg-white"
                            @change="preview = URL.createObjectURL($event.target.files[0])"
                        >

                        <p class="text-xs text-gray-500">
                            Max 2MB • JPG / PNG / WEBP
                        </p>

                        {{-- Preview --}}
                        <template x-if="preview">
                            <div class="rounded-lg border bg-gray-50 p-3">
                                <p class="text-xs text-gray-600 mb-2">Preview:</p>
                                <img :src="preview" class="w-full max-h-64 object-cover rounded-md" alt="Thumbnail preview">
                            </div>
                        </template>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center gap-2 pt-2">
                        <x-primary-button>Create Course</x-primary-button>

                        <a href="{{ route('courses.index') }}"
                           class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
