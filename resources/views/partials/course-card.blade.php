@php
    $thumb = $course->thumbnail ?? null;
    $thumbUrl = $thumb ? (str_starts_with($thumb, 'http') ? $thumb : asset('storage/'.$thumb)) : null;
@endphp

<div class="rounded-2xl border bg-white overflow-hidden hover:shadow-sm transition min-w-0">
    <div class="aspect-[16/9] bg-gray-100">
        @if($thumbUrl)
            <img src="{{ $thumbUrl }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
        @else
            <div class="h-full w-full flex items-center justify-center text-gray-400 text-sm">
                No thumbnail
            </div>
        @endif
    </div>

    <div class="p-6">
        <div class="flex flex-wrap gap-2 text-xs text-gray-600">
            @if($course->category)
                <span class="rounded-full bg-gray-100 px-2 py-1">{{ $course->category }}</span>
            @endif
            @if($course->level)
                <span class="rounded-full bg-gray-100 px-2 py-1">{{ $course->level }}</span>
            @endif
            @if($course->duration)
                <span class="rounded-full bg-gray-100 px-2 py-1">{{ $course->duration }}</span>
            @endif
            @if($course->is_featured)
                <span class="rounded-full bg-gray-900 text-white px-2 py-1">Featured</span>
            @endif
        </div>

        <div class="mt-3 text-lg font-semibold break-words">{{ $course->title }}</div>

        <p class="mt-2 text-sm text-gray-600 line-clamp-3">
            {{ $course->description ?? 'No description yet.' }}
        </p>

        <div class="mt-5 flex items-center justify-between gap-3">
            <a href="{{ url('/courses/'.$course->id) }}"
               class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                View
            </a>

            @auth
                @if(auth()->user()->role === 'student')
                    <form method="POST" action="{{ route('courses.enroll', $course->id) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Enroll
                        </button>
                    </form>
                @endif
            @endauth
        </div>
    </div>
</div>
