<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Visitor Pass</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #001f54 0%, #003d7a 50%, #0056b3 100%);
            color: #ffffff;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            text-align: center;
            margin: -30px -30px 30px -30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .pass-code {
            background: linear-gradient(135deg, #F7A923 0%, #E6940F 100%);
            color: #2C3E50;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 20px 0;
            font-family: monospace;
        }
        .qr-code {
            text-align: center;
            margin: 30px 0;
        }
        .qr-code img {
            max-width: 300px;
            border: 3px solid #F7A923;
            border-radius: 10px;
            padding: 10px;
            background: white;
        }
        .details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #001f54;
        }
        .detail-value {
            color: #333;
        }
        .instructions {
            background-color: #e7f3ff;
            border-left: 4px solid #0056b3;
            padding: 15px;
            margin: 20px 0;
        }
        .instructions h3 {
            margin-top: 0;
            color: #001f54;
        }
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .container {
                padding: 20px;
            }
            .pass-code {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎫 Your Visitor Pass</h1>
            <p style="margin: 5px 0 0 0;">SOLIERA Visitor Management System</p>
        </div>

        <p>Dear <strong>{{ $visitor->name }}</strong>,</p>

        <p>Your visitor pass has been approved! Please use the QR code and pass code below to check in at our facility.</p>

        <div class="pass-code">
            {{ $qrPass->pass_code }}
        </div>

        <div class="qr-code">
            <img src="{{ $qrCodeUrl }}" alt="QR Code Pass">
            <p style="margin-top: 10px; color: #6c757d; font-size: 14px;">
                <strong>Scan this QR code at the entrance</strong>
            </p>
        </div>

        <div class="details">
            <h3 style="margin-top: 0; color: #001f54;">Visit Details</h3>
            
            <div class="detail-row">
                <span class="detail-label">Visitor Name:</span>
                <span class="detail-value">{{ $visitor->name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Purpose:</span>
                <span class="detail-value">{{ $visitor->purpose ?? 'Not specified' }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Host:</span>
                <span class="detail-value">{{ $visitor->host_employee ?? 'Not specified' }}</span>
            </div>
            
            @if($visitor->department)
            <div class="detail-row">
                <span class="detail-label">Department:</span>
                <span class="detail-value">{{ $visitor->department }}</span>
            </div>
            @endif
            
            <div class="detail-row">
                <span class="detail-label">Valid From:</span>
                <span class="detail-value">{{ $qrPass->valid_from->format('M d, Y H:i A') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Valid Until:</span>
                <span class="detail-value">{{ $qrPass->valid_until->format('M d, Y H:i A') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Pass Status:</span>
                <span class="detail-value" style="color: #28a745; font-weight: bold;">✓ ACTIVE</span>
            </div>
        </div>

        <div class="instructions">
            <h3>📋 Check-in Instructions</h3>
            <ol style="margin: 10px 0; padding-left: 20px;">
                <li>Present this QR code or pass code at the entrance</li>
                <li>Security personnel will scan your pass</li>
                <li>You may be asked to show your physical ID for verification</li>
                <li>Proceed to the reception or your host's location</li>
            </ol>
        </div>

        <div class="warning">
            <strong>⚠️ Important:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>This pass is valid only for the dates and times specified above</li>
                <li>Do not share this pass with others</li>
                <li>Bring a valid government-issued ID for verification</li>
                <li>Follow all facility rules and regulations</li>
            </ul>
        </div>

        @if($visitor->special_requirements)
        <div style="background-color: #e7f3ff; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <strong>Special Requirements:</strong>
            <p style="margin: 5px 0 0 0;">{{ $visitor->special_requirements }}</p>
        </div>
        @endif

        <p style="margin-top: 30px;">If you have any questions or need to modify your visit, please contact us at <a href="mailto:info@soliera.com">info@soliera.com</a>.</p>

        <p>We look forward to welcoming you!</p>

        <p style="margin-top: 20px;">
            Best regards,<br>
            <strong>SOLIERA Team</strong>
        </p>

        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>© {{ date('Y') }} SOLIERA. All rights reserved.</p>
            <p style="margin-top: 10px; font-size: 11px;">
                Pass Code: {{ $qrPass->pass_code }} | Generated: {{ $qrPass->created_at->format('M d, Y H:i A') }}
            </p>
        </div>
    </div>
</body>
</html>



