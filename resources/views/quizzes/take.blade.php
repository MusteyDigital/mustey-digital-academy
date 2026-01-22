<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Take Quiz: {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @if($quiz->questions->isEmpty())
                    <p>No questions yet.</p>
                @else
                    <form method="POST" action="{{ route('quizzes.submit', [$course->id, $quiz->id]) }}">
                        @csrf

                        <ol class="list-decimal pl-6 space-y-6">
                            @foreach($quiz->questions as $q)
                                <li>
                                    <div class="font-semibold mb-2">{{ $q->question }}</div>

                                    @foreach(['a','b','c','d'] as $opt)
                                        @php $label = 'option_'.$opt; @endphp
                                        <label class="block">
                                            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt }}" required>
                                            {{ strtoupper($opt) }}: {{ $q->$label }}
                                        </label>
                                    @endforeach
                                </li>
                            @endforeach
                        </ol>

                        <div class="mt-6">
                            <x-primary-button>Submit Quiz</x-primary-button>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
