<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Visit is Approved - Digital Pass</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f3f4f6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .pass-card {
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            margin: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .gradient-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #2563eb 100%);
            padding: 32px 24px;
            text-align: center;
            color: white;
        }
        .avatar-container {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            margin: 0 auto 12px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .avatar-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .visitor-name {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 8px;
            color: white;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            background-color: #10b981;
            color: white;
        }
        .details-section {
            padding: 24px;
            background-color: white;
        }
        .detail-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        .detail-icon {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            margin-top: 2px;
            color: #9ca3af;
        }
        .detail-content {
            flex: 1;
        }
        .detail-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 4px;
        }
        .detail-value {
            font-size: 14px;
            font-weight: 500;
            color: #111827;
            margin: 0;
        }
        .qr-section {
            text-align: center;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            margin-top: 16px;
        }
        .qr-code {
            width: 128px;
            height: 128px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            margin: 0 auto 8px;
        }
        .qr-text {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 16px;
        }
        .banner {
            background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 16px;
        }
        .footer {
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .footer p {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="pass-card">
            <!-- Gradient Header -->
            <div class="gradient-header">
                <div class="avatar-container">
                    @if($visitor->profile_photo_url)
                        <img src="{{ url($visitor->profile_photo_url) }}" alt="{{ $visitor->name }}">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background-color: rgba(255, 255, 255, 0.2); color: white; font-size: 36px; font-weight: bold;">
                            {{ strtoupper(substr($visitor->name, 0, 1)) }}
                        </div>
@endif
                </div>
                <h2 class="visitor-name">{{ $visitor->name }}</h2>
                <span class="status-badge">ACTIVE</span>
            </div>

            <!-- Details Section -->
            <div class="details-section">
                <!-- Email -->
                <div class="detail-row">
                    <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <div class="detail-content">
                        <div class="detail-label">Email</div>
                        <p class="detail-value">{{ $visitor->email }}</p>
                    </div>
                </div>

                <!-- Phone -->
                <div class="detail-row">
                    <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <div class="detail-content">
                        <div class="detail-label">Phone</div>
                        <p class="detail-value">{{ $visitor->contact ?? $visitor->phone ?? '—' }}</p>
                    </div>
                </div>

                <!-- Check In -->
                <div class="detail-row">
                    <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <div class="detail-content">
                        <div class="detail-label">Check In</div>
                        <p class="detail-value">{{ $visitor->time_in ? \Carbon\Carbon::parse($visitor->time_in)->format('M d, Y, h:i:s A') : ($visitor->registered_at ? \Carbon\Carbon::parse($visitor->registered_at)->format('M d, Y, h:i:s A') : '—') }}</p>
                    </div>
                </div>

                <!-- Expires At -->
                <div class="detail-row">
                    <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="detail-content">
                        <div class="detail-label">Expires At</div>
                        <p class="detail-value">{{ $visitor->pass_valid_until ? \Carbon\Carbon::parse($visitor->pass_valid_until)->format('M d, Y, h:i:s A') : '—' }}</p>
                    </div>
                </div>

                <!-- Pass Code -->
                <div class="detail-row">
                    <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    <div class="detail-content">
                        <div class="detail-label">Pass Code</div>
                        <p class="detail-value">{{ $accessCode ?? '—' }}</p>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="qr-section">
                    @if($qrCode)
                        <img src="{{ $qrCode }}" alt="QR Code" class="qr-code">
                        <p class="qr-text">Scan to verify</p>
@endif
                    <div class="banner">
                        Please present this pass at the reception
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Your Visit Has Been Approved ✅</strong></p>
            <p>Great news! Your visit has been approved and you are now checked in.</p>
            <p>We look forward to seeing you. Please present this email or your QR code at the entrance.</p>
            <p style="margin-top: 20px; color: #9ca3af; font-size: 12px;">
                Thanks,<br>
{{ config('app.name') }} Team
            </p>
        </div>
    </div>
</body>
</html>
