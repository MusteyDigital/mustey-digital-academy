<x-layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                Admin — Certificates
            </h2>

            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Admin Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Search --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[260px]">
                        <label class="text-sm font-medium text-slate-600 mb-1 block">Search</label>
                        <input name="q"
                               value="{{ $q }}"
                               class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="Serial, student, email, course, instructor">
                    </div>

                    <button class="inline-flex items-center gap-2 rounded-xl bg-slate-900 text-white px-5 py-2.5 text-sm font-semibold hover:bg-slate-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                        </svg>
                        Filter
                    </button>

                    <a href="{{ route('admin.certificates.index') }}"
                       class="inline-flex items-center px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        Reset
                    </a>
                </form>

                @if($q !== '')
                    <p class="text-sm text-slate-500 mt-4">
                        Showing results for: <span class="font-semibold text-slate-700">{{ $q }}</span>
                        · Found: <span class="font-semibold text-slate-700">{{ $certificates->total() }}</span>
                    </p>
                @endif
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">Serial</th>
                                <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">Student</th>
                                <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">Course</th>
                                <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">Instructor</th>
                                <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">Issued</th>
                                <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($certificates as $c)
                                <tr class="border-b border-slate-100 last:border-b-0 hover:bg-slate-50 transition">
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-slate-800">
                                            {{ $c->serial }}
                                        </div>
                                        <div class="text-xs text-slate-400">
                                            ID: {{ $c->id }}
                                        </div>
                                    </td>

                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-slate-800">
                                            {{ optional($c->user)->name ?? '—' }}
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            {{ optional($c->user)->email ?? '' }}
                                        </div>
                                    </td>

                                    <td class="py-3 px-4">
                                        @if($c->course)
                                            <a class="text-blue-600 font-medium hover:text-blue-700 transition" href="{{ route('courses.show', $c->course->id) }}">
                                                {{ $c->course->title }}
                                            </a>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>

                                    <td class="py-3 px-4 text-slate-700">
                                        {{ optional(optional($c->course)->instructor)->name ?? '—' }}
                                    </td>

                                    <td class="py-3 px-4 text-slate-500">
                                        {{ $c->issued_at ? $c->issued_at->format('M j, Y') : '—' }}
                                    </td>

                                    <td class="py-3 px-4">
                                        <a class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 transition"
                                           href="{{ route('certificates.verify', $c->serial) }}"
                                           target="_blank" rel="noopener">
                                            <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Verify Page
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 px-4 text-slate-500 text-center">
                                        No certificates found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $certificates->links() }}
                </div>
            </div>

        </div>
    </div>
</x-layouts.admin>