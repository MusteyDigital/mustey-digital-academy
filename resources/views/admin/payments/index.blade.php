<x-layouts.admin>
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white shadow-sm sm:rounded-lg p-5 border">
            <p class="text-xs uppercase tracking-widest text-gray-500">Total Payments</p>
            <p class="text-3xl font-bold mt-2">{{ $totalPayments }}</p>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-5 border">
            <p class="text-xs uppercase tracking-widest text-gray-500">Successful Payments</p>
            <p class="text-3xl font-bold mt-2">{{ $successfulPayments }}</p>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-5 border">
            <p class="text-xs uppercase tracking-widest text-gray-500">Failed Payments</p>
            <p class="text-3xl font-bold mt-2">{{ $failedPayments }}</p>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-5 border">
            <p class="text-xs uppercase tracking-widest text-gray-500">Total Revenue</p>
            <p class="text-3xl font-bold mt-2">₦{{ number_format($totalSuccessfulRevenue) }}</p>
        </div>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg p-6 border mt-6">
        <h3 class="font-semibold text-gray-800 text-lg mb-4">Filter Payments</h3>

        <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Search</label>
                <input type="text" name="q" value="{{ $q }}"
                       placeholder="Student, email, reference, course"
                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="success" {{ $status === 'success' ? 'selected' : '' }}>Success</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div>
                <label class="block text-sm text-gray-600 mb-1">Course</label>
                <select name="course_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ (string) $courseId === (string) $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Apply
                </button>
                <a href="{{ route('admin.payments.index') }}"
                   class="inline-flex items-center px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg p-6 border mt-6">
        <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
            <h3 class="font-semibold text-gray-800 text-lg">Payments</h3>

            <a href="{{ route('admin.payments.export.csv', request()->query()) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Export CSV
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left p-3">Student</th>
                        <th class="text-left p-3">Course</th>
                        <th class="text-left p-3">Amount</th>
                        <th class="text-left p-3">Status</th>
                        <th class="text-left p-3">Reference</th>
                        <th class="text-left p-3">Date</th>
                        <th class="text-left p-3">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr class="border-b">
                            <td class="p-3">
                                <div class="font-semibold text-gray-900">{{ $payment->user->name ?? '—' }}</div>
                                <div class="text-gray-500">{{ $payment->user->email ?? '—' }}</div>
                            </td>
                            <td class="p-3">{{ $payment->course->title ?? '—' }}</td>
                            <td class="p-3 font-semibold">₦{{ number_format($payment->amount) }}</td>
                            <td class="p-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $payment->status === 'success' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $payment->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ strtoupper($payment->status) }}
                                </span>
                            </td>
                            <td class="p-3 break-all">{{ $payment->reference }}</td>
                            <td class="p-3">
                                {{ $payment->paid_at ? $payment->paid_at->format('M j, Y g:i A') : $payment->created_at->format('M j, Y g:i A') }}
                            </td>
                            <td class="p-3">
                                <a href="{{ route('payments.receipt', $payment->id) }}"
                                   class="underline text-blue-600">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-gray-600">No payments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $payments->links() }}
        </div>
    </div>
</x-layouts.admin>
