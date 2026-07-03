<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Payment Receipt
            </h2>

            <a href="{{ url()->previous() }}"
               class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                <div class="text-center border-b border-slate-200 pb-4">
                    <h3 class="text-2xl font-bold text-slate-900">Mustey Digital Academy</h3>
                    <p class="text-sm text-slate-500 mt-1">Payment Receipt</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-slate-500">Student</div>
                        <div class="font-semibold text-slate-900">{{ $payment->user->name ?? '—' }}</div>
                        <div class="text-slate-600">{{ $payment->user->email ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="text-slate-500">Course</div>
                        <div class="font-semibold text-slate-900">{{ $payment->course->title ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="text-slate-500">Reference</div>
                        <div class="font-semibold text-slate-900 break-all">{{ $payment->reference }}</div>
                    </div>

                    <div>
                        <div class="text-slate-500">Gateway</div>
                        <div class="font-semibold text-slate-900 uppercase">{{ $payment->gateway ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="text-slate-500">Amount</div>
                        <div class="font-semibold text-slate-900">₦{{ number_format($payment->amount) }}</div>
                    </div>

                    <div>
                        <div class="text-slate-500">Status</div>
                        <div class="font-semibold text-green-700 uppercase">{{ $payment->status }}</div>
                    </div>

                    <div>
                        <div class="text-slate-500">Paid At</div>
                        <div class="font-semibold text-slate-900">
                            {{ $payment->paid_at ? $payment->paid_at->format('M j, Y g:i A') : ($payment->created_at?->format('M j, Y g:i A') ?? '—') }}
                        </div>
                    </div>

                    <div>
                        <div class="text-slate-500">Currency</div>
                        <div class="font-semibold text-slate-900 uppercase">{{ $payment->currency ?? '—' }}</div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button onclick="window.print()"
                            class="inline-flex items-center rounded-xl bg-blue-600 text-white px-4 py-2 text-sm font-medium hover:bg-blue-700 transition">
                        Print Receipt
                    </button>

                    <a href="{{ route('payments.receipt.pdf', $payment->id) }}"
                       class="inline-flex items-center rounded-xl bg-green-600 text-white px-4 py-2 text-sm font-medium hover:bg-green-700 transition">
                        Download PDF
                    </a>

                    <a href="{{ route('courses.show', $payment->course_id) }}"
                       class="inline-flex items-center rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                        Go to Course
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>