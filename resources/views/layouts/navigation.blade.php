<nav x-data="{ mobileSidebar: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- LEFT: Logo + Desktop Links --}}
            <div class="flex items-center gap-3 min-w-0">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>
                <span class="block sm:hidden min-w-0 truncate text-sm font-semibold text-gray-800 dark:text-gray-200">
                    {{ config('app.name', 'Mustey Digital Academy') }}
                </span>

                {{-- Desktop nav links --}}
                <div class="hidden sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(auth()->check() && auth()->user()->role === 'instructor')
                        <x-nav-link :href="route('instructor.dashboard')" :active="request()->routeIs('instructor.dashboard')">
                            {{ __('Instructor Dashboard') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            {{-- RIGHT: User Dropdown + Mobile Sidebar Button --}}
            <div class="flex items-center gap-3">

                {{-- Notifications (optional: keep if you have it) --}}
                <div class="flex items-center">
                    <a href="{{ route('notifications.index') }}"
                       class="relative inline-flex items-center px-2 py-2 text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0m6 0H9" />
                        </svg>
                    </a>
                </div>

                {{-- Mobile sidebar toggle (hamburger) --}}
                <button
                    type="button"
                    @click="mobileSidebar = true"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:hidden"
                    aria-label="Open sidebar"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- User dropdown --}}
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                {{-- Mobile dropdown trigger --}}
            </div>
        </div>
    </div>


    {{-- MOBILE SIDEBAR OVERLAY + DRAWER --}}
    <div
        x-show="mobileSidebar"
        x-cloak
        class="fixed inset-0 z-50 sm:hidden"
        aria-label="Mobile sidebar"
    >
        {{-- overlay --}}
        <div
            class="absolute inset-0 bg-black/50"
            @click="mobileSidebar = false"
        ></div>

        {{-- drawer --}}
        <div
            class="absolute left-0 top-0 h-full w-80 max-w-[85%] bg-white dark:bg-gray-900 shadow-xl p-4 overflow-y-auto"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
        >
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <x-application-logo class="block h-8 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    <span class="font-semibold text-gray-800 dark:text-gray-200">
                        {{ config('app.name', 'Mustey Digital Academy') }}
                    </span>
                </div>

                <button
                    type="button"
                    @click="mobileSidebar = false"
                    class="p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800"
                    aria-label="Close sidebar"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- ✅ Use the same sidebar content --}}
            @include('layouts.sidebar')
        </div>
    </div>
</nav>
