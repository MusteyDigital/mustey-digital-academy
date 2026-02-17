<x-layouts.admin>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        <div class="bg-white shadow-sm sm:rounded-lg p-5 border">
            <p class="text-xs uppercase tracking-widest text-gray-500">Total Users</p>
            <p class="text-3xl font-bold mt-2">{{ $totalUsers }}</p>
            <a class="underline text-blue-600 text-sm mt-2 inline-block"
               href="{{ route('admin.users.index') }}">
                Manage Users →
            </a>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-5 border">
            <p class="text-xs uppercase tracking-widest text-gray-500">Students</p>
            <p class="text-3xl font-bold mt-2">{{ $totalStudents }}</p>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-5 border">
            <p class="text-xs uppercase tracking-widest text-gray-500">Instructors</p>
            <p class="text-3xl font-bold mt-2">{{ $totalInstructors }}</p>
        </div>

        @isset($totalAdmins)
            <div class="bg-white shadow-sm sm:rounded-lg p-5 border">
                <p class="text-xs uppercase tracking-widest text-gray-500">Admins</p>
                <p class="text-3xl font-bold mt-2">{{ $totalAdmins }}</p>
            </div>
        @endisset

        <div class="bg-white shadow-sm sm:rounded-lg p-5 border">
            <p class="text-xs uppercase tracking-widest text-gray-500">Courses</p>
            <p class="text-3xl font-bold mt-2">{{ $totalCourses }}</p>
            <a class="underline text-blue-600 text-sm mt-2 inline-block"
               href="{{ route('admin.courses.index') }}">
                View Courses →
            </a>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-5 border">
            <p class="text-xs uppercase tracking-widest text-gray-500">Enrollments</p>
            <p class="text-3xl font-bold mt-2">{{ $totalEnrollments }}</p>
            <a class="underline text-blue-600 text-sm mt-2 inline-block"
               href="{{ route('admin.enrollments.index') }}">
                View Enrollments →
            </a>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-5 border">
            <p class="text-xs uppercase tracking-widest text-gray-500">Certificates</p>
            <p class="text-3xl font-bold mt-2">{{ $totalCertificates }}</p>
            <a class="underline text-blue-600 text-sm mt-2 inline-block"
               href="{{ route('admin.certificates.index') }}">
                View Certificates →
            </a>
        </div>

    </div>

    {{-- Quick Links --}}
    <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
        <h3 class="font-semibold text-gray-800 text-lg">Quick Links</h3>

        <div class="mt-4 flex flex-wrap gap-2">
            <a class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-gray-50"
               href="{{ route('admin.users.index') }}">
                👥 Users
            </a>

            <a class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-gray-50"
               href="{{ route('admin.courses.index') }}">
                📚 Courses
            </a>

            <a class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-gray-50"
               href="{{ route('admin.enrollments.index') }}">
                🧾 Enrollments
            </a>

            <a class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-gray-50"
               href="{{ route('admin.attendance.lessons') }}">
                ✅ Lesson Attendance
            </a>

            <a class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-gray-50"
               href="{{ route('admin.attendance.live') }}">
                🎥 Live Attendance
            </a>

            <a class="inline-flex items-center rounded-md border px-4 py-2 text-sm hover:bg-gray-50"
               href="{{ route('admin.certificates.index') }}">
                🏅 Certificates
            </a>
        </div>
    </div>

    {{-- Recent Activity --}}
    @if(isset($recentUsers) || isset($recentCourses) || isset($recentCertificates))
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            @isset($recentUsers)
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
                    <div class="flex justify-between mb-3">
                        <h3 class="font-semibold text-gray-800">Recent Users</h3>
                        <a class="underline text-sm text-blue-600" href="{{ route('admin.users.index') }}">View</a>
                    </div>

                    @forelse($recentUsers as $u)
                        <div class="flex justify-between text-sm mb-2">
                            <span>{{ $u->name }}</span>
                            <span class="text-gray-500">{{ strtoupper($u->role) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600">No recent users.</p>
                    @endforelse
                </div>
            @endisset

            @isset($recentCourses)
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
                    <div class="flex justify-between mb-3">
                        <h3 class="font-semibold text-gray-800">Recent Courses</h3>
                        <a class="underline text-sm text-blue-600" href="{{ route('admin.courses.index') }}">View</a>
                    </div>

                    @forelse($recentCourses as $c)
                        <a class="block underline text-blue-600 text-sm mb-2"
                           href="{{ route('courses.show', $c->id) }}">
                            {{ $c->title }}
                        </a>
                    @empty
                        <p class="text-sm text-gray-600">No recent courses.</p>
                    @endforelse
                </div>
            @endisset

            @isset($recentCertificates)
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
                    <div class="flex justify-between mb-3">
                        <h3 class="font-semibold text-gray-800">Recent Certificates</h3>
                        <a class="underline text-sm text-blue-600" href="{{ route('admin.certificates.index') }}">View</a>
                    </div>

                    @forelse($recentCertificates as $cert)
                        <a class="block underline text-blue-600 text-sm mb-2"
                           href="{{ route('certificates.verify', $cert->serial) }}">
                            {{ $cert->serial }}
                        </a>
                    @empty
                        <p class="text-sm text-gray-600">No recent certificates.</p>
                    @endforelse
                </div>
            @endisset

        </div>
    @endif
        {{-- ✅ Top Courses by Enrollments (PLACED CORRECTLY OUTSIDE GRID) --}}
    <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
        <h3 class="font-semibold text-gray-800 text-lg">Top Courses by Enrollments</h3>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left p-3">Course</th>
                        <th class="text-left p-3">Enrollments</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollmentsByCourse as $row)
                        <tr class="border-b">
                            <td class="p-3">
                                {{ $topCourses[$row->course_id]->title ?? '—' }}
                            </td>
                            <td class="p-3 font-semibold">
                                {{ $row->total }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="p-4 text-gray-600">No enrollments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
        <h3 class="font-semibold text-gray-800">Users (Last 7 Days)</h3>
        <canvas id="usersChart" class="mt-4"></canvas>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg p-6 border">
        <h3 class="font-semibold text-gray-800">Enrollments (Last 7 Days)</h3>
        <canvas id="enrollmentsChart" class="mt-4"></canvas>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg p-6 border lg:col-span-2">
        <h3 class="font-semibold text-gray-800">Certificates (Last 7 Days)</h3>
        <canvas id="certsChart" class="mt-4"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = @json($days);

    const usersData = @json($usersSeries);
    const enrollmentsData = @json($enrollmentsSeries);
    const certsData = @json($certsSeries);

    new Chart(document.getElementById('usersChart'), {
        type: 'line',
        data: { labels, datasets: [{ label: 'Users', data: usersData, tension: 0.3 }] },
    });

    new Chart(document.getElementById('enrollmentsChart'), {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Enrollments', data: enrollmentsData }] },
    });

    new Chart(document.getElementById('certsChart'), {
        type: 'line',
        data: { labels, datasets: [{ label: 'Certificates', data: certsData, tension: 0.3 }] },
    });
</script>


</x-layouts.admin>
