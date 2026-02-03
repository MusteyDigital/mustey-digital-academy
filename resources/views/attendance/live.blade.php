<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Live Session Attendance — {{ $course->title }}
            </h2>

            <a class="underline text-gray-600" href="{{ route('courses.show', $course->id) }}">
                ← Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <h3 class="font-semibold text-gray-800">Live Attendance List</h3>

                @if($attendances->isEmpty())
                    <p class="text-gray-600">No live attendance records yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead class="bg-gray-50">
                                <tr class="text-left">
                                    <th class="border px-3 py-2">#</th>
                                    <th class="border px-3 py-2">Student</th>
                                    <th class="border px-3 py-2">Status</th>
                                    <th class="border px-3 py-2">Marked At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendances as $i => $a)
                                    <tr>
                                        <td class="border px-3 py-2">{{ $i + 1 }}</td>
                                        <td class="border px-3 py-2">{{ $a->user->name ?? 'Unknown' }}</td>
                                        <td class="border px-3 py-2">{{ $a->status }}</td>
                                        <td class="border px-3 py-2">
                                            {{ $a->marked_at ? \Illuminate\Support\Carbon::parse($a->marked_at)->format('D, M j, Y g:i A') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
