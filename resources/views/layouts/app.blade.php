<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Mustey Digital Academy') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased overflow-x-hidden bg-slate-50">
    <x-toast />
    @auth
        @php
            $unreadNotifications = auth()->user()->unreadNotifications()->count();
            $isDemoUser = in_array(auth()->user()->email, [
                'demo-student@musteydigitalacademy.online',
                'demo-instructor@musteydigitalacademy.online',
            ], true);
        @endphp
    @endauth

    @auth
        @if($isDemoUser)
            <div class="bg-amber-400 text-amber-950 text-xs sm:text-sm font-semibold text-center py-1.5 px-4 sticky top-0 z-40">
                🎭 Demo Mode — you're exploring a sandboxed account. Creating, editing, and deleting is disabled.
            </div>
        @endif
    @endauth

    <div class="min-h-screen flex" x-data="{ mobileSidebarOpen: false }">

        {{-- Desktop Sidebar --}}
        @auth
            <aside class="hidden md:flex shrink-0 w-64">
                @include('layouts.sidebar')
            </aside>
        @endauth

        {{-- Mobile Sidebar Drawer --}}
        @auth
            <div
                x-show="mobileSidebarOpen"
                x-cloak
                class="fixed inset-0 z-50 md:hidden"
                style="display: none;"
            >
                <div
                    class="absolute inset-0 bg-black/50"
                    @click="mobileSidebarOpen = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                ></div>

                <div
                    class="absolute left-0 top-0 h-full w-64 max-w-[85%] overflow-y-auto"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="-translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-full"
                    @click="mobileSidebarOpen = false"
                >
                    @include('layouts.sidebar')
                </div>
            </div>
        @endauth

        {{-- Main Content --}}
        <div class="flex-1 min-w-0 flex flex-col">
            {{-- Top bar --}}
            @auth
            <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between sticky {{ $isDemoUser ? 'top-8' : 'top-0' }} z-10 shadow-sm">
                <div class="flex items-center gap-3">
                    {{-- Mobile Menu Button --}}
                    <button
                        type="button"
                        @click="mobileSidebarOpen = true"
                        class="md:hidden text-slate-500 hover:text-blue-600 transition"
                        aria-label="Open menu"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    @isset($header)
                        <h1 class="text-lg font-semibold text-slate-800">{{ $header }}</h1>
                    @endisset
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('notifications.index') }}" class="relative text-slate-500 hover:text-blue-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0m6 0H9"/></svg>
                        @if(isset($unreadNotifications) && $unreadNotifications > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">{{ $unreadNotifications }}</span>
                        @endif
                    </a>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xs shadow">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="text-sm font-medium text-slate-700 hidden sm:block">{{ auth()->user()->name }}</span>
                        @if($isDemoUser)
                            <span class="hidden sm:inline-flex items-center rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 uppercase tracking-wide">Demo</span>
                        @endif
                    </div>
                </div>
            </header>
            @endauth
            {{-- Page Content --}}
            <main class="flex-1 p-6">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </main>
        </div>
    </div>
</body>
</html>
