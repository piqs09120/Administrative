@component('mail::message')

# Thank you for visiting {{ config('app.name') }}

Hello {{ $visitor->name }},

We hope you had a pleasant experience. This is a quick summary of your visit.

@component('mail::panel')
- Visitor: {{ $visitor->name }}
- Company/Organization: {{ $visitor->company ?? 'N/A' }}
- Purpose: {{ $visitor->purpose ?? 'N/A' }}
- Facility/Department: {{ optional($visitor->facility)->name ?? ($visitor->department ?? 'N/A') }}
- Check-in Time: {{ $visitor->time_in ? \Carbon\Carbon::parse($visitor->time_in)->format('M d, Y h:i A') : 'N/A' }}
- Check-out Time: {{ $visitor->time_out ? \Carbon\Carbon::parse($visitor->time_out)->format('M d, Y h:i A') : 'N/A' }}
@if($visitor->expected_date_out)
- Expected Date Out: {{ \Carbon\Carbon::parse($visitor->expected_date_out)->format('M d, Y') }}
@endif
@if($visitor->expected_time_out)
- Expected Time Out: {{ \Carbon\Carbon::parse($visitor->expected_time_out)->format('h:i A') }}
@endif
- Duration: @php
    if ($visitor->time_in && $visitor->time_out) {
        $diff = \Carbon\Carbon::parse($visitor->time_in)->diff(\Carbon\Carbon::parse($visitor->time_out));
        echo sprintf('%dh %dm', $diff->h + ($diff->d * 24), $diff->i);
    } else {
        echo '—';
    }
@endphp
@endcomponent

@component('mail::button', ['url' => url('/visitor')])
View Visitor Portal
@endcomponent

If you have any questions or feedback, just reply to this email.

Thanks,
{{ config('app.name') }} Team

@endcomponent

