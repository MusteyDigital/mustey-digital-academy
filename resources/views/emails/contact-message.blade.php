@component('mail::message')
# New Contact Message

**From:** {{ $contactMessage->name }} ({{ $contactMessage->email }})

@if($contactMessage->subject)
**Subject:** {{ $contactMessage->subject }}
@endif

@component('mail::panel')
{{ $contactMessage->message }}
@endcomponent

@component('mail::button', ['url' => route('admin.contact-messages.show', $contactMessage->id)])
View Message
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent