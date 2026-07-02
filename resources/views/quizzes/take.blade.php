<x-app-layout>
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $quiz->title }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $course->title }}</p>
        </div>
        <a href="{{ route('courses.show', $course->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition">
            ← Back to Course
        </a>
    </div>

    @if($quiz->questions->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-16 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-700 mb-2">No questions yet</h3>
            <p class="text-slate-500 text-sm">This quiz has no questions. Check back later.</p>
        </div>

    @else
        {{-- Quiz Info Banner --}}
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 flex flex-wrap gap-4">
            @if($quiz->questions_count ?? $quiz->questions->count())
                <div class="flex items-center gap-2 text-sm text-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><strong>{{ $quiz->questions->count() }}</strong> Questions</span>
                </div>
            @endif
            @if(!is_null($quiz->pass_mark))
                <div class="flex items-center gap-2 text-sm text-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Pass Mark: <strong>{{ $quiz->pass_mark }}%</strong></span>
                </div>
            @endif
            @if(!empty($quiz->time_limit_minutes))
                <div class="flex items-center gap-2 text-sm text-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Time Limit: <strong>{{ $quiz->time_limit_minutes }} min</strong></span>
                </div>
            @endif
            @if(!is_null($quiz->max_attempts))
                <div class="flex items-center gap-2 text-sm text-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Max Attempts: <strong>{{ $quiz->max_attempts }}</strong></span>
                </div>
            @endif
        </div>

        {{-- Quiz Form --}}
        <form method="POST" action="{{ route('quizzes.submit', [$course->id, $quiz->id]) }}" class="space-y-5">
            @csrf

            @foreach($quiz->questions as $index => $q)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="shrink-0 w-8 h-8 bg-blue-600 text-white rounded-xl flex items-center justify-center text-sm font-bold">
                            {{ $index + 1 }}
                        </span>
                        <p class="font-semibold text-slate-800 pt-1">{{ $q->question }}</p>
                    </div>

                    <div class="space-y-2 pl-11">
                        @foreach(['a','b','c','d'] as $opt)
                            @php $label = 'option_'.$opt; @endphp
                            @if(!empty($q->$label))
                                <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500">
                                    <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}" required
                                           class="mt-0.5 accent-blue-600">
                                    <span class="text-sm text-slate-700">
                                        <span class="font-semibold text-blue-600">{{ strtoupper($opt) }}.</span>
                                        {{ $q->$label }}
                                    </span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition shadow-sm">
                    Submit Quiz
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>
            </div>
        </form>
    @endif

</div>
</x-app-layout>
