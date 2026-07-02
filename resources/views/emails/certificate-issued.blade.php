@component('mail::message')
# Certificate Issued

Hello {{ $user->name }},

Congratulations! Your certificate for **{{ $course->title }}** has been issued successfully.

@component('mail::panel')
**Serial:** {{ $certificate->serial }}
@endcomponent

@component('mail::button', ['url' => $downloadUrl])
Download Certificate
@endcomponent

@component('mail::button', ['url' => $verifyUrl, 'color' => 'gray'])
Verify Certificate
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent