<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    Add Question — {{ $quiz->title }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Course: <span class="font-semibold">{{ $course->title }}</span>
                </p>
            </div>

            <a href="{{ route('quizzes.show', [$course->id, $quiz->id]) }}"
               class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Quiz
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

                {{-- Validation summary --}}
                @if ($errors->any())
                    <div class="mb-4 rounded-xl bg-red-50 p-4 text-red-800 border border-red-200">
                        <div class="font-semibold mb-1">Please fix the errors below.</div>
                        <ul class="list-disc pl-5 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('quiz-questions.store', [$course->id, $quiz->id]) }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="question" value="Question" />
                        <textarea id="question"
                                  class="block mt-1 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm p-2.5"
                                  name="question" required>{{ old('question') }}</textarea>
                        <x-input-error :messages="$errors->get('question')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="option_a" value="Option A" />
                            <x-text-input id="option_a" class="block mt-1 w-full" type="text"
                                          name="option_a" value="{{ old('option_a') }}" required />
                        </div>

                        <div>
                            <x-input-label for="option_b" value="Option B" />
                            <x-text-input id="option_b" class="block mt-1 w-full" type="text"
                                          name="option_b" value="{{ old('option_b') }}" required />
                        </div>

                        <div>
                            <x-input-label for="option_c" value="Option C" />
                            <x-text-input id="option_c" class="block mt-1 w-full" type="text"
                                          name="option_c" value="{{ old('option_c') }}" required />
                        </div>

                        <div>
                            <x-input-label for="option_d" value="Option D" />
                            <x-text-input id="option_d" class="block mt-1 w-full" type="text"
                                          name="option_d" value="{{ old('option_d') }}" required />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="correct_option" value="Correct Option (a, b, c, d)" />
                        <select id="correct_option"
                                class="rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm w-full"
                                name="correct_option" required>
                            <option value="a" @selected(old('correct_option') === 'a')>A</option>
                            <option value="b" @selected(old('correct_option') === 'b')>B</option>
                            <option value="c" @selected(old('correct_option') === 'c')>C</option>
                            <option value="d" @selected(old('correct_option') === 'd')>D</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <x-primary-button>Save Question</x-primary-button>

                        <a href="{{ route('quizzes.show', [$course->id, $quiz->id]) }}"
                           class="text-sm text-slate-500 hover:text-slate-700 hover:underline transition">
                            Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>