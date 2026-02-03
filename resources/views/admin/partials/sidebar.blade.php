<div class="bg-white border shadow-sm sm:rounded-lg overflow-hidden">
    <div class="p-4 border-b bg-gray-50">
        <p class="text-sm font-semibold text-gray-800">Navigation</p>
        <p class="text-xs text-gray-500">Admin tools & reports</p>
    </div>

    @php
        $is = fn($name) => request()->routeIs($name)
            ? 'bg-gray-900 text-white'
            : 'text-gray-700 hover:bg-gray-50';
    @endphp

    <nav class="p-3 space-y-1">

        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold {{ $is('admin.dashboard') }}">
            🏠 Dashboard
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.users.*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-50' }}">
            👥 Users
        </a>

        <a href="{{ route('admin.courses.index') }}"
           class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.courses.*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-50' }}">
            📚 Courses
        </a>

        <a href="{{ route('admin.enrollments.index') }}"
           class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.enrollments.*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-50' }}">
            🧾 Enrollments
        </a>

        <a href="{{ route('admin.attendance.lessons') }}"
           class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.attendance.lessons') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-50' }}">
            ✅ Lesson Attendance
        </a>

        <a href="{{ route('admin.attendance.live') }}"
           class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.attendance.live') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-50' }}">
            🎥 Live Attendance
        </a>

        <a href="{{ route('admin.certificates.index') }}"
           class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.certificates.*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-50' }}">
            🏅 Certificates
        </a>

    </nav>

    <div class="p-4 border-t bg-gray-50">
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center justify-center w-full rounded-md border px-4 py-2 text-sm hover:bg-white">
            ← Back to Main Dashboard
        </a>
    </div>
</div>
