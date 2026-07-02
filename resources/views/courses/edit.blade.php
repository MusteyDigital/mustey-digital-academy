<x-app-layout>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between flex-wrap gap-2">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Edit Course</h1>
                    <p class="text-sm text-slate-500 mt-1">{{ $course->title }}</p>
                </div>

                <a href="{{ route('courses.show', $course->id) }}"
                   class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Course
                </a>
            </div>

            {{-- Flash --}}
            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Errors --}}
            @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                    <ul class="list-disc pl-5 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                {{-- Current thumbnail preview --}}
                @if(!empty($course->thumbnail))
                    <div>
                        <p class="text-sm font-medium text-slate-700 mb-2">Current Thumbnail</p>
                        <img
                            src="{{ asset('storage/' . $course->thumbnail) }}"
                            alt="Course thumbnail"
                            class="w-full max-w-md rounded-xl border border-slate-200"
                        >
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('courses.update', $course->id) }}"
                      enctype="multipart/form-data"
                      class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Course Title</label>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title', $course->title) }}"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <textarea
                            name="description"
                            rows="5"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Course description..."
                        >{{ old('description', $course->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Course Price (₦)</label>
                        <input
                            type="number"
                            name="price"
                            min="0"
                            value="{{ old('price', $course->price ?? 0) }}"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="e.g. 5000"
                        >
                        <p class="text-xs text-slate-500 mt-1">Enter 0 for a free course.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Change Thumbnail (optional)</label>
                        <label class="flex flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-center cursor-pointer hover:bg-slate-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            <span class="text-sm text-slate-500">Click to upload a new image</span>
                            <input
                                type="file"
                                name="thumbnail"
                                accept="image/*"
                                class="hidden"
                            >
                        </label>
                        <p class="text-xs text-slate-500 mt-1">Max 2MB • JPG/PNG/WebP</p>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition">
                            Save Changes
                        </button>

                        <a href="{{ route('courses.show', $course->id) }}"
                           class="inline-flex items-center rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <p class="text-sm text-slate-700">
                    <span class="font-semibold">Note:</span>
                    Live session settings (meeting URL + start time) are edited on the session page.
                </p>

                <div class="mt-3">
                    <a class="inline-flex items-center gap-2 text-blue-600 hover:underline text-sm font-medium"
                       href="{{ route('courses.session.edit', $course->id) }}">
                        Edit Live Session Settings
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>