<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit Request Declined</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #dc3545;
            margin: 0;
        }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .declined-notice {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
            color: #721c24;
        }
        .contact-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ Visit Request Declined</h1>
            <p>We regret to inform you that your visit request has been declined</p>
        </div>

        <div class="declined-notice">
            <h3>⚠️ Request Status: DECLINED</h3>
            <p>Unfortunately, we are unable to accommodate your visit request at this time. This decision may be due to scheduling conflicts, capacity limitations, or other operational considerations.</p>
        </div>

        <div class="info-box">
            <h3>Visit Details</h3>
            <p><strong>Name:</strong> {{ $visitor->name }}</p>
            <p><strong>Email:</strong> {{ $visitor->email }}</p>
            <p><strong>Company:</strong> {{ $visitor->company ?? 'N/A' }}</p>
            <p><strong>Purpose:</strong> {{ $visitor->purpose }}</p>
            <p><strong>Host:</strong> {{ $visitor->host_employee }}</p>
            <p><strong>Requested Date:</strong> {{ \Carbon\Carbon::parse($visitor->scheduled_date)->format('l, F j, Y') }}</p>
            <p><strong>Requested Time:</strong> {{ \Carbon\Carbon::parse($visitor->scheduled_time)->format('g:i A') }}</p>
        </div>

        <div class="contact-info">
            <h3>📞 Next Steps</h3>
            <p>If you believe this is an error or would like to discuss alternative arrangements, please contact us:</p>
            <ul>
                <li><strong>Email:</strong> admin@company.com</li>
                <li><strong>Phone:</strong> (555) 123-4567</li>
                <li><strong>Hours:</strong> Monday - Friday, 9:00 AM - 5:00 PM</li>
            </ul>
            <p>You may also submit a new visit request for a different date or time.</p>
        </div>

        <div class="info-box">
            <h3>Alternative Options</h3>
            <ul>
                <li>Submit a new visit request for a different date</li>
                <li>Contact your host directly to discuss alternative arrangements</li>
                <li>Consider a virtual meeting if applicable</li>
                <li>Check our visitor portal for available time slots</li>
            </ul>
        </div>

        <div class="footer">
            <p>We apologize for any inconvenience this may cause.</p>
            <p>Thank you for your understanding.</p>
            <p><small>This is an automated message. Please do not reply to this email.</small></p>
        </div>
    </div>
</body>
</html>