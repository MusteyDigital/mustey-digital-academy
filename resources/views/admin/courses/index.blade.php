<x-layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                Admin — Courses
            </h2>

            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Admin Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Search --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[240px]">
                        <label class="text-sm font-medium text-slate-600 mb-1 block">Search</label>
                        <input
                            name="q"
                            value="{{ old('q', request('q', '')) }}"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Search title or description"
                        >
                    </div>

                    <button class="inline-flex items-center gap-2 rounded-xl bg-slate-900 text-white px-5 py-2.5 text-sm font-semibold hover:bg-slate-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                        </svg>
                        Filter
                    </button>

                    <a href="{{ route('admin.courses.index') }}"
                       class="inline-flex items-center px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        Reset
                    </a>
                </form>

                @if(request('q', '') !== '')
                    <p class="text-sm text-slate-500 mt-4">
                        Showing results for:
                        <span class="font-semibold text-slate-700">{{ request('q') }}</span>
                        · Found:
                        <span class="font-semibold text-slate-700">
                            {{ method_exists($courses, 'total') ? $courses->total() : $courses->count() }}
                        </span>
                    </p>
                @endif
            </div>

            {{-- Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($courses as $course)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3 hover:shadow-md transition">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-lg font-semibold text-slate-800">
                                {{ $course->title }}
                            </p>
                            <span class="text-xs text-slate-400 whitespace-nowrap shrink-0">
                                ID: {{ $course->id }}
                            </span>
                        </div>

                        <p class="text-sm text-slate-600">
                            Instructor:
                            <span class="font-semibold text-slate-800">
                                {{ optional($course->instructor)->name ?? '—' }}
                            </span>
                        </p>

                        <p class="text-sm text-slate-500 leading-relaxed">
                            {{ \Illuminate\Support\Str::limit($course->description ?? '', 140) }}
                        </p>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-xs text-slate-400">
                                Created: {{ $course->created_at ? $course->created_at->format('M j, Y') : '—' }}
                            </span>

                            <a class="inline-flex items-center gap-1 text-blue-600 text-sm font-medium hover:text-blue-700 transition"
                               href="{{ route('courses.show', $course->id) }}">
                                Open Course
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        </div>

                        {{-- Admin actions --}}
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <span class="text-xs text-slate-400">
                                Manage
                            </span>

                            <form method="POST"
                                  action="{{ route('admin.courses.destroy', $course->id) }}"
                                  onsubmit="return confirm('Delete this course? This cannot be undone.');">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-sm text-red-700 hover:bg-red-100 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center text-slate-500">
                        No courses found.
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if(method_exists($courses, 'links'))
                <div class="bg-white rounded-2xl border border-slate-200 p-4">
                    {{ $courses->links() }}
                </div>
            @endif

        </div>
    </div>
</x-layouts.admin>