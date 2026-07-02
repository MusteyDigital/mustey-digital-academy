<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Live Session Attendance — {{ $course->title }}
            </h2>

            <a class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition"
               href="{{ route('courses.show', $course->id) }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-slate-800 text-lg">Live Attendance List</h3>

                @if($attendances->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-200 p-8 bg-slate-50 text-slate-500 text-center text-sm">
                        No live attendance records yet.
                    </div>
                @else
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full">
                            <thead class="bg-slate-50">
                                <tr class="text-left">
                                    <th class="px-4 py-3 text-sm font-semibold text-slate-700 border-b border-slate-200">#</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-slate-700 border-b border-slate-200">Student</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-slate-700 border-b border-slate-200">Status</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-slate-700 border-b border-slate-200">Marked At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendances as $i => $a)
                                    <tr class="border-b border-slate-100 last:border-b-0 hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 text-sm text-slate-500">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-900 font-medium">{{ $a->user->name ?? 'Unknown' }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium
                                                {{ strtolower($a->status) === 'present' ? 'bg-green-50 text-green-700' : (strtolower($a->status) === 'absent' ? 'bg-red-50 text-red-700' : 'bg-slate-100 text-slate-700') }}">
                                                {{ $a->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-500">
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