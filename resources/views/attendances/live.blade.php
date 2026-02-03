<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Live Session Attendance
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <p class="mb-4 font-semibold">
                    Course: {{ $course->title }}
                </p>

                @if($attendances->isEmpty())
                    <p>No live attendance has been recorded yet.</p>
                @else
                    <table class="min-w-full border">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-2">Student</th>
                                <th class="text-left p-2">Email</th>
                                <th class="text-left p-2">Status</th>
                                <th class="text-left p-2">Marked At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                                <tr class="border-b">
                                    <td class="p-2">{{ $attendance->user->name ?? 'N/A' }}</td>
                                    <td class="p-2">{{ $attendance->user->email ?? 'N/A' }}</td>
                                    <td class="p-2">{{ $attendance->status }}</td>
                                    <td class="p-2">{{ optional($attendance->marked_at)->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
