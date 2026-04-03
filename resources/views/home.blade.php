{{-- resources/views/home.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Mustey Digital Academy') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white text-gray-900 overflow-x-hidden">
    {{-- Top Bar --}}
    <header class="border-b bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 sticky top-0 z-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 rounded-xl bg-gray-900 shrink-0"></div>
                    <div class="leading-tight min-w-0">
                        <div class="font-semibold truncate">{{ config('app.name', 'Mustey Digital Academy') }}</div>
                        <div class="text-xs text-gray-500 truncate">Learn • Build • Grow</div>
                    </div>
                </a>

                <nav class="hidden md:flex items-center gap-6 text-sm">
                    <a href="#featured" class="text-gray-600 hover:text-gray-900">Featured</a>
                    <a href="#courses" class="text-gray-600 hover:text-gray-900">Courses</a>
                    <a href="#features" class="text-gray-600 hover:text-gray-900">Why us</a>
                </nav>

                <div class="flex items-center gap-2 shrink-0">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                            Dashboard
                        </a>
                    @else

			<a href="{{ route('login') }}"
   			   class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
    			    Login
			</a>
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                            Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 sm:py-14 lg:py-20">
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div class="min-w-0">
                    <p class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                        🚀 Learn digital skills the right way
                    </p>

                    <h1 class="mt-4 text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight break-words">
                        Learn practical digital skills with <span class="text-gray-900">Mustey Digital Academy</span>
                    </h1>

                    <p class="mt-4 text-base sm:text-lg text-gray-600 max-w-xl">
                        Build job-ready skills in Data Analysis, Web Development, and more — with structured lessons,
                        quizzes, certificates, and progress tracking.
                    </p>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="#courses"
                           class="inline-flex justify-center items-center rounded-lg bg-gray-900 px-5 py-3 text-sm font-medium text-white hover:bg-gray-800">
                            Browse Courses
                        </a>

                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="inline-flex justify-center items-center rounded-lg border border-gray-200 px-5 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                               class="inline-flex justify-center items-center rounded-lg border border-gray-200 px-5 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Create Free Account
                            </a>
                        @endauth
                    </div>

                    <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 gap-4 max-w-lg">
                        <div class="rounded-xl border p-4">
                            <div class="text-xl font-semibold">Courses</div>
                            <div class="text-xs text-gray-500">Learn step-by-step</div>
                        </div>
                        <div class="rounded-xl border p-4">
                            <div class="text-xl font-semibold">Quizzes</div>
                            <div class="text-xs text-gray-500">Track progress</div>
                        </div>
                        <div class="rounded-xl border p-4 sm:col-auto col-span-2">
                            <div class="text-xl font-semibold">Certificates</div>
                            <div class="text-xs text-gray-500">Earn proof of learning</div>
                        </div>
                    </div>
                </div>

                {{-- Right side card --}}
                <div class="lg:justify-self-end w-full">
                    <div class="rounded-2xl border bg-white shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold">Top Tracks</div>
                            <span class="text-xs text-gray-500">Updated</span>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div class="rounded-xl border p-4">
                                <div class="font-medium">Data Analysis</div>
                                <div class="text-sm text-gray-600">Excel • Power BI • Projects</div>
                            </div>
                            <div class="rounded-xl border p-4">
                                <div class="font-medium">Web Development</div>
                                <div class="text-sm text-gray-600">HTML • CSS • JS • Laravel</div>
                            </div>
                            <div class="rounded-xl border p-4">
                                <div class="font-medium">Digital Literacy</div>
                                <div class="text-sm text-gray-600">Productivity • Internet Safety</div>
                            </div>
                        </div>

                        <div class="mt-6 rounded-xl bg-gray-50 p-4 text-sm text-gray-700">
                            Tip: Create an account, then enroll from your dashboard.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured --}}
    <section id="featured" class="bg-gray-50 border-y">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold">Featured Courses</h2>
                    <p class="mt-2 text-gray-600">Hand-picked courses to get you started fast.</p>
                </div>
            </div>

            <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($featuredCourses as $course)
                    @include('partials.course-card', ['course' => $course])
                @empty
                    <div class="rounded-2xl border bg-white p-6 text-gray-700">
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
                    <h2 class="text-2xl font-bold">Latest Courses</h2>
                    <p class="mt-2 text-gray-600">Newest courses on the platform.</p>
                </div>
                <a href="{{ url('/courses') }}" class="text-sm font-medium text-gray-900 hover:underline">
                    View all →
                </a>
            </div>

            <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($latestCourses as $course)
                    @include('partials.course-card', ['course' => $course])
                @empty
                    <div class="rounded-2xl border bg-white p-6 text-gray-700">
                        No courses yet. Add a course from the instructor/admin dashboard.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="bg-gray-50 border-y">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-2xl font-bold">Why students love this platform</h2>
            <p class="mt-2 text-gray-600 max-w-2xl">
                Simple, fast, and structured — designed for learning on phone or PC.
            </p>

            <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="rounded-2xl bg-white border p-6">
                    <div class="font-semibold">Structured lessons</div>
                    <p class="mt-2 text-sm text-gray-600">Modules and lessons organized clearly for easy study.</p>
                </div>
                <div class="rounded-2xl bg-white border p-6">
                    <div class="font-semibold">Quizzes & tracking</div>
                    <p class="mt-2 text-sm text-gray-600">Assess learning and monitor progress automatically.</p>
                </div>
                <div class="rounded-2xl bg-white border p-6">
                    <div class="font-semibold">Certificates</div>
                    <p class="mt-2 text-sm text-gray-600">Reward completion with downloadable certificates.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-gray-900 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <div>
                    <h2 class="text-2xl font-bold">Ready to start learning?</h2>
                    <p class="mt-2 text-white/80 max-w-xl">
                        Create an account, enroll in a course, and begin your learning journey today.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 lg:justify-end">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="inline-flex justify-center items-center rounded-lg bg-white px-5 py-3 text-sm font-medium text-gray-900 hover:bg-gray-100">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="inline-flex justify-center items-center rounded-lg bg-white px-5 py-3 text-sm font-medium text-gray-900 hover:bg-gray-100">
                            Create Account
                        </a>
                        <a href="{{ route('login') }}"
                           class="inline-flex justify-center items-center rounded-lg border border-white/20 px-5 py-3 text-sm font-medium text-white hover:bg-white/10">
                            Login
                        </a>
                    @endauth
                </div>
            </div>

            <div class="mt-8 border-t border-white/10 pt-6 text-sm text-white/60">
                © {{ date('Y') }} {{ config('app.name', 'Mustey Digital Academy') }}. All rights reserved.
            </div>
        </div>
    </section>
</body>
</html>
