<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Coupon;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Mail\PaymentReceiptMail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function initialize(Request $request, Course $course)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            abort(403);
        }

        $alreadyEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($alreadyEnrolled) {
            return redirect()->route('courses.show', $course->id)
                ->with('success', 'You are already enrolled in this course.');
        }

        if ((int) $course->price <= 0) {
            Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                ['status' => 'enrolled']
            );

            return redirect()->route('courses.show', $course->id)
                ->with('success', 'Enrollment successful.');
        }

        $couponCode = strtoupper(trim((string) $request->input('coupon_code', '')));
        $coupon = null;
        $originalAmount = (int) $course->price;
        $discountAmount = 0;
        $finalAmount = $originalAmount;

        if ($couponCode !== '') {
            $coupon = Coupon::whereRaw('UPPER(code) = ?', [$couponCode])->first();

            if (!$coupon || !$coupon->isValid()) {
                return redirect()->route('courses.show', $course->id)
                    ->with('error', 'Invalid or expired coupon code.');
            }

            $discountAmount = $coupon->discountAmount($originalAmount);
            $finalAmount = max(0, $originalAmount - $discountAmount);
        }

        if ($finalAmount <= 0) {
            Enrollment::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                ['status' => 'enrolled']
            );

            return redirect()->route('courses.show', $course->id)
                ->with('success', 'Coupon applied successfully. Enrollment activated.');
        }

        $reference = 'MDA-' . $course->id . '-' . $user->id . '-' . Str::upper(Str::random(10));

        Payment::updateOrCreate(
            ['reference' => $reference],
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount' => $finalAmount,
                'currency' => 'NGN',
                'gateway' => 'paystack',
                'status' => 'pending',
                'gateway_response' => [
                    'original_amount' => $originalAmount,
                    'discount_amount' => $discountAmount,
                    'coupon_code' => $coupon?->code,
                ],
            ]
        );

        $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))
            ->acceptJson()
            ->post(rtrim(env('PAYSTACK_PAYMENT_URL'), '/') . '/transaction/initialize', [
                'email' => $user->email,
                'amount' => $finalAmount * 100,
                'reference' => $reference,
                'callback_url' => env('PAYSTACK_CALLBACK_URL'),
                'metadata' => [
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                    'coupon_code' => $coupon?->code,
                    'original_amount' => $originalAmount,
                    'discount_amount' => $discountAmount,
                    'final_amount' => $finalAmount,
                ],
            ]);

        if (!$response->successful() || !data_get($response->json(), 'status')) {
            Payment::where('reference', $reference)->update([
                'status' => 'failed',
                'gateway_status' => data_get($response->json(), 'message'),
                'gateway_response' => $response->json(),
            ]);
            return redirect()->route('courses.show', $course->id)
                ->with('error', 'Unable to initialize payment right now.');
        }

        $authorizationUrl = data_get($response->json(), 'data.authorization_url');

        return redirect()->away($authorizationUrl);
    }

    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('courses.index')
                ->with('error', 'Missing payment reference.');
        }

        $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))
            ->acceptJson()
            ->get(rtrim(env('PAYSTACK_PAYMENT_URL'), '/') . '/transaction/verify/' . urlencode($reference));

        if (!$response->successful() || !data_get($response->json(), 'status')) {
            return redirect()->route('courses.index')
                ->with('error', 'Payment verification failed.');
        }

        $paymentData = data_get($response->json(), 'data', []);

        if (data_get($paymentData, 'status') !== 'success') {
            return redirect()->route('courses.index')
                ->with('error', 'Payment was not successful.');
        }

        $courseId = data_get($paymentData, 'metadata.course_id');
        $userId = data_get($paymentData, 'metadata.user_id');

        if (!$courseId || !$userId) {
            return redirect()->route('courses.index')
                ->with('error', 'Payment metadata missing.');
        }

        $payment = Payment::updateOrCreate(
            ['reference' => $reference],
            [
                'user_id' => $userId,
                'course_id' => $courseId,
                'amount' => (int) (data_get($paymentData, 'amount', 0) / 100),
                'currency' => data_get($paymentData, 'currency', 'NGN'),
                'gateway' => 'paystack',
                'gateway_status' => data_get($paymentData, 'status'),
                'paid_at_gateway_reference' => data_get($paymentData, 'reference'),
                'gateway_response' => $paymentData,
                'status' => 'success',
                'paid_at' => now(),
            ]
        );

        Enrollment::firstOrCreate(
            [
                'user_id' => $userId,
                'course_id' => $courseId,
            ],
            [
                'status' => 'enrolled',
            ]
        );

        try {
            Mail::to($payment->user->email)->send(new PaymentReceiptMail($payment));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('courses.show', $courseId)
            ->with('success', 'Payment successful. You are now enrolled.');
    }

    public function receipt(Payment $payment)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if ($user->role !== 'admin' && $payment->user_id !== $user->id) {
            abort(403);
        }

        $payment->load(['user', 'course']);

        return view('payments.receipt', compact('payment'));
    }

    public function receiptPdf(Payment $payment)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if ($user->role !== 'admin' && $payment->user_id !== $user->id) {
            abort(403);
        }

        $payment->load(['user', 'course']);

        $pdf = Pdf::loadView('payments.receipt-pdf', compact('payment'))
            ->setPaper('a4', 'portrait');

        $filename = 'receipt-' . $payment->reference . '.pdf';

        return $pdf->download($filename);
    }
}
