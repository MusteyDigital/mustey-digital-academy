<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Courses</h1>
                    <p class="text-sm text-slate-500 mt-1">Browse all available courses.</p>
                </div>

                @auth
                    @if(in_array(auth()->user()->role, ['instructor','admin']))
                        <a href="{{ route('courses.create') }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2.5 text-sm font-medium hover:bg-blue-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Create Course
                        </a>
                    @endif
                @endauth
            </div>

            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if($courses->isEmpty())
                <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-10 text-center">
                    <p class="font-semibold text-slate-700">No courses yet.</p>
                    <p class="text-sm text-slate-500 mt-1">Check back soon.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($courses as $course)
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition">

                            @if(!empty($course->thumbnail))
                                <a href="{{ route('courses.show', $course->id) }}" class="block">
                                    <img
                                        src="{{ asset('storage/'.$course->thumbnail) }}"
                                        alt="{{ $course->title }} thumbnail"
                                        class="w-full object-cover"
                                        style="height: 180px;"
                                        loading="lazy"
                                    >
                                </a>
                            @else
                                <a href="{{ route('courses.show', $course->id) }}"
                                   class="w-full h-[180px] bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </a>
                            @endif

                            <div class="p-5 space-y-3">
                                <div>
                                    <a href="{{ route('courses.show', $course->id) }}"
                                       class="text-lg font-semibold text-slate-800 hover:text-blue-600 transition">
                                        {{ $course->title }}
                                    </a>

                                    <p class="text-sm text-slate-500 mt-1 line-clamp-2">
                                        {{ $course->description ?? 'No description yet.' }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-1.5 text-sm text-slate-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="font-medium text-slate-700">
                                        {{ optional($course->instructor)->name ?? '—' }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between pt-2 flex-wrap gap-2">
                                    <a href="{{ route('courses.show', $course->id) }}"
                                       class="inline-flex items-center gap-1 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                                        Open
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
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
                                                   class="inline-flex items-center bg-blue-600 text-white px-3 py-2 rounded-xl text-sm font-medium hover:bg-blue-700 transition">
                                                    Edit
                                                </a>

                                                <form method="POST" action="{{ route('courses.destroy', $course->id) }}"
                                                      onsubmit="return confirm('Delete this course?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center bg-red-600 text-white px-3 py-2 rounded-xl text-sm font-medium hover:bg-red-700 transition">
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