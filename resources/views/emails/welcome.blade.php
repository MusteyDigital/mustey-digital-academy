@component('mail::message')
# Welcome, {{ $user->name }} 🎉

Thanks for signing up on **Nexdus Academy x Mustey Digital Academy**.

@component('mail::button', ['url' => config('app.url')])
Go to Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
