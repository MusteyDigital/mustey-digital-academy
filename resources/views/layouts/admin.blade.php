<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Panel' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900">

    {{-- Top bar --}}
    <header class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold">Admin Panel</h1>
                <p class="text-xs text-gray-500">Mustey Digital Academy</p>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-600">
                    Logged in as: <span class="font-semibold">{{ auth()->user()->name }}</span>
                </span>

                {{-- Mobile menu button --}}
                <button id="openSidebar"
                        class="lg:hidden inline-flex items-center justify-center rounded-md border px-3 py-2 text-sm hover:bg-gray-50">
                    ☰ Menu
                </button>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- Sidebar (desktop) --}}
            <aside class="hidden lg:block lg:col-span-3">
                @include('admin.partials.sidebar')
            </aside>

            {{-- Mobile sidebar overlay --}}
            <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 hidden z-40 lg:hidden"></div>

            {{-- Sidebar (mobile drawer) --}}
            <aside id="mobileSidebar"
                   class="fixed top-0 left-0 h-full w-72 bg-white shadow-lg transform -translate-x-full transition z-50 lg:hidden">
                <div class="p-4 border-b flex items-center justify-between">
                    <div>
                        <p class="font-semibold">Navigation</p>
                        <p class="text-xs text-gray-500">Admin tools & reports</p>
                    </div>
                    <button id="closeSidebar" class="rounded-md border px-3 py-1 text-sm hover:bg-gray-50">✕</button>
                </div>

                <div class="p-3">
                    @include('admin.partials.sidebar', ['isMobile' => true])
                </div>
            </aside>

            {{-- Main content --}}
            <main class="lg:col-span-9 space-y-6">

                {{-- Flash messages --}}
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

                {{-- Page content --}}
                {{ $slot }}

            </main>

        </div>
    </div>

    {{-- Sidebar JS --}}
    <script>
        const openBtn = document.getElementById('openSidebar');
        const closeBtn = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('mobileSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            if (!sidebar || !overlay) return;
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        }

        function closeSidebar() {
            if (!sidebar || !overlay) return;
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }

        openBtn?.addEventListener('click', openSidebar);
        closeBtn?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);
    </script>
    <script>
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') closeSidebar();
});
</script>


</body>
</html>
