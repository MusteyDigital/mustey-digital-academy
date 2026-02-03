@php
    $nav = [
        ['label' => 'Dashboard',   'route' => 'admin.dashboard',          'icon' => '🏠'],
        ['label' => 'Users',       'route' => 'admin.users.index',        'icon' => '👥'],
        ['label' => 'Courses',     'route' => 'admin.courses.index',      'icon' => '📚'],
        ['label' => 'Enrollments', 'route' => 'admin.enrollments.index',  'icon' => '🧾'],
        ['label' => 'Lesson Att.', 'route' => 'admin.attendance.lessons', 'icon' => '✅'],
        ['label' => 'Live Att.',   'route' => 'admin.attendance.live',    'icon' => '🎥'],
        ['label' => 'Certificates','route' => 'admin.certificates.index', 'icon' => '🏅'],
    ];

    $isActive = function ($route) {
        return request()->routeIs($route);
    };
@endphp

<aside class="w-full md:w-64 md:min-h-screen bg-white border-r">
    {{-- Brand --}}
    <div class="p-5 border-b">
        <div class="font-bold text-gray-900 text-lg">Admin Panel</div>
        <div class="text-xs text-gray-500 mt-1">Nexdus × Mustey Academy</div>

        <div class="mt-3 text-xs text-gray-600">
            Logged in as:
            <span class="font-semibold">{{ auth()->user()->name }}</span>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="p-3 space-y-1">
        @foreach($nav as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm border
                      {{ $isActive($item['route'])
                            ? 'bg-gray-900 text-white border-gray-900'
                            : 'bg-white text-gray-700 border-transparent hover:bg-gray-50 hover:border-gray-200' }}">
                <span class="text-base">{{ $item['icon'] }}</span>
                <span class="font-semibold">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- Footer --}}
    <div class="p-4 border-t mt-auto">
        <a href="{{ route('dashboard') }}"
           class="block text-sm text-gray-600 hover:underline">
            ← Back to Main App
        </a>

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit"
                    class="w-full rounded bg-red-600 text-white px-4 py-2 text-sm font-semibold hover:bg-red-700">
                Logout
            </button>
        </form>
    </div>
</aside>
