<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Facility Usage Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .report-list {
            background: white;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .report-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .report-item:last-child {
            border-bottom: none;
        }
        
        .report-name {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .report-status {
            background: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .summary {
            background: white;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .summary h3 {
            color: #2c3e50;
            margin-top: 0;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .stat-item {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        
        .button {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 5px;
        }
        
        .button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Monthly Facility Usage Reports</h1>
        <p>{{ $monthName }} {{ $year }}</p>
    </div>
    
    <div class="content">
        <div class="greeting">
            <p>Hello,</p>
            <p>Please find attached the monthly facility usage reports for <strong>{{ $monthName }} {{ $year }}</strong>. The reports have been automatically generated and include detailed analytics on facility utilization, reservation patterns, and revenue data.</p>
        </div>
        
        <div class="report-list">
            <h3 style="color: #2c3e50; margin-top: 0;">Generated Reports</h3>
            @foreach($reports as $report)
            <div class="report-item">
                <span class="report-name">{{ $report['facility_name'] }} Report</span>
                <span class="report-status">✓ Attached</span>
            </div>
            @endforeach
        </div>
        
        <div class="summary">
            <h3>Report Summary</h3>
            <p>The attached Excel files contain comprehensive data including:</p>
            <ul>
                <li><strong>Reservation Details:</strong> Complete list of all reservations with timing, purpose, and status</li>
                <li><strong>Facility Usage Analytics:</strong> Utilization rates and booking patterns by facility</li>
                <li><strong>Revenue Analysis:</strong> Payment data and revenue breakdown</li>
                <li><strong>Approval Statistics:</strong> Approval rates and processing times</li>
                <li><strong>Daily Usage Patterns:</strong> Day-by-day utilization trends</li>
            </ul>
            
            <div class="summary-stats">
                <div class="stat-item">
                    <div class="stat-value">{{ count($reports) }}</div>
                    <div class="stat-label">Reports Generated</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $monthName }}</div>
                    <div class="stat-label">Report Period</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $year }}</div>
                    <div class="stat-label">Year</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $generated_at }}</div>
                    <div class="stat-label">Generated</div>
                </div>
            </div>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <p><strong>Need Help?</strong></p>
            <p>If you have any questions about these reports or need assistance with the data, please contact the system administrator or refer to the user documentation.</p>
        </div>
    </div>
    
    <div class="footer">
        <p>This email was automatically generated by the Soliera Facility Management System.</p>
        <p>Generated on {{ $generated_at }}</p>
    </div>
</body>
</html>
