<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📚 Courses
            </h2>

            @auth
                @if(in_array(auth()->user()->role, ['instructor','admin']))
                    <a href="{{ route('courses.create') }}"
                       class="inline-flex items-center gap-2 rounded-md bg-gray-900 text-white px-4 py-2 text-sm hover:bg-black">
                        ➕ Create Course
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if($courses->isEmpty())
                <div class="bg-white border rounded-lg p-8 text-center text-gray-600">
                    <p class="font-semibold text-gray-900">No courses yet.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($courses as $course)
                        <div class="bg-white border rounded-lg overflow-hidden hover:shadow-md transition">

                            {{-- ✅ Thumbnail --}}
                            @if(!empty($course->thumbnail))
                                <a href="{{ route('courses.show', $course->id) }}" class="block">
                                <img
                                    src="{{ asset('storage/'.$course->thumbnail) }}"
                                    alt="{{ $course->title }} thumbnail"
                                    class="w-full max-h-64 object-cover rounded-t-lg"
                                    style="height: 180px;"
                                    loading="lazy"
                                >

                                </a>
                            @else
                                {{-- Optional placeholder (remove if you don’t want it) --}}
                                <div class="w-full h-40 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                                    No thumbnail
                                </div>
                            @endif

                            <div class="p-5 space-y-3">
                                <div>
                                    <a href="{{ route('courses.show', $course->id) }}"
                                       class="text-lg font-semibold text-gray-900 underline">
                                        {{ $course->title }}
                                    </a>

                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $course->description ?? 'No description yet.' }}
                                    </p>
                                </div>

                                <div class="text-sm text-gray-700">
                                    Instructor:
                                    <span class="font-semibold">
                                        {{ optional($course->instructor)->name ?? '—' }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between pt-2 flex-wrap gap-2">
                                    <a href="{{ route('courses.show', $course->id) }}"
                                       class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-gray-50">
                                        Open →
                                    </a>

                                    @auth
                                        @php
                                            $canManage =
                                                auth()->user()->role === 'admin' ||
                                                (auth()->user()->role === 'instructor' && $course->instructor_id === auth()->id());
                                        @endphp

                                        @if($canManage)
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('courses.edit', $course->id) }}"
                                                   class="bg-blue-600 text-white px-3 py-2 rounded text-sm">
                                                    Edit
                                                </a>

                                                <form method="POST" action="{{ route('courses.destroy', $course->id) }}"
                                                      onsubmit="return confirm('Delete this course?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="bg-red-600 text-white px-3 py-2 rounded text-sm">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth
                                </div>

                            </div>

                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
