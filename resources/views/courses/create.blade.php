<x-app-layout>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('courses.index') }}"
               class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Courses
            </a>

            <div>
                <h1 class="text-2xl font-bold text-slate-800">Create Course</h1>
                <p class="text-sm text-slate-500 mt-1">Fill in the details below to publish a new course.</p>
            </div>

            {{-- Errors --}}
            @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                    <p class="font-semibold mb-2 text-sm">Please fix the errors below:</p>
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <form method="POST"
                      action="{{ route('courses.store') }}"
                      enctype="multipart/form-data"
                      class="space-y-5"
                      x-data="{ preview: null }">
                    @csrf

                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Course Title</label>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="e.g. Data Analysis"
                            required
                        >
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <textarea
                            name="description"
                            rows="5"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Course description..."
                        >{{ old('description') }}</textarea>
                    </div>

                    {{-- Price --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Course Price (₦)</label>
                        <input
                            type="number"
                            name="price"
                            min="0"
                            value="{{ old('price', 0) }}"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="e.g. 5000"
                        >
                        <p class="text-xs text-slate-500 mt-1">Enter 0 for a free course.</p>
                    </div>

                    {{-- Thumbnail --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Thumbnail (optional)</label>

                        <label class="flex flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-center cursor-pointer hover:bg-slate-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            <span class="text-sm text-slate-500">Click to upload an image</span>
                            <input
                                type="file"
                                name="thumbnail"
                                accept="image/*"
                                class="hidden"
                                @change="preview = URL.createObjectURL($event.target.files[0])"
                            >
                        </label>

                        <p class="text-xs text-slate-500">
                            Max 2MB • JPG / PNG / WEBP
                        </p>

                        {{-- Preview --}}
                        <template x-if="preview">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <p class="text-xs text-slate-500 mb-2">Preview:</p>
                                <img :src="preview" class="w-full max-h-64 object-cover rounded-lg" alt="Thumbnail preview">
                            </div>
                        </template>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition">
                            Create Course
                        </button>

                        <a href="{{ route('courses.index') }}"
                           class="inline-flex items-center rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>