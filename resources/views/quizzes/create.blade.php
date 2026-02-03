<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Quiz — {{ $course->title }}
            </h2>

            <a href="{{ route('courses.show', $course->id) }}" class="underline text-gray-600">
                ← Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">

                @if($errors->any())
                    <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('quizzes.store', $course->id) }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label value="Quiz Title" />
                        <x-text-input class="block mt-1 w-full" type="text" name="title" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div class="pt-2">
                        <x-primary-button>Create Quiz</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
