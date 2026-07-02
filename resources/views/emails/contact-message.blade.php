@component('mail::message')
# New Contact Message

**From:** {{ $contactMessage->name }} ({{ $contactMessage->email }})

@if($contactMessage->subject)
**Subject:** {{ $contactMessage->subject }}
@endif

{{ $contactMessage->message }}

This message was saved to your contact_messages table (ID #{{ $contactMessage->id }}).

Thanks,<br>
{{ config('app.name') }}
@endcomponent
