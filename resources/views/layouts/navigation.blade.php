<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- ✅ Student Links --}}
                    @auth
                        @if(auth()->user()->role === 'student')
                            <x-nav-link :href="route('enrollments.my-courses')" :active="request()->routeIs('enrollments.my-courses')">
                                My Courses
                            </x-nav-link>

                            <x-nav-link :href="route('progress.index')" :active="request()->routeIs('progress.*')">
                                My Progress
                            </x-nav-link>
                        @endif
                    @endauth

                    {{-- ✅ Instructor Link --}}
                    @auth
                        @if(auth()->user()->role === 'instructor')
                            <x-nav-link :href="route('instructor.dashboard')" :active="request()->routeIs('instructor.*')">
                                Instructor Dashboard
                            </x-nav-link>
                        @endif
                    @endauth

                    {{-- ✅ Admin Panel Link (your original) --}}
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                            Admin Panel
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Right side -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">

                {{-- ✅ Notifications Bell (Desktop) --}}
                @auth
                    @php
                        $unreadCount = auth()->user()->unreadNotifications()->count();
                        $latestNotifications = auth()->user()->notifications()->latest()->take(5)->get();
                    @endphp

                    <x-dropdown align="right" width="80">
                        <x-slot name="trigger">
                            <button
                                class="relative inline-flex items-center p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition">
                                {{-- Bell Icon --}}
                                <svg class="h-6 w-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0h6z" />
                                </svg>

                                {{-- Unread badge --}}
                                @if($unreadCount > 0)
                                    <span
                                        class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 border-b text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Notifications
                            </div>

                            {{-- Latest notifications --}}
                            <div class="max-h-80 overflow-y-auto">
                                @forelse($latestNotifications as $n)
                                    <div class="px-4 py-3 border-b">
                                        <p class="text-sm text-gray-800 dark:text-gray-100">
                                            {{ $n->data['message'] ?? 'New notification' }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $n->created_at->diffForHumans() }}
                                        </p>

                                        {{-- Mark as read --}}
                                        @if(is_null($n->read_at))
                                            <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="mt-2">
                                                @csrf
                                                <button type="submit" class="text-xs underline text-blue-600">
                                                    Mark as read
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @empty
                                    <div class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        No notifications yet.
                                    </div>
                                @endforelse
                            </div>

                            <div class="px-4 py-3">
                                <a href="{{ route('notifications.index') }}"
                                   class="block text-center text-sm underline text-blue-600">
                                    View all notifications →
                                </a>

                                @if($unreadCount > 0)
                                    <form method="POST" action="{{ route('notifications.readAll') }}" class="mt-2">
                                        @csrf
                                        <button type="submit"
                                                class="w-full text-center text-sm rounded-md border px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            Mark all as read
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </x-slot>
                    </x-dropdown>
                @endauth

                <!-- Settings Dropdown -->
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

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            {{-- ✅ Student Links (Mobile) --}}
            @auth
                @if(auth()->user()->role === 'student')
                    <x-responsive-nav-link :href="route('enrollments.my-courses')" :active="request()->routeIs('enrollments.my-courses')">
                        My Courses
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('progress.index')" :active="request()->routeIs('progress.*')">
                        My Progress
                    </x-responsive-nav-link>
                @endif
            @endauth

            {{-- ✅ Instructor Link (Mobile) --}}
            @auth
                @if(auth()->user()->role === 'instructor')
                    <x-responsive-nav-link :href="route('instructor.dashboard')" :active="request()->routeIs('instructor.*')">
                        Instructor Dashboard
                    </x-responsive-nav-link>
                @endif
            @endauth

            {{-- ✅ Admin Panel Link (Mobile) --}}
            @if(auth()->check() && auth()->user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                    Admin Panel
                </x-responsive-nav-link>
            @endif

            {{-- ✅ Notifications Link (Mobile) --}}
            @auth
                <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                    Notifications
                    @php $unreadCountMobile = auth()->user()->unreadNotifications()->count(); @endphp
                    @if($unreadCountMobile > 0)
                        <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-red-600 rounded-full">
                            {{ $unreadCountMobile }}
                        </span>
                    @endif
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
