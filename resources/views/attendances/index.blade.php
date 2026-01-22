<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Attendance — {{ $course->title }} | {{ $lesson->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">

                <a class="underline" href="{{ route('lessons.show', [$course->id, $lesson->id]) }}">
                    ← Back to Lesson
                </a>

                @if($attendances->isEmpty())
                    <p>No attendance marked yet.</p>
                @else
                    <table class="w-full border">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-2">Student</th>
                                <th class="text-left p-2">Email</th>
                                <th class="text-left p-2">Status</th>
                                <th class="text-left p-2">Marked At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $a)
                                <tr class="border-b">
                                    <td class="p-2">{{ $a->user->name }}</td>
                                    <td class="p-2">{{ $a->user->email }}</td>
                                    <td class="p-2">{{ $a->status }}</td>
                                    <td class="p-2">{{ optional($a->marked_at)->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
