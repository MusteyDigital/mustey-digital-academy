{{-- resources/views/contact.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us — {{ config('app.name', 'Mustey Digital Academy') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased overflow-x-hidden bg-slate-50 text-slate-900">

    {{-- Nav (matches home.blade.php header pattern) --}}
    <header class="bg-white/80 backdrop-blur border-b border-slate-200 sticky top-0 z-10">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow">
                    M
                </div>
                <span class="font-semibold text-slate-800">{{ config('app.name', 'Mustey Digital Academy') }}</span>
            </a>
            <nav class="hidden sm:flex items-center gap-6 text-sm font-medium text-slate-600">
                <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Home</a>
                <a href="{{ route('courses.index') }}" class="hover:text-blue-600 transition">Courses</a>
                <a href="{{ route('contact') }}" class="text-blue-600">Contact</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-blue-600 transition">Log in</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-white shadow hover:bg-blue-700 transition">Get Started</a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Hero --}}
    <section class="relative bg-gradient-to-br from-blue-600 to-indigo-700 text-white">
        <div class="max-w-6xl mx-auto px-6 py-16 text-center">
            <h1 class="text-3xl sm:text-4xl font-bold">Get in Touch</h1>
            <p class="mt-3 text-blue-100 max-w-xl mx-auto">
                Questions about a course, enrollment, or a technical issue? Send us a message and we'll get back to you.
            </p>
        </div>
    </section>

    <main class="max-w-6xl mx-auto px-6 py-12 grid md:grid-cols-5 gap-8">

        {{-- Contact form --}}
        <div class="md:col-span-3 rounded-2xl border border-slate-200 bg-white shadow-sm p-6 sm:p-8">

            @if (session('status'))
                <div class="mb-6 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                @csrf

                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-slate-700 mb-1">Subject <span class="text-slate-400">(optional)</span></label>
                    <input id="subject" name="subject" type="text" value="{{ old('subject') }}"
                           class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-slate-700 mb-1">Message</label>
                    <textarea id="message" name="message" rows="6" required
                              class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-white font-medium shadow hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Send Message
                </button>
            </form>
        </div>

        {{-- Contact info sidebar --}}
        <div class="md:col-span-2 space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="font-semibold text-slate-800">Email Us</h3>
                <p class="text-sm text-slate-500 mt-1">{{ config('mail.admin_address', config('mail.from.address')) }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-semibold text-slate-800">Response Time</h3>
                <p class="text-sm text-slate-500 mt-1">We typically reply within 24–48 hours on business days.</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <h3 class="font-semibold text-slate-800">Before You Message</h3>
                <p class="text-sm text-slate-500 mt-1">Browse our <a href="{{ route('courses.index') }}" class="text-blue-600 hover:underline">course catalog</a> — your question might already be answered there.</p>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="border-t border-slate-200 bg-white">
        <div class="max-w-6xl mx-auto px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-slate-500">
            <span>&copy; {{ date('Y') }} {{ config('app.name', 'Mustey Digital Academy') }}. All rights reserved.</span>
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Home</a>
                <a href="{{ route('courses.index') }}" class="hover:text-blue-600 transition">Courses</a>
                <a href="{{ route('contact') }}" class="hover:text-blue-600 transition">Contact</a>
            </div>
        </div>
    </footer>

</body>
</html>