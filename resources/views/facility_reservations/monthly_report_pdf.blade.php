<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Facility Usage Report - {{ $monthName }} {{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin: 0;
        }
        
        .header h2 {
            color: #7f8c8d;
            font-size: 16px;
            margin: 5px 0 0 0;
            font-weight: normal;
        }
        
        .summary-section {
            margin-bottom: 30px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .summary-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
        }
        
        .summary-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #6c757d;
            text-transform: uppercase;
        }
        
        .summary-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
        }
        
        .summary-card .subtitle {
            font-size: 11px;
            color: #6c757d;
            margin: 5px 0 0 0;
        }
        
        .section-title {
            color: #2c3e50;
            font-size: 18px;
            font-weight: bold;
            margin: 25px 0 15px 0;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th {
            background: #2c3e50;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        
        .table td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
            font-size: 11px;
        }
        
        .table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-denied {
            background: #f8d7da;
            color: #721c24;
        }
        
        .facility-usage-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .facility-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
        }
        
        .facility-card h4 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 14px;
        }
        
        .facility-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        
        .facility-stat {
            text-align: center;
        }
        
        .facility-stat .label {
            font-size: 10px;
            color: #6c757d;
            text-transform: uppercase;
        }
        
        .facility-stat .value {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Monthly Facility Usage Report</h1>
        <h2>{{ $monthName }} {{ $year }}</h2>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-grid">
            <div class="summary-card">
                <h3>Total Reservations</h3>
                <p class="value">{{ $reportData['summary']['total_reservations'] }}</p>
            </div>
            <div class="summary-card">
                <h3>Approved</h3>
                <p class="value">{{ $reportData['summary']['approved_reservations'] }}</p>
                <p class="subtitle">{{ number_format($reportData['summary']['approval_rate'], 1) }}% approval rate</p>
            </div>
            <div class="summary-card">
                <h3>Total Hours</h3>
                <p class="value">{{ number_format($reportData['summary']['total_hours'], 1) }}</p>
                <p class="subtitle">{{ number_format($reportData['summary']['average_booking_duration'], 1) }}h average</p>
            </div>
            <div class="summary-card">
                <h3>Revenue</h3>
                <p class="value">${{ number_format($reportData['summary']['total_revenue'], 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Facility Usage Breakdown -->
    @if(count($reportData['facility_usage']) > 0)
    <div class="section-title">Facility Usage Breakdown</div>
    <div class="facility-usage-grid">
        @foreach($reportData['facility_usage'] as $facility)
        <div class="facility-card">
            <h4>{{ $facility['facility_name'] }}</h4>
            <div class="facility-stats">
                <div class="facility-stat">
                    <div class="label">Reservations</div>
                    <div class="value">{{ $facility['reservation_count'] }}</div>
                </div>
                <div class="facility-stat">
                    <div class="label">Total Hours</div>
                    <div class="value">{{ number_format($facility['total_hours'], 1) }}</div>
                </div>
                <div class="facility-stat">
                    <div class="label">Revenue</div>
                    <div class="value">${{ number_format($facility['revenue'], 2) }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Daily Usage Pattern -->
    @if(count($reportData['daily_usage']) > 0)
    <div class="section-title">Daily Usage Pattern</div>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Reservations</th>
                <th>Total Hours</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['daily_usage'] as $day)
            <tr>
                <td>{{ \Carbon\Carbon::parse($day['date'])->format('M j, Y') }}</td>
                <td>{{ $day['reservation_count'] }}</td>
                <td>{{ number_format($day['total_hours'], 1) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Detailed Reservations -->
    <div class="section-title">Detailed Reservations</div>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Facility</th>
                <th>Reserved By</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Duration</th>
                <th>Purpose</th>
                <th>Status</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['reservations'] as $reservation)
            <tr>
                <td>{{ $reservation['id'] }}</td>
                <td>{{ $reservation['facility_name'] }}</td>
                <td>{{ $reservation['reserved_by'] }}</td>
                <td>{{ $reservation['start_time'] }}</td>
                <td>{{ $reservation['end_time'] }}</td>
                <td>{{ $reservation['duration_hours'] }}h</td>
                <td>{{ \Illuminate\Support\Str::limit($reservation['purpose'], 30) }}</td>
                <td>
                    <span class="status-badge status-{{ $reservation['status'] }}">
                        {{ $reservation['status'] }}
                    </span>
                </td>
                <td>${{ number_format($reservation['payment_amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the Soliera Facility Management System.</p>
        <p>For questions or support, please contact the system administrator.</p>
    </div>
</body>
</html>
