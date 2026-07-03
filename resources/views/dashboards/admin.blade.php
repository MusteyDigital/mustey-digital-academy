<x-layouts.admin>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 font-medium">Total Users</p>
            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $totalUsers }}</p>
            <a class="inline-flex items-center gap-1 text-blue-600 text-sm font-medium mt-3 hover:text-blue-700 transition"
               href="{{ route('admin.users.index') }}">
                Manage Users
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 font-medium">Students</p>
            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $totalStudents }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 font-medium">Instructors</p>
            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $totalInstructors }}</p>
        </div>

        @isset($totalAdmins)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <p class="text-xs uppercase tracking-wide text-slate-500 font-medium">Admins</p>
                <p class="text-3xl font-bold text-slate-800 mt-2">{{ $totalAdmins }}</p>
            </div>
        @endisset

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 font-medium">Courses</p>
            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $totalCourses }}</p>
            <a class="inline-flex items-center gap-1 text-blue-600 text-sm font-medium mt-3 hover:text-blue-700 transition"
               href="{{ route('admin.courses.index') }}">
                View Courses
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 font-medium">Enrollments</p>
            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $totalEnrollments }}</p>
            <a class="inline-flex items-center gap-1 text-blue-600 text-sm font-medium mt-3 hover:text-blue-700 transition"
               href="{{ route('admin.enrollments.index') }}">
                View Enrollments
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 font-medium">Certificates</p>
            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $totalCertificates }}</p>
            <a class="inline-flex items-center gap-1 text-blue-600 text-sm font-medium mt-3 hover:text-blue-700 transition"
               href="{{ route('admin.certificates.index') }}">
                View Certificates
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 font-medium">Successful Payments</p>
            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $successfulPaymentsCount }}</p>
        </div>

        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-2xl shadow-sm p-5 text-white">
            <p class="text-xs uppercase tracking-wide text-green-100 font-medium">Total Revenue</p>
            <p class="text-3xl font-bold mt-2">₦{{ number_format($totalRevenue) }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 font-medium">Today Revenue</p>
            <p class="text-3xl font-bold text-slate-800 mt-2">₦{{ number_format($todayRevenue) }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 font-medium">This Month Revenue</p>
            <p class="text-3xl font-bold text-slate-800 mt-2">₦{{ number_format($monthRevenue) }}</p>
        </div>

    </div>

    {{-- Quick Links --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mt-4">
        <h3 class="font-semibold text-slate-800 text-lg">Quick Links</h3>

        <div class="mt-4 flex flex-wrap gap-2">
            @php
                $quickLinks = [
                    ['route' => 'admin.users.index', 'label' => 'Users', 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8zm6 0a4 4 0 100-8 4 4 0 000 8z'],
                    ['route' => 'admin.courses.index', 'label' => 'Courses', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    ['route' => 'admin.enrollments.index', 'label' => 'Enrollments', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['route' => 'admin.payments.index', 'label' => 'Payments', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ['route' => 'admin.coupons.index', 'label' => 'Coupons', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
                    ['route' => 'admin.attendance.lessons', 'label' => 'Lesson Attendance', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['route' => 'admin.attendance.live', 'label' => 'Live Attendance', 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                    ['route' => 'admin.certificates.index', 'label' => 'Certificates', 'icon' => 'M12 15a4 4 0 100-8 4 4 0 000 8zm0 0v6m-4-2.5L6 21m10-2.5l2 2.5'],
                ];
            @endphp

            @foreach($quickLinks as $link)
                <a class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition"
                   href="{{ route($link['route']) }}">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}" />
                    </svg>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="font-semibold text-slate-800 text-lg">Top Courses by Revenue</h3>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="text-left py-2 px-3 text-xs uppercase tracking-wide text-slate-500 font-medium">Course</th>
                            <th class="text-left py-2 px-3 text-xs uppercase tracking-wide text-slate-500 font-medium">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenueByCourse as $row)
                            <tr class="border-b border-slate-100 last:border-b-0">
                                <td class="py-3 px-3 text-slate-700">
                                    {{ $topRevenueCourses[$row->course_id]->title ?? '—' }}
                                </td>
                                <td class="py-3 px-3 font-semibold text-slate-800">
                                    ₦{{ number_format($row->total_revenue) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-6 px-3 text-slate-500 text-center">No revenue yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="font-semibold text-slate-800 text-lg mb-3">Recent Payments</h3>

            @forelse($recentPayments as $payment)
                <div class="border-b border-slate-100 last:border-b-0 py-3 text-sm">
                    <div class="font-semibold text-slate-800">
                        {{ $payment->user->name ?? 'Student' }}
                    </div>
                    <div class="text-slate-600">
                        {{ $payment->course->title ?? 'Course' }}
                    </div>
                    <div class="text-slate-500 mt-0.5">
                        ₦{{ number_format($payment->amount) }} ·
                        <span class="font-medium {{ strtolower($payment->status) === 'success' ? 'text-green-600' : 'text-slate-500' }}">
                            {{ strtoupper($payment->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No recent payments yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Activity --}}
    @if(isset($recentUsers) || isset($recentCourses) || isset($recentCertificates))
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">

            @isset($recentUsers)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold text-slate-800">Recent Users</h3>
                        <a class="text-sm text-blue-600 font-medium hover:text-blue-700 transition" href="{{ route('admin.users.index') }}">View</a>
                    </div>

                    @forelse($recentUsers as $u)
                        <div class="flex justify-between text-sm py-1.5">
                            <span class="text-slate-700">{{ $u->name }}</span>
                            <span class="text-slate-500 font-medium">{{ strtoupper($u->role) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No recent users.</p>
                    @endforelse
                </div>
            @endisset

            @isset($recentCourses)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold text-slate-800">Recent Courses</h3>
                        <a class="text-sm text-blue-600 font-medium hover:text-blue-700 transition" href="{{ route('admin.courses.index') }}">View</a>
                    </div>

                    @forelse($recentCourses as $c)
                        <a class="block text-blue-600 text-sm font-medium py-1.5 hover:text-blue-700 transition"
                           href="{{ route('courses.show', $c->id) }}">
                            {{ $c->title }}
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">No recent courses.</p>
                    @endforelse
                </div>
            @endisset

            @isset($recentCertificates)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold text-slate-800">Recent Certificates</h3>
                        <a class="text-sm text-blue-600 font-medium hover:text-blue-700 transition" href="{{ route('admin.certificates.index') }}">View</a>
                    </div>

                    @forelse($recentCertificates as $cert)
                        <a class="block text-blue-600 text-sm font-medium py-1.5 hover:text-blue-700 transition"
                           href="{{ route('certificates.verify', $cert->serial) }}">
                            {{ $cert->serial }}
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">No recent certificates.</p>
                    @endforelse
                </div>
            @endisset

        </div>
    @endif

    {{-- Top Courses by Enrollments --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mt-4">
        <h3 class="font-semibold text-slate-800 text-lg">Top Courses by Enrollments</h3>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2 px-3 text-xs uppercase tracking-wide text-slate-500 font-medium">Course</th>
                        <th class="text-left py-2 px-3 text-xs uppercase tracking-wide text-slate-500 font-medium">Enrollments</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollmentsByCourse as $row)
                        <tr class="border-b border-slate-100 last:border-b-0">
                            <td class="py-3 px-3 text-slate-700">
                                {{ $topCourses[$row->course_id]->title ?? '—' }}
                            </td>
                            <td class="py-3 px-3 font-semibold text-slate-800">
                                {{ $row->total }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="py-6 px-3 text-slate-500 text-center">No enrollments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="font-semibold text-slate-800">Users (Last 7 Days)</h3>
            <div class="mt-4 h-56">
                <canvas id="usersChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="font-semibold text-slate-800">Enrollments (Last 7 Days)</h3>
            <div class="mt-4 h-56">
                <canvas id="enrollmentsChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 lg:col-span-2">
            <h3 class="font-semibold text-slate-800">Certificates (Last 7 Days)</h3>
            <div class="mt-4 h-56">
                <canvas id="certsChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const labels = @json($days);
            const usersData = @json($usersSeries);
            const enrollmentsData = @json($enrollmentsSeries);
            const certsData = @json($certsSeries);

            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } }
                }
            };

            new Chart(document.getElementById('usersChart'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Users',
                        data: usersData,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        tension: 0.35,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#2563eb',
                    }]
                },
                options: commonOptions,
            });

            new Chart(document.getElementById('enrollmentsChart'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Enrollments',
                        data: enrollmentsData,
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                    }]
                },
                options: commonOptions,
            });

            new Chart(document.getElementById('certsChart'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Certificates',
                        data: certsData,
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.1)',
                        tension: 0.35,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#16a34a',
                    }]
                },
                options: commonOptions,
            });
        })();
    </script>

</x-layouts.admin>