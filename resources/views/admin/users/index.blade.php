<x-layouts.admin>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin — Users
            </h2>

            <a href="{{ route('admin.dashboard') }}" class="underline text-gray-600">
                ← Back to Admin Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash --}}
            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Search + Export --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
                <form method="GET" class="flex flex-wrap gap-2 items-end">

                    <div class="flex-1 min-w-[220px]">
                        <label class="text-sm text-gray-600">Search</label>
                        <input name="q" value="{{ $q }}" class="w-full border rounded p-2" placeholder="Name or email">
                    </div>

                    <button class="rounded bg-gray-900 text-white px-4 py-2 text-sm font-semibold">
                        Filter
                    </button>

                    <a href="{{ route('admin.users.index') }}"
                       class="rounded border px-4 py-2 text-sm hover:bg-gray-50">
                        Reset
                    </a>

                    {{-- Export CSV --}}
                    <a href="{{ route('admin.users.export', ['q' => $q]) }}"
                       class="rounded bg-blue-600 text-white px-4 py-2 text-sm font-semibold hover:bg-blue-700">
                        Export CSV
                    </a>

                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="text-left p-3">User</th>
                                <th class="text-left p-3">Email</th>
                                <th class="text-left p-3">Role</th>
                                <th class="text-left p-3">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($users as $u)
                                @php
                                    $role = $u->role ?? 'student';

                                    $badge = match($role) {
                                        'admin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'instructor' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        default => 'bg-green-100 text-green-800 border-green-200',
                                    };

                                    $isMe = auth()->id() === $u->id;
                                @endphp

                                <tr class="border-b">
                                    <td class="p-3">
                                        <div class="font-semibold text-gray-900">{{ $u->name }}</div>
                                        <div class="text-xs text-gray-500">ID: {{ $u->id }}</div>

                                        @if($isMe)
                                            <div class="mt-1 inline-flex text-xs rounded-full bg-gray-100 text-gray-700 px-2 py-1 border">
                                                (This is you)
                                            </div>
                                        @endif
                                    </td>

                                    <td class="p-3 text-gray-700">
                                        {{ $u->email }}
                                    </td>

                                    <td class="p-3">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold border {{ $badge }}">
                                            {{ $role }}
                                        </span>
                                    </td>

                                    <td class="p-3">
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
                                                class="border rounded p-2 text-sm"
                                                data-current="{{ $role }}"
                                                {{ $isMe ? 'disabled' : '' }}
                                            >
                                                <option value="student" @selected($role === 'student')>student</option>
                                                <option value="instructor" @selected($role === 'instructor')>instructor</option>
                                                <option value="admin" @selected($role === 'admin')>admin</option>
                                            </select>

                                            <button
                                                type="submit"
                                                class="rounded bg-blue-600 text-white px-4 py-2 text-sm font-semibold hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed"
                                                {{ $isMe ? 'disabled' : '' }}
                                            >
                                                Update
                                            </button>
                                        </form>

                                        @if($isMe)
                                            <p class="text-xs text-gray-500 mt-2">
                                                You can’t change your own role for safety.
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-gray-600">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
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
