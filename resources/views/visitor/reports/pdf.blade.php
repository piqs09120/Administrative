<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Visitor Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .summary h2 {
            margin: 0 0 15px 0;
            font-size: 18px;
            color: #333;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .summary-table td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .visitor-details {
            margin-top: 30px;
        }
        .visitor-details h2 {
            margin: 0 0 15px 0;
            font-size: 18px;
            color: #333;
        }
        .visitor-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .visitor-table th,
        .visitor-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .visitor-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .visitor-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Visitor Report</h1>
        <p>Generated on: {{ $generated_at->format('F d, Y \a\t h:i A') }}</p>
        <p>Date Range: {{ $date_range['start']->format('F d, Y') }} - {{ $date_range['end']->format('F d, Y') }}</p>
    </div>

    @if($include_statistics)
    <div class="summary">
        <h2>Report Summary</h2>
        <table class="summary-table">
            <tr>
                <td>Total Visitors:</td>
                <td>{{ $statistics['total_visitors'] }}</td>
            </tr>
            <tr>
                <td>Currently In Building:</td>
                <td>{{ $statistics['currently_in'] }}</td>
            </tr>
            <tr>
                <td>Completed Visits:</td>
                <td>{{ $statistics['completed_visits'] }}</td>
            </tr>
            <tr>
                <td>Average Duration:</td>
                <td>{{ $statistics['average_duration'] }}</td>
            </tr>
        </table>
    </div>
    @endif

    @if($include_details && $visitors->count() > 0)
    <div class="visitor-details">
        <h2>Visitor Details</h2>
        <table class="visitor-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Purpose</th>
                    <th>Facility</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Duration</th>
                    <th>Host</th>
                </tr>
            </thead>
            <tbody>
                @foreach($visitors as $visitor)
                <tr>
                    <td>{{ $visitor->name }}</td>
                    <td>{{ $visitor->company ?? 'N/A' }}</td>
                    <td>{{ $visitor->purpose ?? 'N/A' }}</td>
                    <td>{{ $visitor->facility->name ?? 'N/A' }}</td>
                    <td>{{ $visitor->time_in ? \Carbon\Carbon::parse($visitor->time_in)->format('M d, Y h:i A') : 'N/A' }}</td>
                    <td>{{ $visitor->time_out ? \Carbon\Carbon::parse($visitor->time_out)->format('M d, Y h:i A') : 'Still in' }}</td>
                    <td>
                        @if($visitor->time_out)
                            {{ \Carbon\Carbon::parse($visitor->time_in)->diffForHumans(\Carbon\Carbon::parse($visitor->time_out), true) }}
                        @elseif($visitor->time_in)
                            {{ \Carbon\Carbon::parse($visitor->time_in)->diffForHumans(now(), true) }} (ongoing)
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $visitor->host_employee ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>This report was generated automatically by the Soliera Visitor Management System</p>
        <p>For questions or support, please contact the system administrator</p>
    </div>
</body>
</html>
