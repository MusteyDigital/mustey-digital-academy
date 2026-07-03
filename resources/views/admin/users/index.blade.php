<x-layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                Admin — Users
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

            {{-- Flash --}}
            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Search + Export --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <form method="GET" class="flex flex-wrap gap-3 items-end">

                    <div class="flex-1 min-w-[220px]">
                        <label class="text-sm font-medium text-slate-600 mb-1 block">Search</label>
                        <input name="q" value="{{ $q }}"
                               class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                               placeholder="Name or email">
                    </div>

                    <button class="inline-flex items-center gap-2 rounded-xl bg-slate-900 text-white px-5 py-2.5 text-sm font-semibold hover:bg-slate-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                        </svg>
                        Filter
                    </button>

                    <a href="{{ route('admin.users.index') }}"
                       class="inline-flex items-center px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        Reset
                    </a>

                    {{-- Export CSV --}}
                    <a href="{{ route('admin.users.export', ['q' => $q]) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-green-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-green-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export CSV
                    </a>

                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">User</th>
                                <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">Email</th>
                                <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">Role</th>
                                <th class="text-left py-3 px-4 text-xs uppercase tracking-wide text-slate-500 font-medium">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($users as $u)
                                @php
                                    $role = $u->role ?? 'student';

                                    $badge = match($role) {
                                        'admin' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'instructor' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        default => 'bg-green-50 text-green-700 border-green-200',
                                    };

                                    $isMe = auth()->id() === $u->id;
                                @endphp

                                <tr class="border-b border-slate-100 last:border-b-0 hover:bg-slate-50 transition">
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-slate-800">{{ $u->name }}</div>
                                        <div class="text-xs text-slate-400">ID: {{ $u->id }}</div>

                                        @if($isMe)
                                            <div class="mt-1.5 inline-flex text-xs rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 border border-slate-200 font-medium">
                                                This is you
                                            </div>
                                        @endif
                                    </td>

                                    <td class="py-3 px-4 text-slate-700">
                                        {{ $u->email }}
                                    </td>

                                    <td class="py-3 px-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold border {{ $badge }}">
                                            {{ $role }}
                                        </span>
                                    </td>

                                    <td class="py-3 px-4">
                                        <form
                                            method="POST"
                                            action="{{ route('admin.users.role', $u) }}"
                                            class="flex items-center gap-2 flex-wrap"
                                            onsubmit="return confirmRoleChange(this, '{{ $u->name }}')"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <select
                                                name="role"
                                                class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition disabled:opacity-60 disabled:cursor-not-allowed"
                                                data-current="{{ $role }}"
                                                {{ $isMe ? 'disabled' : '' }}
                                            >
                                                <option value="student" @selected($role === 'student')>student</option>
                                                <option value="instructor" @selected($role === 'instructor')>instructor</option>
                                                <option value="admin" @selected($role === 'admin')>admin</option>
                                            </select>

                                            <button
                                                type="submit"
                                                class="rounded-xl bg-blue-600 text-white px-4 py-2 text-sm font-semibold hover:bg-blue-700 transition disabled:opacity-60 disabled:cursor-not-allowed"
                                                {{ $isMe ? 'disabled' : '' }}
                                            >
                                                Update
                                            </button>
                                        </form>

                                        @if($isMe)
                                            <p class="text-xs text-slate-400 mt-2">
                                                You can't change your own role for safety.
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 px-4 text-slate-500 text-center">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            </div>

        </div>
    </div>

    {{-- Confirmation script --}}
    <script>
        function confirmRoleChange(form, name) {
            const select = form.querySelector('select[name="role"]');
            if (!select) return true;

            const currentRole = select.dataset.current || '';
            const newRole = select.value || '';

            if (newRole === currentRole) {
                alert("No change: user already has this role.");
                return false;
            }

            return confirm(`Change role for "${name}" from "${currentRole}" to "${newRole}"?`);
        }
    </script>

</x-layouts.admin>