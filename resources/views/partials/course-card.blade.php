@php
    $thumb = $course->thumbnail ?? null;
    $thumbUrl = $thumb ? (str_starts_with($thumb, 'http') ? $thumb : asset('storage/'.$thumb)) : null;
@endphp
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition min-w-0">
    <div class="aspect-[16/9] bg-slate-100 relative">
        @if($thumbUrl)
            <img src="{{ $thumbUrl }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
        @else
            <div class="h-full w-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
        @endif

        @if($course->is_featured)
            <span class="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full bg-white/95 text-slate-800 px-2.5 py-1 text-xs font-semibold shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.364 1.118l1.287 3.957c.3.922-.755 1.688-1.539 1.118l-3.367-2.446a1 1 0 00-1.175 0l-3.367 2.446c-.784.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.063 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.285-3.958z"/>
                </svg>
                Featured
            </span>
        @endif
    </div>

    <div class="p-6">
        <div class="flex flex-wrap gap-2 text-xs">
            @if($course->category)
                <span class="rounded-full bg-blue-50 text-blue-700 px-2.5 py-1 font-medium">{{ $course->category }}</span>
            @endif
            @if($course->level)
                <span class="rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 font-medium">{{ $course->level }}</span>
            @endif
            @if($course->duration)
                <span class="rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 font-medium">{{ $course->duration }}</span>
            @endif
        </div>

        <div class="mt-3 text-lg font-semibold text-slate-800 break-words">{{ $course->title }}</div>
        <p class="mt-2 text-sm text-slate-500 line-clamp-3">
            {{ $course->description ?? 'No description yet.' }}
        </p>

        <div class="mt-5 flex items-center justify-between gap-3">
            <a href="{{ url('/courses/'.$course->id) }}"
               class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                View
            </a>

            @auth
                @if(auth()->user()->role === 'student')
                    <form method="POST" action="{{ route('courses.enroll', $course->id) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                            Enroll
                        </button>
                    </form>
                @endif
            @endauth
        </div>
    </div>
</div>