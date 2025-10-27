<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Visit Confirmation</title>
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
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .access-code {
            background-color: #007bff;
            color: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            letter-spacing: 2px;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .important {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
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
            <h1>🏢 Scheduled Visit Confirmation</h1>
            <p>Your visit has been successfully scheduled</p>
        </div>

        <div class="info-box">
            <h3>Visit Details</h3>
            <p><strong>Name:</strong> {{ $visitor->name }}</p>
            <p><strong>Email:</strong> {{ $visitor->email }}</p>
            <p><strong>Company:</strong> {{ $visitor->company ?? 'N/A' }}</p>
            <p><strong>Purpose:</strong> {{ $visitor->purpose }}</p>
            <p><strong>Host:</strong> {{ $visitor->host_employee }}</p>
            <p><strong>Scheduled Date:</strong> {{ \Carbon\Carbon::parse($visitor->scheduled_date)->format('l, F j, Y') }}</p>
            <p><strong>Scheduled Time:</strong> {{ \Carbon\Carbon::parse($visitor->scheduled_time)->format('g:i A') }}</p>
            <p><strong>Pass ID:</strong> {{ $visitor->pass_id }}</p>
        </div>

        <div class="access-code">
            <h3>Your Access Code</h3>
            <div style="font-size: 32px; letter-spacing: 3px;">{{ $accessCode }}</div>
            <p style="margin: 10px 0 0 0; font-size: 14px;">Use this code on your scheduled date</p>
        </div>

        <div class="qr-code">
            <h3>Digital QR Pass</h3>
            <p>Your QR code will be generated and sent separately, or you can access it through our visitor portal.</p>
            <p><strong>Pass Valid:</strong> {{ \Carbon\Carbon::parse($visitor->scheduled_date)->format('M j, Y') }} only</p>
        </div>

        <div class="important">
            <h3>⚠️ Important Instructions</h3>
            <ul>
                <li><strong>Date Restriction:</strong> Your pass and access code are only valid on {{ \Carbon\Carbon::parse($visitor->scheduled_date)->format('l, F j, Y') }}</li>
                <li><strong>Arrival Time:</strong> Please arrive at {{ \Carbon\Carbon::parse($visitor->scheduled_time)->format('g:i A') }}</li>
                <li><strong>Access Code:</strong> Present this code at the reception desk: <strong>{{ $accessCode }}</strong></li>
                <li><strong>QR Code:</strong> Show your QR code for quick check-in</li>
                <li><strong>Valid ID:</strong> Bring a valid photo ID that matches your registration</li>
            </ul>
        </div>

        <div class="info-box">
            <h3>What to Expect</h3>
            <ol>
                <li>Arrive at the main reception desk</li>
                <li>Present your access code and QR pass</li>
                <li>Show valid photo ID</li>
                <li>Receive your visitor badge</li>
                <li>Your host will be notified of your arrival</li>
            </ol>
        </div>

        <div class="footer">
            <p>If you need to reschedule or cancel your visit, please contact us immediately.</p>
            <p>Thank you for choosing our facility!</p>
            <p><small>This is an automated message. Please do not reply to this email.</small></p>
        </div>
    </div>
</body>
</html>
