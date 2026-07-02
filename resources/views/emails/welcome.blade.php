@component('mail::message')
# Welcome, {{ $user->name }}

Thanks for signing up on **{{ config('app.name') }}**. We're excited to have you on board.

@component('mail::button', ['url' => config('app.url')])
Go to Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent