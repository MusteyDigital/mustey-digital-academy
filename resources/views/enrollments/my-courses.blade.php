<x-app-layout>
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">My Courses</h1>
            <p class="text-sm text-slate-500 mt-1">All courses you are currently enrolled in.</p>
        </div>
        <a href="{{ route('progress.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            My Progress
        </a>
    </div>

    {{-- Empty State --}}
    @if($courses->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-16 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-700 mb-2">No courses yet</h3>
            <p class="text-slate-500 text-sm mb-6">You haven't enrolled in any courses. Browse our catalog to get started.</p>
            <a href="{{ route('courses.index') }}"
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition">
                Browse Courses
            </a>
        </div>

    @else
        <div class="grid md:grid-cols-2 gap-5">
            @foreach($courses as $course)
                @php
                    $lessonIds = $course->lessons()->pluck('id')->toArray();
                    $lessonCount = count($lessonIds);
                    $completedCount = $lessonCount > 0
                        ? \App\Models\LessonCompletion::where('user_id', auth()->id())->whereIn('lesson_id', $lessonIds)->count()
                        : 0;
                    $percent = $lessonCount > 0 ? (int) round(($completedCount / $lessonCount) * 100) : 0;
                    $nextLesson = $lessonCount > 0
                        ? $course->lessons()->orderBy('order')->orderBy('id')->whereNotIn('id',
                            \App\Models\LessonCompletion::where('user_id', auth()->id())->pluck('lesson_id')->toArray()
                          )->first()
                        : null;
                @endphp

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">

                    {{-- Thumbnail --}}
                    @if(!empty($course->thumbnail))
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-40 object-cover">
                    @else
                        <div class="w-full h-40 bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                            <svg class="w-10 h-10 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                    @endif

                    <div class="p-5 flex-1 flex flex-col space-y-4">
                        {{-- Title & Instructor --}}
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg leading-tight">{{ $course->title }}</h3>
                            <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ $course->description }}</p>
                            <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $course->instructor->name ?? '—' }}
                            </p>
                        </div>

                        {{-- Progress --}}
                        <div>
                            <div class="flex items-center justify-between text-xs text-slate-500 mb-1.5">
                                <span>{{ $completedCount }}/{{ $lessonCount }} lessons</span>
                                <span class="font-semibold text-slate-700">{{ $percent }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full {{ $percent === 100 ? 'bg-green-500' : 'bg-blue-600' }}" style="width: {{ $percent }}%;"></div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-wrap gap-2 mt-auto pt-2">
                            @if($nextLesson)
                                <a href="{{ route('lessons.show', [$course->id, $nextLesson->id]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-xl font-medium hover:bg-blue-700 transition">
                                    Continue →
                                </a>
                            @else
                                <a href="{{ route('courses.show', $course->id) }}"
                                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-xl font-medium hover:bg-green-700 transition">
                                    ✅ Review
                                </a>
                            @endif

                            <a href="{{ route('progress.show', $course->id) }}"
                               class="inline-flex items-center px-4 py-2 border border-slate-200 text-slate-700 text-sm rounded-xl hover:bg-slate-50 transition">
                                Progress
                            </a>

                            <a href="{{ route('courses.show', $course->id) }}"
                               class="inline-flex items-center px-4 py-2 border border-slate-200 text-slate-700 text-sm rounded-xl hover:bg-slate-50 transition">
                                Open
                            </a>

                            @if($course->meeting_url)
                                <a href="{{ $course->meeting_url }}" target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm rounded-xl font-medium hover:bg-indigo-700 transition">
                                    Join Live
                                </a>
                            @endif
                        </div>

                        {{-- Unenroll --}}
                        <form method="POST" action="{{ route('courses.unenroll', $course->id) }}"
                              onsubmit="return confirm('Are you sure you want to unenroll from this course?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition">
                                Unenroll from this course
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
</x-app-layout>
