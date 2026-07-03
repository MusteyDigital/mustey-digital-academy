@php
    $nav = [
        ['label' => 'Dashboard',          'route' => 'admin.dashboard',          'match' => 'admin.dashboard',          'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['label' => 'Users',              'route' => 'admin.users.index',        'match' => 'admin.users.*',            'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8zm6 0a4 4 0 100-8 4 4 0 000 8z'],
        ['label' => 'Courses',            'route' => 'admin.courses.index',      'match' => 'admin.courses.*',          'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ['label' => 'Enrollments',        'route' => 'admin.enrollments.index',  'match' => 'admin.enrollments.*',      'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['label' => 'Payments',           'route' => 'admin.payments.index',     'match' => 'admin.payments.*',         'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ['label' => 'Coupons',            'route' => 'admin.coupons.index',      'match' => 'admin.coupons.*',          'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
        ['label' => 'Lesson Attendance',  'route' => 'admin.attendance.lessons', 'match' => 'admin.attendance.lessons', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Live Attendance',    'route' => 'admin.attendance.live',    'match' => 'admin.attendance.live',    'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
        ['label' => 'Certificates',       'route' => 'admin.certificates.index', 'match' => 'admin.certificates.*',     'icon' => 'M12 15a4 4 0 100-8 4 4 0 000 8zm0 0v6m-4-2.5L6 21m10-2.5l2 2.5'],
    ];
    $isActive = function ($match) {
        return request()->routeIs($match);
    };
@endphp
<aside class="w-full md:w-64 md:min-h-screen bg-white border-r border-slate-200">
    {{-- Brand --}}
    <div class="p-5 border-b border-slate-100">
        <div class="font-bold text-slate-800 text-lg">Admin Panel</div>
        <div class="text-xs text-slate-500 mt-1">Mustey Digital Academy</div>
        <div class="mt-3 text-xs text-slate-500">
            Logged in as:
            <span class="font-semibold text-slate-700">{{ auth()->user()->name }}</span>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="p-3 space-y-1">
        @foreach($nav as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition
                      {{ $isActive($item['match'])
                            ? 'bg-slate-900 text-white'
                            : 'text-slate-700 hover:bg-slate-50' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>
                <span class="font-medium">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- Footer --}}
    <div class="p-4 border-t border-slate-100 mt-auto">
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Main App
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit"
                    class="w-full rounded-xl bg-red-600 text-white px-4 py-2.5 text-sm font-semibold hover:bg-red-700 transition">
                Logout
            </button>
        </form>
    </div>
</aside>
