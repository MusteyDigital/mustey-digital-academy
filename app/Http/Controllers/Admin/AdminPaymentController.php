<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminPaymentController extends Controller
{
    protected function filteredPaymentsQuery(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $courseId = trim((string) $request->query('course_id', ''));

        return Payment::with(['user', 'course'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('reference', 'like', "%{$q}%")
                        ->orWhereHas('user', function ($u) use ($q) {
                            $u->where('name', 'like', "%{$q}%")
                              ->orWhere('email', 'like', "%{$q}%");
                        })
                        ->orWhereHas('course', function ($c) use ($q) {
                            $c->where('title', 'like', "%{$q}%");
                        });
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($courseId !== '', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            });
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $courseId = trim((string) $request->query('course_id', ''));

        $payments = $this->filteredPaymentsQuery($request)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $totalSuccessfulRevenue = Payment::where('status', 'success')->sum('amount');
        $totalPayments = Payment::count();
        $successfulPayments = Payment::where('status', 'success')->count();
        $failedPayments = Payment::where('status', 'failed')->count();

        $courses = \App\Models\Course::orderBy('title')->get(['id', 'title']);

        return view('admin.payments.index', compact(
            'payments',
            'q',
            'status',
            'courseId',
            'courses',
            'totalSuccessfulRevenue',
            'totalPayments',
            'successfulPayments',
            'failedPayments'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $filename = 'payments-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($request) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Student Name',
                'Student Email',
                'Course',
                'Amount',
                'Currency',
                'Status',
                'Gateway',
                'Reference',
                'Paid At',
                'Created At',
            ]);

            $this->filteredPaymentsQuery($request)
                ->latest()
                ->chunk(200, function ($payments) use ($handle) {
                    foreach ($payments as $payment) {
                        fputcsv($handle, [
                            $payment->id,
                            optional($payment->user)->name,
                            optional($payment->user)->email,
                            optional($payment->course)->title,
                            $payment->amount,
                            $payment->currency,
                            $payment->status,
                            $payment->gateway,
                            $payment->reference,
                            optional($payment->paid_at)?->format('Y-m-d H:i:s'),
                            optional($payment->created_at)?->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
