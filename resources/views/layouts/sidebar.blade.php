@php
    $unreadNotifications = auth()->user()->unreadNotifications()->count();
@endphp

<aside class="w-64 bg-white border-r min-h-screen">
    <div class="p-4 border-b">
        <div class="font-bold text-gray-900">
            {{ config('app.name', 'Nexdus Academy') }}
        </div>
        <div class="text-xs text-gray-600 mt-1">
            Logged in as: {{ auth()->user()->role ?? 'user' }}
        </div>
    </div>

    <nav class="p-4 space-y-2 text-sm">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="block px-3 py-2 rounded hover:bg-gray-100">
            🏠 Dashboard
        </a>

        {{-- ✅ Notifications (ALL ROLES) --}}
        <a href="{{ route('notifications.index') }}"
           class="flex items-center justify-between px-3 py-2 rounded hover:bg-gray-100">
            <span>🔔 Notifications</span>

            @if($unreadNotifications > 0)
                <span class="inline-flex items-center justify-center rounded-full bg-red-600 text-white text-[10px] px-2 py-0.5">
                    {{ $unreadNotifications }}
                </span>
            @endif
        </a>

        {{-- Student --}}
        @if(auth()->user()->role === 'student')
            <a href="{{ route('enrollments.my-courses') }}"
               class="block px-3 py-2 rounded hover:bg-gray-100">
                📚 My Courses
            </a>

            <a href="{{ route('progress.index') }}"
               class="block px-3 py-2 rounded hover:bg-gray-100">
                📈 My Progress
            </a>
        @endif

        {{-- Instructor --}}
        @if(auth()->user()->role === 'instructor' || auth()->user()->role === 'admin')
            <div class="pt-3 text-xs uppercase tracking-wider text-gray-500">
                Instructor Panel
            </div>

            <a href="{{ route('instructor.dashboard') }}"
               class="block px-3 py-2 rounded hover:bg-gray-100">
                👨‍🏫 Instructor Dashboard
            </a>

            <a href="{{ route('instructor.courses.index') }}"
               class="block px-3 py-2 rounded hover:bg-gray-100">
                📘 Manage Courses
            </a>

            <a href="{{ route('courses.create') }}"
               class="block px-3 py-2 rounded hover:bg-gray-100">
                ➕ Create Course
            </a>
        @endif

        {{-- Admin --}}
        @if(auth()->user()->role === 'admin')
            <div class="pt-3 text-xs uppercase tracking-wider text-gray-500">
                Admin Panel
            </div>

            <a href="{{ route('admin.dashboard') }}"
               class="block px-3 py-2 rounded hover:bg-gray-100">
                🛡 Admin Dashboard
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="block px-3 py-2 rounded hover:bg-gray-100">
                👥 Users
            </a>

            <a href="{{ route('admin.courses.index') }}"
               class="block px-3 py-2 rounded hover:bg-gray-100">
                📚 All Courses
            </a>

            <a href="{{ route('admin.enrollments.index') }}"
               class="block px-3 py-2 rounded hover:bg-gray-100">
                ✅ Enrollments
            </a>
        @endif

        <div class="pt-3">
            <a href="{{ route('profile.edit') }}"
               class="block px-3 py-2 rounded hover:bg-gray-100">
                ⚙️ Profile
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left px-3 py-2 rounded hover:bg-gray-100">
                    🚪 Logout
                </button>
            </form>
        </div>

    </nav>
</aside>
