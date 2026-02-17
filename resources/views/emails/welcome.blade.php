@component('mail::message')
# Welcome, {{ $user->name }} 🎉

Thanks for signing up on **Nexdus Academy x Mustey Digital Academy**. Welcome aboard to Kebbi State Tech Bootcamp 2026.

@component('mail::button', ['url' => config('app.url')])
Go to Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
