<x-app-layout>
    {{-- Top Header --}}
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    Admin Panel
                </h2>
                <p class="text-sm text-slate-500">
                    Mustey Digital Academy
                </p>
            </div>

            <div class="text-sm text-slate-600">
                Logged in as:
                <span class="font-semibold text-slate-800">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-10" x-data="{ mobileSidebarOpen: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mobile Sidebar Toggle --}}
            <button
                type="button"
                @click="mobileSidebarOpen = true"
                class="lg:hidden mb-4 inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Admin Menu
            </button>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- Sidebar: desktop (static) --}}
                <aside class="hidden lg:block lg:col-span-3">
                    @include('components.admin.sidebar-nav')
                </aside>

                {{-- Sidebar: mobile (drawer) --}}
                <div
                    x-show="mobileSidebarOpen"
                    x-cloak
                    class="fixed inset-0 z-50 lg:hidden"
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
                        class="absolute left-0 top-0 h-full w-80 max-w-[85%] bg-white shadow-xl p-4 overflow-y-auto"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="-translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="-translate-x-full"
                    >
                        <div class="flex items-center justify-between mb-4">
                            <span class="font-semibold text-slate-800">Admin Menu</span>
                            <button
                                type="button"
                                @click="mobileSidebarOpen = false"
                                class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition"
                                aria-label="Close menu"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        @include('components.admin.sidebar-nav')
                    </div>
                </div>

                {{-- Main Content --}}
                <main class="lg:col-span-9 space-y-6">

                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800 text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 text-sm">
                            <ul class="list-disc pl-5 space-y-1">
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