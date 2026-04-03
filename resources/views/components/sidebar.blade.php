@php
    $user = auth()->user();

    $isAdmin = $user && $user->role === 'admin';
    $isInstructor = $user && $user->role === 'instructor';
    $isStudent = $user && $user->role === 'student';

    $dashboardRoute = $isAdmin
        ? route('admin.dashboard')
        : ($isInstructor ? route('instructor.dashboard') : route('student.dashboard'));

    // ✅ Use instructor routes for instructor actions
    $coursesManageRoute = $isInstructor ? route('instructor.courses.index') : route('courses.index');
@endphp

{{-- ✅ Mobile top bar (hamburger) --}}
<div class="md:hidden w-full bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3 flex items-center justify-between">
    <a href="{{ $dashboardRoute }}" class="font-semibold text-gray-800 dark:text-gray-100">
        {{ config('app.name', 'Nexdus Academy') }}
    </a>

    <button
        type="button"
        onclick="document.getElementById('mobileSidebar').classList.toggle('hidden')"
        class="inline-flex items-center justify-center rounded-md border px-3 py-2 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 hover:bg-gray-50"
        aria-label="Open menu"
    >
        ☰
    </button>
</div>

{{-- ✅ Mobile sidebar drawer --}}
<div id="mobileSidebar" class="hidden md:hidden fixed inset-0 z-50">
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('mobileSidebar').classList.add('hidden')"></div>

    {{-- Drawer --}}
    <div class="absolute left-0 top-0 h-full w-72 bg-white dark:bg-gray-800 shadow-lg p-4 overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <div class="font-semibold text-gray-800 dark:text-gray-100">
                Menu
            </div>
            <button
                type="button"
                onclick="document.getElementById('mobileSidebar').classList.add('hidden')"
                class="rounded-md border px-3 py-1 text-sm bg-white dark:bg-gray-900 dark:text-gray-100 hover:bg-gray-50"
            >
                ✕
            </button>
        </div>

        {{-- Links --}}
        <nav class="space-y-2">
            <a href="{{ $dashboardRoute }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                🏠 Dashboard
            </a>

            <a href="{{ route('courses.index') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                📚 All Courses
            </a>

            @if($isStudent)
                <a href="{{ route('enrollments.my-courses') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                    ✅ My Courses
                </a>

                <a href="{{ route('progress.index') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                    📈 My Progress
                </a>
		
		<a href="{{ route('practice-lab.index') }}"
   		   class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
    		    🧠 Practice Lab
		</a>
            @endif

            @if($isInstructor)
                <a href="{{ $coursesManageRoute }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                    🛠 Manage Courses
                </a>

                <a href="{{ route('courses.create') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                    ➕ Create Course
                </a>
            @endif

            @if($isAdmin)
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                    🧩 Admin Panel
                </a>

                <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                    👥 Users
                </a>

                <a href="{{ route('admin.courses.index') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                    📚 Courses (Admin)
                </a>
            @endif

            <a href="{{ route('notifications.index') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                🔔 Notifications
            </a>

            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                ⚙ Profile
            </a>

            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-md bg-red-50 hover:bg-red-100 text-red-700">
                    🚪 Logout
                </button>
            </form>
        </nav>
    </div>
</div>

{{-- ✅ Desktop sidebar --}}
<aside class="hidden md:block w-64 min-h-screen bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
    <div class="p-4">
        <div class="text-sm text-gray-500 dark:text-gray-300">Signed in as</div>
        <div class="font-semibold text-gray-900 dark:text-gray-100">
            {{ $user->name }}
        </div>
        <div class="text-xs mt-1 inline-flex rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-1 text-gray-700 dark:text-gray-100">
            {{ ucfirst($user->role) }}
        </div>
    </div>

    <nav class="px-3 pb-6 space-y-1">
        <a href="{{ $dashboardRoute }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
            🏠 Dashboard
        </a>

        <a href="{{ route('courses.index') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
            📚 All Courses
        </a>

        @if($isStudent)
            <a href="{{ route('enrollments.my-courses') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                ✅ My Courses
            </a>

            <a href="{{ route('progress.index') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                📈 My Progress
            </a>

            <a href="{{ route('practice-lab.index') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                🧠 Practice Lab
            </a>
        @endif

        @if($isInstructor)
            <a href="{{ $coursesManageRoute }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                🛠 Manage Courses
            </a>

            <a href="{{ route('courses.create') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                ➕ Create Course
            </a>
        @endif

        @if($isAdmin)
            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                🧩 Admin Panel
            </a>

            <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                👥 Users
            </a>

            <a href="{{ route('admin.courses.index') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
                📚 Courses (Admin)
            </a>
        @endif

        <a href="{{ route('notifications.index') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
            🔔 Notifications
        </a>

        <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100">
            ⚙ Profile
        </a>

        <form method="POST" action="{{ route('logout') }}" class="pt-3">
            @csrf
            <button type="submit" class="w-full text-left px-3 py-2 rounded-md bg-red-50 hover:bg-red-100 text-red-700">
                🚪 Logout
            </button>
        </form>
    </nav>
</aside>
