<x-layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin — Courses
            </h2>

            <a href="{{ route('admin.dashboard') }}" class="underline text-gray-600">
                ← Back to Admin Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ✅ Flash success --}}
            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Search --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
                <form method="GET" class="flex flex-wrap gap-2 items-end">
                    <div class="flex-1 min-w-[240px]">
                        <label class="text-sm text-gray-600">Search</label>
                        <input
                            name="q"
                            value="{{ old('q', request('q', '')) }}"
                            class="w-full border rounded p-2"
                            placeholder="Search title or description"
                        >
                    </div>

                    <button class="rounded bg-gray-900 text-white px-4 py-2 text-sm font-semibold">
                        Filter
                    </button>

                    <a href="{{ route('admin.courses.index') }}"
                       class="rounded border px-4 py-2 text-sm hover:bg-gray-50">
                        Reset
                    </a>
                </form>

                @if(request('q', '') !== '')
                    <p class="text-sm text-gray-600 mt-3">
                        Showing results for:
                        <span class="font-semibold">{{ request('q') }}</span>
                        • Found:
                        <span class="font-semibold">
                            {{ method_exists($courses, 'total') ? $courses->total() : $courses->count() }}
                        </span>
                    </p>
                @endif
            </div>

            {{-- Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($courses as $course)
                    <div class="bg-white shadow-sm sm:rounded-lg border p-5 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $course->title }}
                            </p>
                            <span class="text-xs text-gray-500 whitespace-nowrap">
                                ID: {{ $course->id }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-700">
                            Instructor:
                            <span class="font-semibold">
                                {{ optional($course->instructor)->name ?? '—' }}
                            </span>
                        </p>

                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ \Illuminate\Support\Str::limit($course->description ?? '', 140) }}
                        </p>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-xs text-gray-500">
                                Created:
                                {{ optional($course->created_at)->format('M j, Y') ?? '—' }}
                            </span>

                            <a class="underline text-blue-600 text-sm"
                               href="{{ route('courses.show', $course->id) }}">
                                Open Course →
                            </a>
                        </div>

                        {{-- Admin actions --}}
                        <div class="pt-3 border-t flex items-center justify-between gap-2">
                            <span class="text-xs text-gray-500">
                                Manage:
                            </span>

                            <form method="POST"
                                  action="{{ route('admin.courses.destroy', $course->id) }}"
                                  onsubmit="return confirm('Delete this course? This cannot be undone.');">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 hover:bg-red-100">
                                    🗑 Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white border rounded-lg p-6 text-gray-600">
                        No courses found.
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if(method_exists($courses, 'links'))
                <div class="bg-white border rounded-lg p-4">
                    {{ $courses->links() }}
                </div>
            @endif

        </div>
    </div>
</x-layouts.admin>
