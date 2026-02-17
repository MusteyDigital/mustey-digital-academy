@component('mail::message')
# 🎉 Course Completed!

Hello **{{ $user->name }}**,  

Congratulations! You have successfully completed:

## ✅ {{ $course->title }}

@if($course->description)
{{ $course->description }}
@endif

Your certificate is ready.

@component('mail::button', ['url' => route('certificates.verify', $certificate->serial_code)])
Verify Certificate
@endcomponent

If you want to download directly from your dashboard, log into your account.

Thanks,  
**{{ config('app.name') }}**
@endcomponent
