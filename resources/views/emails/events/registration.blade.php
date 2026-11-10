@component('mail::message')
@if($isAdmin)
# New Event Registration

A user has successfully registered for an event.

**Registration ID:** {{ $registration->id }}  
**Payment Status:** {{ ucfirst($registration->payment_status) }}  
**PayPal Order ID:** {{ $registration->paypal_order_id ?? 'N/A' }}

{{-- @component('mail::button', ['url' => url('/admin/event-registrations/'.$registration->id)])
View Registration
@endcomponent --}}

Thanks,  
{{ config('app.name') }}

@else
# Event Registration Confirmed 🎉

Hi {{ Auth::user()->name }},

Your registration for the event has been **successfully confirmed and paid!**

**Registration ID:** {{ $registration->id }}  
**Payment Status:** {{ ucfirst($registration->payment_status) }}  
**PayPal Order ID:** {{ $registration->paypal_order_id ?? 'N/A' }}

@component('mail::button', ['url' => url('/my-registrations/'.$registration->id)])
View Your Registration
@endcomponent

We look forward to seeing you at the event!

Thanks,  
{{ config('app.name') }}
@endif
@endcomponent
