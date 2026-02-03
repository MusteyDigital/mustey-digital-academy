<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Courses
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">

                <div class="flex items-center justify-between">
                    <p class="text-gray-600">All courses you are enrolled in.</p>
                    <a class="underline text-blue-600" href="{{ route('progress.index') }}">My Progress</a>
                </div>

                @if($courses->isEmpty())
                    <p class="text-gray-600">No enrolled courses yet.</p>
                    <a class="underline text-blue-600" href="{{ route('courses.index') }}">Browse Courses</a>
                @else
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($courses as $course)
                            <div class="border rounded-lg p-4 space-y-3">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="font-semibold text-lg">{{ $course->title }}</h3>
                                        <p class="text-sm text-gray-600">{{ $course->description }}</p>
                                        <p class="text-sm text-gray-700">
                                            <strong>Instructor:</strong> {{ $course->instructor->name ?? '—' }}
                                        </p>
                                    </div>
                                    <a class="underline text-blue-600 text-sm" href="{{ route('courses.show', $course->id) }}">
                                        Open
                                    </a>
                                </div>

                                <div class="flex gap-3 flex-wrap">
                                    <a class="underline text-blue-600"
                                       href="{{ route('progress.show', $course->id) }}">
                                        View Progress
                                    </a>

                                    @if($course->meeting_url)
                                        <a class="underline text-blue-600" href="{{ $course->meeting_url }}" target="_blank">
                                            Join Live Class
                                        </a>

                                        <form method="POST" action="{{ route('attendance.live.store', $course->id) }}">
                                            @csrf
                                            <x-primary-button>Mark Live Attendance</x-primary-button>
                                        </form>
                                    @endif
                                </div>

                                <form method="POST" action="{{ route('courses.unenroll', $course->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 underline text-sm">
                                        Unenroll
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
