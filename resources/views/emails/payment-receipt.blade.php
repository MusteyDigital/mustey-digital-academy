<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <div style="max-width: 640px; margin: 0 auto; padding: 24px;">
        <h2 style="margin-bottom: 8px;">Payment Successful</h2>

        <p>Hello {{ $payment->user->name ?? 'Student' }},</p>

        <p>
            Your payment was received successfully and your enrollment has been activated.
        </p>

        <div style="border: 1px solid #d1d5db; border-radius: 8px; padding: 16px; margin: 20px 0;">
            <p style="margin: 0 0 8px;"><strong>Course:</strong> {{ $payment->course->title ?? '—' }}</p>
            <p style="margin: 0 0 8px;"><strong>Amount:</strong> ₦{{ number_format($payment->amount) }}</p>
            <p style="margin: 0 0 8px;"><strong>Reference:</strong> {{ $payment->reference }}</p>
            <p style="margin: 0 0 8px;"><strong>Status:</strong> {{ strtoupper($payment->status) }}</p>
            <p style="margin: 0;"><strong>Date:</strong> {{ $payment->paid_at ? $payment->paid_at->format('M j, Y g:i A') : $payment->created_at->format('M j, Y g:i A') }}</p>
        </div>

        <p>
            You can access your receipt here:
        </p>

        <p>
            <a href="{{ route('payments.receipt', $payment->id) }}"
               style="display:inline-block;padding:10px 16px;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:6px;">
                View Receipt
            </a>
        </p>

        <p>
            You can now continue learning on Mustey Digital Academy.
        </p>

        <p style="margin-top: 24px;">
            Regards,<br>
            <strong>Mustey Digital Academy</strong>
        </p>
    </div>
</body>
</html>
