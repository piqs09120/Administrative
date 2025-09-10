@component('mail::message')
# 🎉 Facility Request Completed Successfully!

Dear {{ $request->contact_name }},

Your facility request has been **completed** and the facility is now available for other reservations.

## 📋 Request Details

| Field | Value |
|-------|-------|
| **Request ID** | {{ $requestId }} |
| **Request Type** | {{ ucfirst($request->request_type) }} |
| **Department** | {{ $request->department }} |
| **Priority** | {{ ucfirst($request->priority) }} |
| **Location** | {{ $request->location }} |
| **Description** | {{ $request->description }} |
| **Requested Date & Time** | {{ \Carbon\Carbon::parse($request->requested_datetime)->format('M d, Y h:i A') }} |
@if($request->request_type === 'reservation' && $request->requested_end_datetime)
| **Until (End Date & Time)** | {{ \Carbon\Carbon::parse($request->requested_end_datetime)->format('M d, Y h:i A') }} |
@endif
| **Facility** | {{ $request->facility ? $request->facility->name : 'N/A' }} |
| **Status** | ✅ **Completed** |
| **Completed At** | {{ now()->format('M d, Y h:i A') }} |

## 🏢 Facility Information
@if($request->facility)
- **Facility Name**: {{ $request->facility->name }}
- **Facility Type**: {{ ucfirst($request->facility->type) }}
- **Capacity**: {{ $request->facility->capacity ?? 'N/A' }}
- **Status**: ✅ **Available** (Freed up for other reservations)
@else
- **Facility**: N/A (Equipment Request)
@endif

## 📧 Contact Information
- **Contact Name**: {{ $request->contact_name }}
- **Email**: {{ $request->contact_email }}
- **Submitted**: {{ $request->created_at->format('M d, Y h:i A') }}

---

### 🎯 What's Next?
The facility has been freed up and is now available for other reservations. If you need to make another request, please visit our [Facilities Reservation Portal]({{ url('/facilities-reservation') }}).

### 📞 Need Help?
If you have any questions or need assistance, please don't hesitate to contact our facilities team.

Thank you for using our facility reservation system!

Best regards,<br>
**Soliera Facilities Management Team**

---

*This is an automated message. Please do not reply to this email.*
@endcomponent
