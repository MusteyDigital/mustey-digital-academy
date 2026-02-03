<x-layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin — Certificates
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
                    <div class="flex-1 min-w-[260px]">
                        <label class="text-sm text-gray-600">Search</label>
                        <input name="q"
                               value="{{ $q }}"
                               class="w-full border rounded p-2"
                               placeholder="Serial, student, email, course, instructor">
                    </div>

                    <button class="rounded bg-gray-900 text-white px-4 py-2 text-sm font-semibold">
                        Filter
                    </button>

                    <a href="{{ route('admin.certificates.index') }}"
                       class="rounded border px-4 py-2 text-sm hover:bg-gray-50">
                        Reset
                    </a>
                </form>

                @if($q !== '')
                    <p class="text-sm text-gray-600 mt-3">
                        Showing results for: <span class="font-semibold">{{ $q }}</span>
                        • Found: <span class="font-semibold">{{ $certificates->total() }}</span>
                    </p>
                @endif
            </div>

            {{-- Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="text-left p-3">Serial</th>
                                <th class="text-left p-3">Student</th>
                                <th class="text-left p-3">Course</th>
                                <th class="text-left p-3">Instructor</th>
                                <th class="text-left p-3">Issued</th>
                                <th class="text-left p-3">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($certificates as $c)
                                <tr class="border-b">
                                    <td class="p-3">
                                        <div class="font-semibold text-gray-900">
                                            {{ $c->serial }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            ID: {{ $c->id }}
                                        </div>
                                    </td>

                                    <td class="p-3">
                                        <div class="font-semibold text-gray-900">
                                            {{ optional($c->user)->name ?? '—' }}
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            {{ optional($c->user)->email ?? '' }}
                                        </div>
                                    </td>

                                    <td class="p-3">
                                        @if($c->course)
                                            <a class="underline text-blue-600" href="{{ route('courses.show', $c->course->id) }}">
                                                {{ $c->course->title }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <td class="p-3 text-gray-700">
                                        {{ optional(optional($c->course)->instructor)->name ?? '—' }}
                                    </td>

                                    <td class="p-3 text-gray-700">
                                        {{ optional($c->issued_at)->format('M j, Y') ?? '—' }}
                                    </td>

                                    <td class="p-3">
                                        <a class="inline-flex items-center rounded-md border px-3 py-2 text-xs hover:bg-gray-50"
                                           href="{{ route('certificates.verify', $c->serial) }}"
                                           target="_blank">
                                            ✅ Verify Page
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-gray-600">
                                        No certificates found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $certificates->links() }}
                </div>
            </div>

        </div>
    </div>
</x-layouts.admin>
