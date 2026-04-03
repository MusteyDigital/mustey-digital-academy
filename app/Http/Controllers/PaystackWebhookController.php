<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $signature = $request->header('x-paystack-signature');

        $computed = hash_hmac(
            'sha512',
            $request->getContent(),
            env('PAYSTACK_SECRET_KEY')
        );

        if (!$signature || !hash_equals($computed, $signature)) {
            Log::warning('Paystack webhook rejected: invalid signature.');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $event = $request->input('event');
        $data = $request->input('data', []);

        Log::info('Paystack webhook received', [
            'event' => $event,
            'reference' => data_get($data, 'reference'),
        ]);

        if ($event === 'charge.success') {
            $reference = data_get($data, 'reference');
            $courseId = data_get($data, 'metadata.course_id');
            $userId = data_get($data, 'metadata.user_id');

            if ($reference && $courseId && $userId) {
                $payment = Payment::updateOrCreate(
                    ['reference' => $reference],
                    [
                        'user_id' => $userId,
                        'course_id' => $courseId,
                        'amount' => (int) (data_get($data, 'amount', 0) / 100),
                        'currency' => data_get($data, 'currency', 'NGN'),
                        'gateway' => 'paystack',
                        'gateway_status' => data_get($data, 'status'),
                        'paid_at_gateway_reference' => data_get($data, 'reference'),
                        'gateway_response' => $data,
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

                Log::info('Paystack webhook processed successfully', [
                    'payment_id' => $payment->id,
                    'reference' => $reference,
                    'user_id' => $userId,
                    'course_id' => $courseId,
                ]);
            }
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
