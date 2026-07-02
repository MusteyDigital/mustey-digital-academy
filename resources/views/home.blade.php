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
    {{-- Top Bar --}}
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
                    <a href="#featured" class="text-slate-600 hover:text-blue-600 transition">Featured</a>
                    <a href="#courses" class="text-slate-600 hover:text-blue-600 transition">Courses</a>
                    <a href="#features" class="text-slate-600 hover:text-blue-600 transition">Why us</a>
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3l7.5 7.5M6 9v10.5a1.5 1.5 0 001.5 1.5h3.75a.75.75 0 00.75-.75V15a.75.75 0 01.75-.75h1.5a.75.75 0 01.75.75v4.5a.75.75 0 00.75.75H18a1.5 1.5 0 001.5-1.5V9" />
                        </svg>
                        Learn digital skills the right way
                    </p>

                    <h1 class="mt-4 text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight break-words text-slate-900">
                        Learn practical digital skills with <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Mustey Digital Academy</span>
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
                            <a href="{{ route('register') }}"
                               class="inline-flex justify-center items-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                                Create Free Account
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
                            Tip: Create an account, then enroll from your dashboard.
                        </div>
                    </div>
                </div>
            </div>
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

    {{-- Courses --}}
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
                        <a href="{{ route('login') }}"
                           class="inline-flex justify-center items-center rounded-xl border border-white/25 px-5 py-3 text-sm font-medium text-white hover:bg-white/10 transition">
                            Login
                        </a>
                    @endauth
                </div>
            </div>

            <div class="mt-8 border-t border-white/10 pt-6 text-sm text-blue-100/70">
                © {{ date('Y') }} {{ config('app.name', 'Mustey Digital Academy') }}. All rights reserved.
            </div>
        </div>
    </section>
</body>
</html>