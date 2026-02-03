<x-app-layout>
    {{-- Top Header --}}
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Admin Panel
                </h2>
                <p class="text-sm text-gray-500">
                    Nexdus Academy × Mustey Digital Academy
                </p>
            </div>

            <div class="text-sm text-gray-600">
                Logged in as:
                <span class="font-semibold">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- Sidebar --}}
                <aside class="lg:col-span-3">
                    <div class="bg-white border shadow-sm sm:rounded-lg overflow-hidden">
                        <div class="p-4 border-b bg-gray-50">
                            <p class="text-sm font-semibold text-gray-800">Navigation</p>
                            <p class="text-xs text-gray-500">Admin tools & reports</p>
                        </div>

                        @php
                            $active = 'bg-gray-900 text-white';
                            $inactive = 'text-gray-700 hover:bg-gray-50';
                            $icon = 'w-5 h-5';

                            $is = function ($pattern) use ($active, $inactive) {
                                return request()->routeIs($pattern) ? $active : $inactive;
                            };
                        @endphp

                        <nav class="p-3 space-y-1">

                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold {{ $is('admin.dashboard') }}">
                                <span class="{{ $icon }}">🏠</span> Dashboard
                            </a>

                            <a href="{{ route('admin.users.index') }}"
                               class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold {{ $is('admin.users.*') }}">
                                <span class="{{ $icon }}">👥</span> Users
                            </a>

                            <a href="{{ route('admin.courses.index') }}"
                               class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold {{ $is('admin.courses.*') }}">
                                <span class="{{ $icon }}">📚</span> Courses
                            </a>

                            <a href="{{ route('admin.enrollments.index') }}"
                               class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold {{ $is('admin.enrollments.*') }}">
                                <span class="{{ $icon }}">🧾</span> Enrollments
                            </a>

                            <a href="{{ route('admin.attendance.lessons') }}"
                               class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold {{ $is('admin.attendance.lessons') }}">
                                <span class="{{ $icon }}">✅</span> Lesson Attendance
                            </a>

                            <a href="{{ route('admin.attendance.live') }}"
                               class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold {{ $is('admin.attendance.live') }}">
                                <span class="{{ $icon }}">🎥</span> Live Attendance
                            </a>

                            <a href="{{ route('admin.certificates.index') }}"
                               class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold {{ $is('admin.certificates.*') }}">
                                <span class="{{ $icon }}">🏅</span> Certificates
                            </a>

                        </nav>

                        <div class="p-4 border-t bg-gray-50">
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center justify-center w-full rounded-md border px-4 py-2 text-sm hover:bg-white">
                                ← Back to Main Dashboard
                            </a>
                        </div>
                    </div>
                </aside>

                {{-- Main Content --}}
                <main class="lg:col-span-9 space-y-6">

                    {{-- Flash Messages --}}
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

                    {{-- Page Content --}}
                    {{ $slot }}

                </main>

            </div>
        </div>
    </div>
</x-app-layout>
