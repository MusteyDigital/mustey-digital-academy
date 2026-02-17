<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🎓 Student Dashboard
            </h2>

            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('enrollments.my-courses') }}" class="underline text-gray-700">
                    My Courses
                </a>

                <a class="inline-flex items-center gap-2 rounded-md border px-3 py-2 text-sm hover:bg-gray-50"
                   href="{{ route('progress.index') }}">
                    📈 <span>My Progress</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800">
                    Welcome, {{ auth()->user()->name }} 👋
                </h3>
                <p class="text-gray-600 mt-1">
                    Here are the courses you are enrolled in.
                </p>
            </div>

            {{-- Courses List --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h3 class="font-semibold text-gray-800 text-lg">
                        📚 Your Courses
                    </h3>

                    <a href="{{ route('courses.index') }}" class="underline text-blue-600 text-sm">
                        Browse Courses →
                    </a>
                </div>

                @if($courses->isEmpty())
                    <div class="mt-4 rounded-lg border border-dashed p-6 bg-gray-50 text-gray-600 text-center">
                        <p class="font-semibold">No courses yet</p>
                        <p class="text-sm mt-1">
                            You are not enrolled in any courses yet.
                        </p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mt-6">
                        @foreach($courses as $course)
                            <div class="border rounded-lg overflow-hidden hover:shadow-md transition bg-white">

                                {{-- ✅ Thumbnail --}}
                                @if(!empty($course->thumbnail))
                                    <div class="w-full">
                                        <img
                                            src="{{ asset('storage/'.$course->thumbnail) }}"
                                            alt="{{ $course->title }} thumbnail"
                                            class="w-full max-h-64 object-cover rounded-t-lg"
                                            style="height: 180px;"
                                            loading="lazy"
                                        >

                                    </div>
                                @endif

                                <div class="p-5 space-y-3">
                                    <div>
                                        <a class="text-lg font-semibold text-gray-900 underline"
                                           href="{{ route('courses.show', $course->id) }}">
                                            {{ $course->title }}
                                        </a>

                                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                            {{ $course->description ?? 'No description available.' }}
                                        </p>
                                    </div>

                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <span class="inline-flex items-center rounded-full bg-gray-100 text-gray-800 px-3 py-1 text-xs font-semibold">
                                            Status: {{ $course->pivot->status ?? 'enrolled' }}
                                        </span>

                                        <a href="{{ route('courses.show', $course->id) }}"
                                           class="inline-flex items-center rounded-md bg-gray-900 text-white px-4 py-2 text-sm hover:bg-black">
                                            Open
                                        </a>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
