<x-layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin — Enrollments
            </h2>

            <a href="{{ route('admin.dashboard') }}" class="underline text-gray-600">
                ← Back to Admin Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Search --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
                <form method="GET" class="flex flex-wrap gap-2 items-end">
                    <div class="flex-1 min-w-[240px]">
                        <label class="text-sm text-gray-600">Search</label>
                        <input name="q"
                               value="{{ $q }}"
                               class="w-full border rounded p-2"
                               placeholder="Student name/email or course title or instructor">
                    </div>

                    <button class="rounded bg-gray-900 text-white px-4 py-2 text-sm font-semibold">
                        Filter
                    </button>

                    <a href="{{ route('admin.enrollments.index') }}"
                       class="rounded border px-4 py-2 text-sm hover:bg-gray-50">
                        Reset
                    </a>
                </form>

                @if($q !== '')
                    <p class="text-sm text-gray-600 mt-3">
                        Showing results for: <span class="font-semibold">{{ $q }}</span>
                        • Found: <span class="font-semibold">{{ $enrollments->total() }}</span>
                    </p>
                @endif
            </div>

            {{-- Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="text-left p-3">Student</th>
                                <th class="text-left p-3">Course</th>
                                <th class="text-left p-3">Instructor</th>
                                <th class="text-left p-3">Status</th>
                                <th class="text-left p-3">Enrolled At</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($enrollments as $en)
                                @php
                                    $status = strtolower($en->status ?? '');
                                    $badge = 'bg-gray-100 text-gray-800';

                                    if ($status === 'active') $badge = 'bg-green-100 text-green-800';
                                    elseif ($status === 'completed') $badge = 'bg-blue-100 text-blue-800';
                                    elseif ($status === 'cancelled' || $status === 'canceled') $badge = 'bg-red-100 text-red-800';
                                    elseif ($status === 'pending') $badge = 'bg-yellow-100 text-yellow-800';
                                @endphp

                                <tr class="border-b">
                                    <td class="p-3">
                                        <div class="font-semibold text-gray-900">
                                            {{ optional($en->user)->name ?? '—' }}
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            {{ optional($en->user)->email ?? '' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            ID: {{ $en->user_id }}
                                        </div>
                                    </td>

                                    <td class="p-3">
                                        @if($en->course)
                                            <a class="underline text-blue-600" href="{{ route('courses.show', $en->course->id) }}">
                                                {{ $en->course->title }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                        <div class="text-xs text-gray-500 mt-1">Course ID: {{ $en->course_id }}</div>
                                    </td>

                                    <td class="p-3 text-gray-700">
                                        {{ optional(optional($en->course)->instructor)->name ?? '—' }}
                                    </td>

                                    <td class="p-3">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $badge }}">
                                            {{ $en->status ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="p-3 text-gray-700">
                                        {{ optional($en->created_at)->format('M j, Y • g:i A') ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-gray-600">
                                        No enrollments found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $enrollments->links() }}
                </div>
            </div>

        </div>
    </div>
</x-layouts.admin>
