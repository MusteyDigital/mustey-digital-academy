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
        @php $unreadNotifications = auth()->user()->unreadNotifications()->count(); @endphp
    @endauth

    <div class="min-h-screen flex">

        {{-- Desktop Sidebar --}}
        @auth
            <aside class="hidden md:flex shrink-0 w-64">
                @include('layouts.sidebar')
            </aside>
        @endauth

        {{-- Main Content --}}
        <div class="flex-1 min-w-0 flex flex-col">

            {{-- Top bar --}}
            @auth
            <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between sticky top-0 z-10 shadow-sm">
                <div>
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
