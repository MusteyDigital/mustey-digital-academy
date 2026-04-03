<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📦 Manage Modules — {{ $course->title }}
            </h2>

            <a href="{{ route('instructor.dashboard') }}" class="underline text-gray-600">
                ← Back to Instructor Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Create module --}}
            <div class="bg-white border rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-3">➕ Add Module</h3>

                <form method="POST" action="{{ route('instructor.modules.store', $course->id) }}" class="flex flex-wrap gap-2">
                    @csrf
                    <input name="title"
                           class="flex-1 min-w-[240px] border rounded p-2"
                           placeholder="Module title (e.g. Introduction)"
                           required>
                    <button class="rounded bg-gray-900 text-white px-4 py-2 text-sm font-semibold">
                        Add
                    </button>
                </form>
            </div>

            {{-- Modules list --}}
            <div class="bg-white border rounded-lg p-6">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h3 class="font-semibold text-gray-800">Modules (drag to reorder)</h3>
                    <span class="text-xs text-gray-500">Total: {{ $modules->count() }}</span>
                </div>

                @if($modules->isEmpty())
                    <div class="mt-4 rounded border border-dashed p-6 bg-gray-50 text-gray-600 text-center">
                        No modules yet.
                    </div>
                @else
                    <ul id="modulesList" class="mt-4 space-y-3">
                        @foreach($modules as $module)
                            <li class="module-item border rounded-lg p-4 flex flex-wrap items-center justify-between gap-3 bg-white"
                                data-id="{{ $module->id }}">

                                <div class="flex items-center gap-3">
                                    <span class="cursor-move text-gray-400 text-xl">☰</span>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $module->title }}</div>
                                        <div class="text-xs text-gray-500">Order: {{ $module->order }}</div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('instructor.modules.lessons.index', [$course->id, $module->id]) }}"
                                       class="rounded border px-3 py-2 text-sm hover:bg-gray-50">
                                        Manage Lessons
                                    </a>

                                    <form method="POST" action="{{ route('instructor.modules.destroy', [$course->id, $module->id]) }}"
                                          onsubmit="return confirm('Delete this module? Lessons inside will lose module link.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 hover:bg-red-100">
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
