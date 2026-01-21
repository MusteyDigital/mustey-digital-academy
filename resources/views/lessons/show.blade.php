<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $course->title }} — {{ $lesson->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">

                <div class="text-gray-700 whitespace-pre-line">
                    {{ $lesson->content ?? 'No content yet.' }}
                </div>

                @if($lesson->video_url)
                    <div>
                        <a class="underline" href="{{ $lesson->video_url }}" target="_blank">
                            Watch Video
                        </a>
                    </div>
                @endif

                {{-- Student: Mark completed / Completed badge --}}
                @if(auth()->user()->role === 'student')
                    @if(!empty($isCompleted) && $isCompleted)
                        <div class="p-3 rounded bg-green-100 text-green-800 font-semibold">
                            ✅ Completed
                        </div>
                    @else
                        <form method="POST" action="{{ route('lessons.complete', [$course->id, $lesson->id]) }}">
                            @csrf
                            <x-primary-button>Mark as Completed</x-primary-button>
                        </form>
                    @endif
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
