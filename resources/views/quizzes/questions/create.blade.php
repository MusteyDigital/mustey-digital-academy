<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Question — {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('quiz-questions.store', [$course->id, $quiz->id]) }}">
                    @csrf

                    <div>
                        <x-input-label value="Question" />
                        <textarea class="block mt-1 w-full border rounded p-2" name="question" required></textarea>
                        <x-input-error :messages="$errors->get('question')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label value="Option A" />
                        <x-text-input class="block mt-1 w-full" type="text" name="option_a" required />
                    </div>

                    <div class="mt-4">
                        <x-input-label value="Option B" />
                        <x-text-input class="block mt-1 w-full" type="text" name="option_b" required />
                    </div>

                    <div class="mt-4">
                        <x-input-label value="Option C" />
                        <x-text-input class="block mt-1 w-full" type="text" name="option_c" required />
                    </div>

                    <div class="mt-4">
                        <x-input-label value="Option D" />
                        <x-text-input class="block mt-1 w-full" type="text" name="option_d" required />
                    </div>

                    <div class="mt-4">
                        <x-input-label value="Correct Option (a, b, c, d)" />
                        <select class="border rounded p-2 w-full" name="correct_option" required>
                            <option value="a">A</option>
                            <option value="b">B</option>
                            <option value="c">C</option>
                            <option value="d">D</option>
                        </select>
                    </div>

                    <div class="mt-6">
                        <x-primary-button>Save Question</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
