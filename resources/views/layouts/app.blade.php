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

<body class="font-sans antialiased overflow-x-hidden">
    <x-toast />

    @auth
        @php
            $unreadNotifications = auth()->user()->unreadNotifications()->count();
        @endphp
    @endauth

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">

        @include('layouts.navigation')


        <div class="flex">
            {{-- Desktop sidebar --}}
            @auth
                <aside class="hidden md:block shrink-0">
                    @include('layouts.sidebar')
                </aside>
            @endauth


            <div class="flex-1 min-w-0">
                {{-- Support both component headers and normal pages --}}
                @isset($header)
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{-- If a component page uses <x-app-layout>, it renders $slot --}}
                        @isset($slot)
                            {{ $slot }}
                        @else
                            {{-- If a normal page uses @extends, it renders @yield --}}
                            @yield('content')
                        @endisset
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>
