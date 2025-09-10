@component('mail::message')

# Your Visit Has Been Approved ✅

Hello {{ $visitor->name }},

Great news! Your visit has been approved and you are now checked in.

@component('mail::panel')
- Pass Number: {{ $visitor->pass_id ?? '—' }}
- Visitor: {{ $visitor->name }}
- Company: {{ $visitor->company ?? 'N/A' }}
- Purpose: {{ $visitor->purpose ?? 'N/A' }}
- Facility/Department: {{ optional($visitor->facility)->name ?? ($visitor->department ?? 'N/A') }}
- Valid From: {{ $visitor->pass_valid_from ? $visitor->pass_valid_from->format('M d, Y h:i A') : '—' }}
- Valid Until: {{ $visitor->pass_valid_until ? $visitor->pass_valid_until->format('M d, Y h:i A') : '—' }}
@if($visitor->expected_date_out)
- Expected Date Out: {{ \Carbon\Carbon::parse($visitor->expected_date_out)->format('M d, Y') }}
@endif
@if($visitor->expected_time_out)
- Expected Time Out: {{ \Carbon\Carbon::parse($visitor->expected_time_out)->format('h:i A') }}
@endif
@endcomponent

@if($qr)
Scan this QR to verify your pass:

@component('mail::button', ['url' => $qr])
Open QR Image
@endcomponent
@endif

@component('mail::button', ['url' => url('/visitor')])
Open Visitor Portal
@endcomponent

We look forward to seeing you. Please present this email or your QR code at the entrance.

Thanks,
{{ config('app.name') }} Team

@endcomponent

