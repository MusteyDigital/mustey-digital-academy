<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Manage Modules — {{ $course->title }}
            </h2>

            <a href="{{ route('instructor.dashboard') }}"
               class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Back to Instructor Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Create module --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                <h3 class="font-semibold text-slate-800 text-lg mb-3">Add Module</h3>

                <form method="POST" action="{{ route('instructor.modules.store', $course->id) }}" class="flex flex-wrap gap-2">
                    @csrf
                    <input name="title"
                           class="flex-1 min-w-[240px] rounded-xl border border-slate-200 p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Module title (e.g. Introduction)"
                           required>
                    <button class="inline-flex items-center rounded-xl bg-blue-600 text-white px-4 py-2 text-sm font-semibold shadow-sm hover:bg-blue-700 transition">
                        Add
                    </button>
                </form>
            </div>

            {{-- Modules list --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h3 class="font-semibold text-slate-800 text-lg">Modules (drag to reorder)</h3>
                    <span class="text-xs text-slate-500">Total: {{ $modules->count() }}</span>
                </div>

                @if($modules->isEmpty())
                    <div class="mt-4 rounded-xl border border-dashed border-slate-200 p-8 bg-slate-50 text-slate-500 text-center text-sm">
                        No modules yet.
                    </div>
                @else
                    <ul id="modulesList" class="mt-4 space-y-3">
                        @foreach($modules as $module)
                            <li class="module-item rounded-xl border border-slate-200 p-4 flex flex-wrap items-center justify-between gap-3 bg-white hover:border-blue-200 transition"
                                data-id="{{ $module->id }}">

                                <div class="flex items-center gap-3">
                                    <span class="cursor-move text-slate-400 text-xl">☰</span>
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $module->title }}</div>
                                        <div class="text-xs text-slate-500">Order: {{ $module->order }}</div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('instructor.modules.lessons.index', [$course->id, $module->id]) }}"
                                       class="inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 transition">
                                        Manage Lessons
                                    </a>

                                    <form method="POST" action="{{ route('instructor.modules.destroy', [$course->id, $module->id]) }}"
                                          onsubmit="return confirm('Delete this module? Lessons inside will lose module link.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 hover:bg-red-100 transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>

                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>

    {{-- Drag sort --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        const list = document.getElementById('modulesList');
        if (list) {
            new Sortable(list, {
                animation: 150,
                handle: '.cursor-move',
                onEnd: async () => {
                    const ids = [...document.querySelectorAll('.module-item')].map(li => li.dataset.id);

                    await fetch("{{ route('instructor.sort.modules', $course->id) }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ ids })
                    });
                }
            });
        }
    </script>
</x-app-layout>