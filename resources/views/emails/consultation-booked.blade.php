<x-mail::message>
# New Consultation Booking

{{ $consultation->name }} has submitted a consultation booking on **{{ config('app.name') }}**.

<x-mail::panel>
**Name:** {{ $consultation->name }}

**Email:** {{ $consultation->email }}

**Phone:** {{ $consultation->phone_no }}

**Subject:** {{ $consultation->subject }}

**Message:**  
{{ $consultation->message }}
</x-mail::panel>

You can reply directly to this email to contact the requester.
</x-mail::message>
