<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Quiz — {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('quizzes.store', $course->id) }}">
                    @csrf

                    <div>
                        <x-input-label value="Quiz Title" />
                        <x-text-input class="block mt-1 w-full" type="text" name="title" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div class="mt-6">
                        <x-primary-button>Create Quiz</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
