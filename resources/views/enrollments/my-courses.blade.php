<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Courses
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if($courses->isEmpty())
                    <p>You have not enrolled in any course yet.</p>
                @else
                    <ul class="space-y-3">
                        @foreach($courses as $course)
                            <li class="border rounded p-4">
                                <a class="underline font-semibold" href="{{ route('courses.show', $course->id) }}">
                                    {{ $course->title }}
                                </a>
                                <div class="text-sm text-gray-600">
                                    Status: {{ $course->pivot->status ?? 'enrolled' }}
                                </div>
<form method="POST" action="{{ route('courses.unenroll', $course->id) }}" class="mt-2">
    @csrf
    @method('DELETE')
    <x-danger-button>Unenroll</x-danger-button>
</form>

                            </li>
                        @endforeach
                    </ul>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
