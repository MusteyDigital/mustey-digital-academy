<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Take Quiz: {{ $quiz->title }}
            </h2>

            <a href="{{ route('courses.show', $course->id) }}"
               class="text-sm text-gray-600 hover:text-gray-900 underline">
                ← Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">

                @if($quiz->questions->isEmpty())
                    <div class="rounded-md border border-dashed p-6 text-center text-gray-600">
                        <p class="font-semibold">No questions yet.</p>
                        <p class="text-sm mt-1">Please check back later.</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('quizzes.submit', [$course->id, $quiz->id]) }}" class="space-y-6">
                        @csrf

                        <ol class="space-y-6">
                            @foreach($quiz->questions as $index => $q)
                                <li class="rounded-lg border p-4">
                                    <div class="font-semibold text-gray-900">
                                        {{ $index + 1 }}. {{ $q->question }}
                                    </div>

                                    <div class="mt-3 space-y-2 text-sm text-gray-800">
                                        @foreach(['a','b','c','d'] as $opt)
                                            @php $label = 'option_'.$opt; @endphp
                                            <label class="flex items-start gap-2 cursor-pointer">
                                                <input class="mt-1" type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}" required>
                                                <span><span class="font-semibold">{{ strtoupper($opt) }}.</span> {{ $q->$label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </li>
                            @endforeach
                        </ol>

                        <div class="pt-2">
                            <x-primary-button>Submit Quiz</x-primary-button>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
