<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Approved Successfully</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 30px;
        }
        .success-icon {
            text-align: center;
            margin-bottom: 20px;
        }
        .success-icon .icon {
            width: 60px;
            height: 60px;
            background: #22c55e;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: white;
        }
        .request-details {
            background: #f0fdf4;
            border: 2px solid #22c55e;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #bbf7d0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #166534;
        }
        .detail-value {
            color: #14532d;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-approved {
            background: #dcfce7;
            color: #166534;
        }
        .badge-reservation {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-low {
            background: #dcfce7;
            color: #166534;
        }
        .badge-medium {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-high {
            background: #fecaca;
            color: #991b1b;
        }
        .badge-urgent {
            background: #fecaca;
            color: #991b1b;
        }
        .footer {
            background: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .cta-button {
            display: inline-block;
            background: #22c55e;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .cta-button:hover {
            background: #16a34a;
        }
        .approval-notice {
            background: #ecfdf5;
            border-left: 4px solid #22c55e;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Request Approved! 🎉</h1>
            <p>Your facility management request has been approved</p>
        </div>
        
        <div class="content">
            <div class="success-icon">
                <div class="icon">✓</div>
            </div>
            
            <h2 style="color: #166534; margin-bottom: 20px;">Congratulations {{ $request->contact_name }}!</h2>
            
            <p>Great news! Your facility management request has been reviewed and <strong>approved</strong> by our team. You can now proceed with your planned activities.</p>
            
            <div class="approval-notice">
                <strong>✅ Approved on:</strong> {{ now()->format('M d, Y \a\t h:i A') }}<br>
                <strong>📋 Status:</strong> Your request is now active and ready to use
            </div>
            
            <div class="request-details">
                <h3 style="margin-top: 0; color: #166534;">Approved Request Details</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Request ID:</span>
                    <span class="detail-value">#{{ $requestId }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Request Type:</span>
                    <span class="detail-value">
                        <span class="badge badge-reservation">{{ ucfirst(str_replace('_', ' ', $request->request_type)) }}</span>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Department:</span>
                    <span class="detail-value">{{ $request->department }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Priority:</span>
                    <span class="detail-value">
                        <span class="badge badge-{{ $request->priority }}">{{ ucfirst($request->priority) }}</span>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Location:</span>
                    <span class="detail-value">{{ $request->location }}</span>
                </div>
                
                @if($request->facility)
                <div class="detail-row">
                    <span class="detail-label">Facility:</span>
                    <span class="detail-value"><strong>{{ $request->facility->name }}</strong></span>
                </div>
                @endif
                
                <div class="detail-row">
                    <span class="detail-label">Requested Date & Time:</span>
                    <span class="detail-value"><strong>{{ $request->requested_datetime->format('M d, Y h:i A') }}</strong></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="badge badge-approved">✅ Approved</span>
                    </span>
                </div>
                
                @if($request->description)
                <div class="detail-row">
                    <span class="detail-label">Description:</span>
                    <span class="detail-value">{{ $request->description }}</span>
                </div>
                @endif
            </div>
            
            <h3 style="color: #166534;">Next Steps</h3>
            <ul style="color: #166534;">
                <li><strong>Access your facility:</strong> You can now use the requested facility at the specified date and time</li>
                <li><strong>Contact person:</strong> If you need assistance, contact our facilities team</li>
                <li><strong>Changes needed:</strong> If you need to modify your request, please contact us as soon as possible</li>
            </ul>
            
            <div style="text-align: center;">
                <a href="{{ url('/facility_reservations/new-request') }}" class="cta-button">View All Your Requests</a>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Soliera Hotel & Restaurant</strong></p>
            <p>Savor the stay. Dine in elegance.</p>
            <p>Thank you for choosing our facilities. Enjoy your experience!</p>
        </div>
    </div>
</body>
</html>
