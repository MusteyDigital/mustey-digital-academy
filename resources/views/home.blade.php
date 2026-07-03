{{-- resources/views/home.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Mustey Digital Academy') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white text-slate-900 overflow-x-hidden">

    {{-- Flash messages --}}
    @if(session('info'))
        <div class="bg-blue-600 text-white text-sm text-center py-2 px-4">
            {{ session('info') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-600 text-white text-sm text-center py-2 px-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <header class="border-b border-slate-200 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 sticky top-0 z-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 shrink-0 flex items-center justify-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="leading-tight min-w-0">
                        <div class="font-bold truncate text-slate-800">{{ config('app.name', 'Mustey Digital Academy') }}</div>
                        <div class="text-xs text-blue-600 truncate font-medium">Learn • Build • Grow</div>
                    </div>
                </a>

                <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                    <a href="#demo"     class="text-slate-600 hover:text-blue-600 transition">Try Demo</a>
                    <a href="#featured" class="text-slate-600 hover:text-blue-600 transition">Featured</a>
                    <a href="#courses"  class="text-slate-600 hover:text-blue-600 transition">Courses</a>
                    <a href="#features" class="text-slate-600 hover:text-blue-600 transition">Why us</a>
                    <a href="{{ route('contact') }}" class="text-slate-600 hover:text-blue-600 transition">Contact</a>
                </nav>

                <div class="flex items-center gap-2 shrink-0">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                            Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-blue-50/60 to-white -z-10"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 sm:py-14 lg:py-20">
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div class="min-w-0">
                    <p class="inline-flex items-center gap-2 rounded-full bg-blue-50 border border-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700">
                        🚀 Learn digital skills the right way
                    </p>

                    <h1 class="mt-4 text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight break-words text-slate-900">
                        Learn practical digital skills with
                        <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Mustey Digital Academy</span>
                    </h1>

                    <p class="mt-4 text-base sm:text-lg text-slate-600 max-w-xl">
                        Build job-ready skills in Data Analysis, Web Development, and more — with structured lessons,
                        quizzes, certificates, and progress tracking.
                    </p>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="#courses"
                           class="inline-flex justify-center items-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 transition shadow-sm">
                            Browse Courses
                        </a>
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="inline-flex justify-center items-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="#demo"
                               class="inline-flex justify-center items-center rounded-xl border border-blue-200 bg-blue-50 px-5 py-3 text-sm font-medium text-blue-700 hover:bg-blue-100 transition">
                                Try Demo →
                            </a>
                        @endauth
                    </div>

                    <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 gap-4 max-w-lg">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="text-xl font-bold text-slate-800">Courses</div>
                            <div class="text-xs text-slate-500 mt-0.5">Learn step-by-step</div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="text-xl font-bold text-slate-800">Quizzes</div>
                            <div class="text-xs text-slate-500 mt-0.5">Track progress</div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:col-auto col-span-2">
                            <div class="text-xl font-bold text-slate-800">Certificates</div>
                            <div class="text-xs text-slate-500 mt-0.5">Earn proof of learning</div>
                        </div>
                    </div>
                </div>

                {{-- Right side card --}}
                <div class="lg:justify-self-end w-full">
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div class="font-bold text-slate-800">Top Tracks</div>
                            <span class="text-xs text-slate-400 font-medium">Updated</span>
                        </div>
                        <div class="mt-4 space-y-3">
                            <div class="rounded-xl border border-slate-200 p-4 hover:border-blue-200 hover:bg-blue-50/40 transition">
                                <div class="font-semibold text-slate-800">Data Analysis</div>
                                <div class="text-sm text-slate-500 mt-0.5">Excel • Power BI • Projects</div>
                            </div>
                            <div class="rounded-xl border border-slate-200 p-4 hover:border-blue-200 hover:bg-blue-50/40 transition">
                                <div class="font-semibold text-slate-800">Web Development</div>
                                <div class="text-sm text-slate-500 mt-0.5">HTML • CSS • JS • Laravel</div>
                            </div>
                            <div class="rounded-xl border border-slate-200 p-4 hover:border-blue-200 hover:bg-blue-50/40 transition">
                                <div class="font-semibold text-slate-800">Digital Literacy</div>
                                <div class="text-sm text-slate-500 mt-0.5">Productivity • Internet Safety</div>
                            </div>
                        </div>
                        <div class="mt-6 rounded-xl bg-blue-50 border border-blue-100 p-4 text-sm text-blue-800">
                            💡 Tip: Try the demo below — no account needed.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Demo Section --}}
    <section id="demo" class="bg-slate-900 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">

            <div class="text-center max-w-2xl mx-auto">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-white/80 mb-4">
                    🎯 No account needed
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold">See the platform in action</h2>
                <p class="mt-3 text-white/70">
                    Watch the walkthrough, then jump in yourself as a student or instructor — instantly.
                </p>
            </div>

            {{-- Video --}}
            <div class="mt-10 max-w-4xl mx-auto">
                <div class="relative rounded-2xl overflow-hidden bg-black aspect-video shadow-2xl">
                    @if(config('app.demo_video_url'))
                        <iframe
                            src="{{ config('app.demo_video_url') }}"
                            class="absolute inset-0 w-full h-full"
                            frameborder="0"
                            allow="autoplay; fullscreen"
                            allowfullscreen>
                        </iframe>
                    @else
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-white/30 gap-3">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/>
                            </svg>
                            <span class="text-sm font-medium">Demo video coming soon</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Demo buttons --}}
            @guest
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <div class="text-center">
                    <a href="{{ route('demo.student') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white text-slate-900 px-7 py-4 text-sm font-semibold hover:bg-slate-100 transition-all active:scale-95 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                        </svg>
                        Try as Student
                    </a>
                    <p class="mt-2 text-xs text-white/50">Browse courses, watch lessons, take quizzes</p>
                </div>

                <div class="hidden sm:block text-white/20 text-2xl font-thin">|</div>

                <div class="text-center">
                    <a href="{{ route('demo.instructor') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 text-white px-7 py-4 text-sm font-semibold hover:bg-white/20 transition-all active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                        Try as Instructor
                    </a>
                    <p class="mt-2 text-xs text-white/50">Create courses, manage content, view analytics</p>
                </div>
            </div>
            <p class="mt-6 text-center text-xs text-white/40">
                Demo data resets automatically every 2 hours. No real data is affected.
            </p>
            @endguest

            @auth
            <div class="mt-8 text-center">
                <a href="{{ url('/dashboard') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-white text-slate-900 px-7 py-4 text-sm font-semibold hover:bg-slate-100 transition">
                    Go to your Dashboard →
                </a>
            </div>
            @endauth
        </div>
    </section>

    {{-- Featured --}}
    <section id="featured" class="bg-slate-50 border-y border-slate-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Featured Courses</h2>
                    <p class="mt-2 text-slate-500">Hand-picked courses to get you started fast.</p>
                </div>
            </div>
            <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($featuredCourses as $course)
                    @include('partials.course-card', ['course' => $course])
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-6 text-slate-500 text-sm">
                        No featured courses yet. Mark courses as featured from the admin/instructor panel.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Latest Courses --}}
    <section id="courses">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Latest Courses</h2>
                    <p class="mt-2 text-slate-500">Newest courses on the platform.</p>
                </div>
                <a href="{{ url('/courses') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                    View all
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
            <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($latestCourses as $course)
                    @include('partials.course-card', ['course' => $course])
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-6 text-slate-500 text-sm">
                        No courses yet. Add a course from the instructor/admin dashboard.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="bg-slate-50 border-y border-slate-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-2xl font-bold text-slate-800">Why students love this platform</h2>
            <p class="mt-2 text-slate-500 max-w-2xl">
                Simple, fast, and structured — designed for learning on phone or PC.
            </p>
            <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="font-semibold text-slate-800">Structured lessons</div>
                    <p class="mt-2 text-sm text-slate-500">Modules and lessons organized clearly for easy study.</p>
                </div>
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <div class="font-semibold text-slate-800">Quizzes & tracking</div>
                    <p class="mt-2 text-sm text-slate-500">Assess learning and monitor progress automatically.</p>
                </div>
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                    <div class="w-10 h-10 rounded-xl bg-green-100 text-green-600 flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="font-semibold text-slate-800">Certificates</div>
                    <p class="mt-2 text-sm text-slate-500">Reward completion with downloadable certificates.</p>
                </div>
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21a48.309 48.309 0 01-8.135-.687c-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                        </svg>
                    </div>
                    <div class="font-semibold text-slate-800">Practice Lab</div>
                    <p class="mt-2 text-sm text-slate-500">Interactive DRAB exercises with XP and leaderboards.</p>
                </div>
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                    <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <div class="font-semibold text-slate-800">Live Sessions</div>
                    <p class="mt-2 text-sm text-slate-500">Join scheduled live classes directly from your dashboard.</p>
                </div>
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                        </svg>
                    </div>
                    <div class="font-semibold text-slate-800">Secure Payments</div>
                    <p class="mt-2 text-sm text-slate-500">Pay safely via Paystack — cards, bank transfer, USSD.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-gradient-to-br from-blue-700 to-indigo-800 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <div>
                    <h2 class="text-2xl font-bold">Ready to start learning?</h2>
                    <p class="mt-2 text-blue-100 max-w-xl">
                        Create an account, enroll in a course, and begin your learning journey today.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 lg:justify-end">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="inline-flex justify-center items-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 hover:bg-slate-100 transition">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="inline-flex justify-center items-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 hover:bg-slate-100 transition">
                            Create Account
                        </a>
                        <a href="{{ route('demo.student') }}"
                           class="inline-flex justify-center items-center rounded-xl border border-white/25 px-5 py-3 text-sm font-medium text-white hover:bg-white/10 transition">
                            Try Demo First
                        </a>
                    @endauth
                </div>
            </div>

            <div class="mt-8 border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-blue-100/70">
                <span>© {{ date('Y') }} {{ config('app.name', 'Mustey Digital Academy') }}. All rights reserved.</span>
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                    <a href="{{ url('/courses') }}" class="hover:text-white transition">Courses</a>
                    <a href="{{ route('contact') }}" class="hover:text-white transition">Contact</a>
                </div>
            </div>
        </div>
    </section>

</body>
</html>